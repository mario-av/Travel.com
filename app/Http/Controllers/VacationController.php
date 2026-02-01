<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdvancedMiddleware;
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
            $vacations = Vacation::with(['category', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

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
    public function store(Request $request): RedirectResponse
    {
        $result = false;

        $validated = $request->validate([
            'title' => 'required|string|min:4|max:200',
            'description' => 'required|string|min:10',
            'location' => 'required|string|min:2|max:150',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'available_slots' => 'required|integer|min:0',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'featured' => 'boolean',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $vacation = new Vacation($validated);
        $vacation->user_id = Auth::user()->id;
        $vacation->featured = $request->boolean('featured');
        $vacation->active = true;

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

            $message = 'Vacation has been created.';
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
            'reviews' => fn($q) => $q->where('approved', true)->with('user'),
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
            return redirect()->route('landing');
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
    public function update(Request $request, Vacation $vacation): RedirectResponse
    {
        if (!$this->ownerControl($vacation)) {
            return redirect()->route('landing');
        }

        $result = false;

        $validated = $request->validate([
            'title' => 'required|string|min:4|max:200',
            'description' => 'required|string|min:10',
            'location' => 'required|string|min:2|max:150',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'available_slots' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'featured' => 'boolean',
            'active' => 'boolean',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $validated['featured'] = $request->boolean('featured');
            $validated['active'] = $request->boolean('active', true);

            $result = $vacation->update($validated);

            // Handle new photo uploads
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
            return redirect()->route('vacation.edit', $vacation->id)->with($messageArray);
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
            return redirect()->route('landing');
        }

        try {
            // Delete associated photos from storage
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
            return redirect()->route('admin.vacation.index')->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }
}
