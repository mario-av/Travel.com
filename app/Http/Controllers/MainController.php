<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MainController - Handles the public landing page.
 * Provides search, filter, and sorting functionality for vacations.
 */
class MainController extends Controller
{
    /**
     * Display the landing page with vacation listings.
     *
     * @param Request $request The incoming request with optional filters.
     * @return View The vacation index view.
     */
    public function index(Request $request): View
    {
        try {
            $query = Vacation::query()
                ->where('active', true)
                ->with(['category', 'photos' => function ($q) {
                    $q->where('is_main', true);
                }]);

            // Search by title or location
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->filled('category')) {
                $query->where('category_id', $request->input('category'));
            }

            // Filter by price range
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->input('min_price'));
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->input('max_price'));
            }

            // Filter featured only
            if ($request->boolean('featured')) {
                $query->where('featured', true);
            }

            // Sorting
            $sortField = $request->input('sort', 'created_at');
            $sortDirection = $request->input('direction', 'desc');
            $allowedSorts = ['price', 'title', 'created_at', 'start_date'];

            if (in_array($sortField, $allowedSorts)) {
                $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
            }

            // Paginate results
            $vacations = $query->paginate(12)->withQueryString();
            $categories = Category::all();
            $featuredVacations = Vacation::where('active', true)
                ->where('featured', true)
                ->with(['photos' => fn($q) => $q->where('is_main', true)])
                ->take(4)
                ->get();

            return view('vacation.index', compact('vacations', 'categories', 'featuredVacations'));
        } catch (\Exception $e) {
            return view('vacation.index', [
                'vacations' => collect(),
                'categories' => collect(),
                'featuredVacations' => collect(),
                'error' => 'An error occurred while loading vacations. Please try again later.'
            ]);
        }
    }
}
