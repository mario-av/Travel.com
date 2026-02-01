<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminMiddleware;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CategoryController - CRUD operations for vacation categories.
 * Requires admin role.
 */
class CategoryController extends Controller
{
    /**
     * Constructor - Apply middleware.
     */
    public function __construct()
    {
        $this->middleware(AdminMiddleware::class);
    }

    /**
     * Display a listing of categories.
     *
     * @return View The categories list view.
     */
    public function index(): View
    {
        $categories = Category::withCount('vacations')->get();
        return view('category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return View The create category form.
     */
    public function create(): View
    {
        return view('category.create');
    }

    /**
     * Store a newly created category in storage.
     *
     * @param Request $request The incoming request with category data.
     * @return RedirectResponse Redirect to category index.
     */
    public function store(Request $request): RedirectResponse
    {
        $result = false;

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        $category = new Category($validated);

        try {
            $result = $category->save();
            $message = 'Category has been created.';
        } catch (UniqueConstraintViolationException $e) {
            $message = 'A category with this name already exists.';
        } catch (QueryException $e) {
            $message = 'Database error occurred.';
        } catch (\Exception $e) {
            $message = 'An error occurred.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('category.index')->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }

    /**
     * Display the specified category.
     *
     * @param Category $category The category to display.
     * @return View The category detail view.
     */
    public function show(Category $category): View
    {
        $category->load(['vacations' => fn($q) => $q->where('active', true)->paginate(12)]);
        return view('category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param Category $category The category to edit.
     * @return View The edit category form.
     */
    public function edit(Category $category): View
    {
        return view('category.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param Request $request The incoming request with updated data.
     * @param Category $category The category to update.
     * @return RedirectResponse Redirect to category edit page.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $result = false;

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $result = $category->update($validated);
            $message = 'Category has been updated.';
        } catch (UniqueConstraintViolationException $e) {
            $message = 'A category with this name already exists.';
        } catch (\Exception $e) {
            $message = 'An error occurred.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('category.edit', $category->id)->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param Category $category The category to delete.
     * @return RedirectResponse Redirect to category index.
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            $result = $category->delete();
            $message = 'Category has been deleted.';
        } catch (\Exception $e) {
            $result = false;
            $message = 'Could not delete the category.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('category.index')->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }
}
