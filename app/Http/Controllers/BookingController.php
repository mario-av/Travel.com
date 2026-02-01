<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vacation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * BookingController - Handles vacation booking operations.
 * Users can create and cancel their own bookings.
 */
class BookingController extends Controller
{
    /**
     * Display a listing of the user's bookings.
     *
     * @return View The bookings list view.
     */
    public function index(): View
    {
        try {
            $bookings = auth()->user()
                ->bookings()
                ->with(['vacation.photos' => fn($q) => $q->where('is_main', true)])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('booking.index', compact('bookings'));
        } catch (\Exception $e) {
            return view('booking.index', [
                'bookings' => collect(),
                'error' => 'Error loading bookings.'
            ]);
        }
    }

    /**
     * Store a newly created booking in storage.
     *
     * @param Request $request The incoming request with booking data.
     * @param Vacation $vacation The vacation to book.
     * @return RedirectResponse Redirect to bookings page.
     */
    public function store(Request $request, Vacation $vacation): RedirectResponse
    {
        $validated = $request->validate([
            'num_guests' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Check availability
            if ($vacation->available_slots < $validated['num_guests']) {
                return redirect()
                    ->back()
                    ->with('error', 'Not enough available slots for this booking.');
            }

            // Calculate total price
            $totalPrice = $vacation->price * $validated['num_guests'];

            // Create booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'vacation_id' => $vacation->id,
                'num_guests' => $validated['num_guests'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);

            // Update available slots
            $vacation->decrement('available_slots', $validated['num_guests']);

            return redirect()
                ->route('booking.index')
                ->with('success', 'Booking created successfully. Please wait for confirmation.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified booking.
     *
     * @param Booking $booking The booking to display.
     * @return View The booking detail view.
     */
    public function show(Booking $booking): View
    {
        // Ensure user owns this booking
        if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        $booking->load(['vacation.photos', 'user']);

        return view('booking.show', compact('booking'));
    }

    /**
     * Cancel the specified booking.
     *
     * @param Booking $booking The booking to cancel.
     * @return RedirectResponse Redirect to bookings page.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        // Ensure user owns this booking
        if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        try {
            if ($booking->status === 'cancelled') {
                return redirect()
                    ->back()
                    ->with('error', 'This booking is already cancelled.');
            }

            // Restore available slots
            $booking->vacation->increment('available_slots', $booking->num_guests);

            $booking->update(['status' => 'cancelled']);

            return redirect()
                ->route('booking.index')
                ->with('success', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error cancelling booking: ' . $e->getMessage());
        }
    }

    /**
     * Confirm a booking (admin only).
     *
     * @param Booking $booking The booking to confirm.
     * @return RedirectResponse Redirect back.
     */
    public function confirm(Booking $booking): RedirectResponse
    {
        try {
            $booking->update(['status' => 'confirmed']);

            return redirect()
                ->back()
                ->with('success', 'Booking confirmed successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error confirming booking: ' . $e->getMessage());
        }
    }
}
