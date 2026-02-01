<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * HomeController - Handles user dashboard and profile operations.
 * Provides user's personal dashboard with bookings and profile management.
 */
class HomeController extends Controller
{
    /**
     * Display the user's dashboard.
     *
     * @return View The dashboard view.
     */
    public function index(): View
    {
        try {
            $user = auth()->user();

            $recentBookings = $user->bookings()
                ->with(['vacation.photos' => fn($q) => $q->where('is_main', true)])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $pendingBookings = $user->bookings()
                ->where('status', 'pending')
                ->count();

            $confirmedBookings = $user->bookings()
                ->where('status', 'confirmed')
                ->count();

            $totalReviews = $user->reviews()->count();

            return view('auth.home', compact(
                'user',
                'recentBookings',
                'pendingBookings',
                'confirmedBookings',
                'totalReviews'
            ));
        } catch (\Exception $e) {
            return view('auth.home', [
                'user' => auth()->user(),
                'recentBookings' => collect(),
                'pendingBookings' => 0,
                'confirmedBookings' => 0,
                'totalReviews' => 0,
                'error' => 'Error loading dashboard data.'
            ]);
        }
    }

    /**
     * Show the profile edit form.
     *
     * @return View The profile edit view.
     */
    public function edit(): View
    {
        return view('auth.edit', ['user' => auth()->user()]);
    }

    /**
     * Update the user's profile.
     *
     * @param Request $request The incoming request with profile data.
     * @return RedirectResponse Redirect to dashboard.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            // Verify current password if changing password
            if (!empty($validated['password'])) {
                if (!Hash::check($validated['current_password'], $user->password)) {
                    return redirect()
                        ->back()
                        ->with('error', 'Current password is incorrect.');
                }
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            unset($validated['current_password']);

            $user->update($validated);

            return redirect()
                ->route('home')
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }
}
