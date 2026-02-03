<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdvancedMiddleware;
use App\Http\Requests\VacationCreateRequest;
use App\Http\Requests\VacationEditRequest;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Vacation;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * VacationController - Resource controller for vacation CRUD operations.
 * Requires advanced or admin role for create/edit/delete operations.
 */
class VacationController extends Controller
{
    /**
     * Constructor - Apply middleware.
     */
    public function __construct()
    {
        $this->middleware('verified')->except(['index', 'show']);
        $this->middleware(AdvancedMiddleware::class)->except(['index', 'show']);
    }

    /**
     * Check if user owns the vacation or is admin.
     *
     * @param Vacation $vacation The vacation to check.
     * @return bool
     */
    private function ownerControl(Vacation $vacation): bool
    {
        $user = Auth::user();
        return $user->id == $vacation->user_id || $user->rol == 'admin';
    }

    /**
     * Display a listing of vacations for admin management.
     *
     * @return View The vacation management view.
     */
    public function index(): View
    {
        try {
            $query = Vacation::with(['category', 'user'])->orderBy('created_at', 'desc');

            // Admin sees all, Advanced/Verified sees only their own
            if (!Auth::user()->isAdmin()) {
                $query->where('user_id', Auth::id());
            }

            $vacations = $query->paginate(15);

            return view('vacation.manage', compact('vacations'));
        } catch (\Exception $e) {
            return view('vacation.manage', [
                'vacations' => collect(),
                'error' => 'Error loading vacations.'
            ]);
        }
    }

    /**
     * Show the form for creating a new vacation.
     *
     * @return View The create vacation form.
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('vacation.create', compact('categories'));
    }

    /**
     * Store a newly created vacation in storage.
     *
     * @param Request $request The incoming request with vacation data.
     * @return RedirectResponse Redirect to vacation show page.
     */
    /**
     * Store a newly created vacation in storage.
     *
     * @param VacationCreateRequest $request The incoming request with vacation data.
     * @return RedirectResponse Redirect to vacation show page.
     */
    public function store(VacationCreateRequest $request): RedirectResponse
    {
        $result = false;
        $vacation = new Vacation($request->validated());
        $vacation->user_id = Auth::user()->id;
        $vacation->featured = $request->boolean('featured');
        $vacation->active = $request->boolean('active', true);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Approval logic: Admin is auto-approved, Advanced requires approval
        if ($user->isAdmin()) {
            $vacation->approved = true;
        } else {
            $vacation->approved = false;
        }

        try {
            $result = $vacation->save();

            // Handle photo uploads
            if ($result && $request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $path = $photo->store('vacations', 'public');
                    Photo::create([
                        'vacation_id' => $vacation->id,
                        'path' => $path,
                        'original_name' => $photo->getClientOriginalName(),
                        'is_main' => $index === 0,
                    ]);
                }
            }

            if ($vacation->approved) {
                $message = 'Vacation has been created.';
            } else {
                $message = 'Vacation created and pending approval.';
            }
        } catch (UniqueConstraintViolationException $e) {
            $message = 'A vacation with these details already exists.';
        } catch (QueryException $e) {
            $message = 'Database error occurred.';
        } catch (\Exception $e) {
            $message = 'An error occurred.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('vacation.show', $vacation->id)->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }

    /**
     * Display the specified vacation.
     *
     * @param Vacation $vacation The vacation to display.
     * @return View The vacation detail view.
     */
    public function show(Vacation $vacation): View
    {
        $vacation->load([
            'category',
            'user',
            'photos',
            'reviews' => function ($q) {
                $q->where('approved', true);
                if (Auth::check()) {
                    $q->orWhere('user_id', Auth::id());
                }
                $q->with('user');
            },
        ]);

        return view('vacation.show', compact('vacation'));
    }

    /**
     * Show the form for editing the specified vacation.
     *
     * @param Vacation $vacation The vacation to edit.
     * @return RedirectResponse|View The edit vacation form or redirect.
     */
    public function edit(Vacation $vacation): RedirectResponse|View
    {
        if (!$this->ownerControl($vacation)) {
            return redirect()->route('main.index');
        }

        $categories = Category::all();
        $vacation->load('photos');
        return view('vacation.edit', compact('vacation', 'categories'));
    }

    /**
     * Update the specified vacation in storage.
     *
     * @param Request $request The incoming request with updated data.
     * @param Vacation $vacation The vacation to update.
     * @return RedirectResponse Redirect to vacation show page.
     */
    public function update(VacationEditRequest $request, Vacation $vacation): RedirectResponse
    {
        if (!$this->ownerControl($vacation)) {
            return redirect()->route('main.index');
        }

        $result = false;

        try {
            $validated = $request->validated();
            $validated['featured'] = $request->boolean('featured');
            $validated['active'] = $request->boolean('active', true);

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Re-require approval if edited by non-admin
            if (!$user->isAdmin()) {
                $validated['approved'] = false;
            }

            $result = $vacation->update($validated);


            if ($request->has('delete_photos')) {
                foreach ($request->input('delete_photos') as $photoId) {
                    $photo = Photo::find($photoId);
                    if ($photo && $photo->vacation_id === $vacation->id) {

                        Storage::disk('public')->delete($photo->path);

                        $photo->delete();
                    }
                }
            }


            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('vacations', 'public');
                    Photo::create([
                        'vacation_id' => $vacation->id,
                        'path' => $path,
                        'original_name' => $photo->getClientOriginalName(),
                        'is_main' => false,
                    ]);
                }
            }

            $message = 'Vacation has been updated.';
        } catch (UniqueConstraintViolationException $e) {
            $message = 'A vacation with these details already exists.';
        } catch (\Exception $e) {
            $message = 'An error occurred.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('vacation.index')->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }

    /**
     * Remove the specified vacation from storage.
     *
     * @param Vacation $vacation The vacation to delete.
     * @return RedirectResponse Redirect to vacation index.
     */
    public function destroy(Vacation $vacation): RedirectResponse
    {
        if (!$this->ownerControl($vacation)) {
            return redirect()->route('main.index');
        }

        try {

            foreach ($vacation->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }

            $result = $vacation->delete();
            $message = 'Vacation has been deleted.';
        } catch (\Exception $e) {
            $result = false;
            $message = 'Could not delete the vacation.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('vacation.index')->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }

    /**
     * Delete multiple vacations at once.
     *
     * @param Request $request The request with IDs to delete.
     * @return RedirectResponse Redirect to vacation index.
     */
    public function deleteGroup(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->withErrors(['general' => 'No se seleccionó ningún elemento.']);
        }

        try {

            $vacations = Vacation::whereIn('id', $ids)->with('photos')->get();
            foreach ($vacations as $vacation) {
                foreach ($vacation->photos as $photo) {
                    Storage::disk('public')->delete($photo->path);
                }
            }

            $count = Vacation::whereIn('id', $ids)->delete();
            $message = "Se han eliminado $count vacaciones.";
        } catch (\Exception $e) {
            $message = 'Error al eliminar los elementos.';
            return back()->withErrors(['general' => $message]);
        }

        return redirect()->route('vacation.index')->with(['general' => $message]);
    }
}
