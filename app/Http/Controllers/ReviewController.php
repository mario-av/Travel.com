<?php

namespace App\Http\Controllers;

use App\Custom\SentComments;
use App\Models\Review;
use App\Models\Vacation;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ReviewController - Handles vacation review operations.
 * Uses SentComments for 10-minute edit window.
 */
class ReviewController extends Controller
{
    /**
     * Constructor - Apply middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Check if user owns the review or is admin.
     *
     * @param Review $review The review to check.
     * @return bool
     */
    private function ownerControl(Review $review): bool
    {
        $user = Auth::user();
        return $user->id == $review->user_id || $user->rol == 'admin';
    }

    /**
     * Store a newly created review in storage.
     *
     * @param Request $request The incoming request with review data.
     * @return RedirectResponse Redirect to vacation page.
     */
    public function store(Request $request): RedirectResponse
    {
        $result = false;

        $validated = $request->validate([
            'vacation_id' => 'required|exists:vacations,id',
            'content' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $vacation = Vacation::find($validated['vacation_id']);

        // Check if user has booked this vacation
        if (!Auth::user()->hasBookedVacation($vacation->id)) {
            return back()->withErrors(['general' => 'You must have booked this vacation to leave a review.']);
        }

        // Check if user already reviewed this vacation
        $existingReview = Review::where('user_id', Auth::user()->id)
            ->where('vacation_id', $vacation->id)
            ->first();

        if ($existingReview) {
            return back()->withErrors(['general' => 'You have already reviewed this vacation.']);
        }

        $review = new Review();
        $review->user_id = Auth::user()->id;
        $review->vacation_id = $vacation->id;
        $review->content = $validated['content'];
        $review->rating = $validated['rating'];
        $review->approved = false;

        try {
            $result = $review->save();

            // Register for temporary edit
            SentComments::addComment($review->id);

            $message = 'Review submitted. It will appear after approval.';
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
     * Show the form for editing the specified review.
     *
     * @param Review $review The review to edit.
     * @return RedirectResponse|View The edit form or redirect.
     */
    public function edit(Review $review): RedirectResponse|View
    {
        if (!$this->ownerControl($review)) {
            return redirect()->route('main.index');
        }

        // Check edit time limit using SentComments
        if (!SentComments::isComment($review->id)) {
            return redirect()
                ->route('vacation.show', $review->vacation_id)
                ->withErrors(['general' => 'Edit time limit exceeded. Reviews can only be edited within 10 minutes.']);
        }

        return view('review.edit', compact('review'));
    }

    /**
     * Update the specified review.
     *
     * @param Request $request The incoming request with updated data.
     * @param Review $review The review to update.
     * @return RedirectResponse Redirect to vacation page.
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        if (!$this->ownerControl($review)) {
            return redirect()->route('main.index');
        }

        // Check edit time limit using SentComments
        if (!SentComments::isComment($review->id)) {
            return back()->withErrors(['general' => 'Edit time limit exceeded.']);
        }

        $result = false;

        $validated = $request->validate([
            'content' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        try {
            $result = $review->update([
                'content' => $validated['content'],
                'rating' => $validated['rating'],
                'approved' => false, // Re-require approval after edit
            ]);
            $message = 'Review updated successfully.';
        } catch (\Exception $e) {
            $message = 'An error occurred.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('vacation.show', $review->vacation_id)->with($messageArray);
        } else {
            return back()->withInput()->withErrors($messageArray);
        }
    }

    /**
     * Remove the specified review.
     *
     * @param Review $review The review to delete.
     * @return RedirectResponse Redirect to vacation page.
     */
    public function destroy(Review $review): RedirectResponse
    {
        if (!$this->ownerControl($review)) {
            return redirect()->route('main.index');
        }

        try {
            $vacationId = $review->vacation_id;
            $result = $review->delete();

            // Remove from edit registry
            SentComments::removeComment($review->id);

            $message = 'Review deleted successfully.';
        } catch (\Exception $e) {
            $result = false;
            $message = 'Could not delete the review.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return redirect()->route('vacation.show', $vacationId)->with($messageArray);
        } else {
            return back()->withErrors($messageArray);
        }
    }

    /**
     * Approve a review (admin only).
     *
     * @param Review $review The review to approve.
     * @return RedirectResponse Redirect back.
     */
    public function approve(Review $review): RedirectResponse
    {
        try {
            $result = $review->update(['approved' => true]);
            $message = 'Review approved successfully.';
        } catch (\Exception $e) {
            $result = false;
            $message = 'Error approving review.';
        }

        $messageArray = ['general' => $message];

        if ($result) {
            return back()->with($messageArray);
        } else {
            return back()->withErrors($messageArray);
        }
    }
}
