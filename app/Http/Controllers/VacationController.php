<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Photo;
use App\Models\Vacation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * VacationController - Resource controller for vacation CRUD operations.
 * Requires advanced or admin role for create/edit/delete operations.
 */
class VacationController extends Controller
{
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
                'error' => 'Error loading vacations: ' . $e->getMessage()
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
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'location' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'available_slots' => 'required|integer|min:0',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'featured' => 'boolean',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $validated['user_id'] = auth()->id();
            $validated['featured'] = $request->boolean('featured');
            $validated['active'] = true;

            $vacation = Vacation::create($validated);

            // Handle photo uploads
            if ($request->hasFile('photos')) {
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

            return redirect()
                ->route('vacation.show', $vacation)
                ->with('success', 'Vacation created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating vacation: ' . $e->getMessage());
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
        try {
            $vacation->load([
                'category',
                'user',
                'photos',
                'reviews' => fn($q) => $q->where('approved', true)->with('user'),
            ]);

            return view('vacation.show', compact('vacation'));
        } catch (\Exception $e) {
            return view('vacation.show', [
                'vacation' => null,
                'error' => 'Error loading vacation details.'
            ]);
        }
    }

    /**
     * Show the form for editing the specified vacation.
     *
     * @param Vacation $vacation The vacation to edit.
     * @return View The edit vacation form.
     */
    public function edit(Vacation $vacation): View
    {
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
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'location' => 'required|string|max:150',
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

            $vacation->update($validated);

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

            return redirect()
                ->route('vacation.show', $vacation)
                ->with('success', 'Vacation updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating vacation: ' . $e->getMessage());
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
        try {
            // Delete associated photos from storage
            foreach ($vacation->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }

            $vacation->delete();

            return redirect()
                ->route('vacation.index')
                ->with('success', 'Vacation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error deleting vacation: ' . $e->getMessage());
        }
    }
}
