<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Vacation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * ReviewController - Handles vacation review operations.
 * Includes edit time limit functionality.
 */
class ReviewController extends Controller
{
    /**
     * Edit time limit in minutes for reviews.
     *
     * @var int
     */
    private const EDIT_TIME_LIMIT = 30;

    /**
     * Store a newly created review in storage.
     *
     * @param Request $request The incoming request with review data.
     * @param Vacation $vacation The vacation to review.
     * @return RedirectResponse Redirect to vacation page.
     */
    public function store(Request $request, Vacation $vacation): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        try {
            // Check if user already reviewed this vacation
            $existingReview = Review::where('user_id', auth()->id())
                ->where('vacation_id', $vacation->id)
                ->first();

            if ($existingReview) {
                return redirect()
                    ->back()
                    ->with('error', 'You have already reviewed this vacation.');
            }

            Review::create([
                'user_id' => auth()->id(),
                'vacation_id' => $vacation->id,
                'content' => $validated['content'],
                'rating' => $validated['rating'],
                'approved' => false,
            ]);

            return redirect()
                ->route('vacation.show', $vacation)
                ->with('success', 'Review submitted successfully. It will appear after approval.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error submitting review: ' . $e->getMessage());
        }
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
        // Ensure user owns this review
        if ($review->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this review.');
        }

        // Check edit time limit
        if (!$this->canEdit($review)) {
            return redirect()
                ->back()
                ->with('error', 'Edit time limit exceeded. Reviews can only be edited within ' .
                    self::EDIT_TIME_LIMIT . ' minutes of creation.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        try {
            $review->update([
                'content' => $validated['content'],
                'rating' => $validated['rating'],
                'approved' => false, // Re-require approval after edit
            ]);

            return redirect()
                ->route('vacation.show', $review->vacation)
                ->with('success', 'Review updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error updating review: ' . $e->getMessage());
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
        // Ensure user owns this review or is admin
        if ($review->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to this review.');
        }

        try {
            $vacationId = $review->vacation_id;
            $review->delete();

            return redirect()
                ->route('vacation.show', $vacationId)
                ->with('success', 'Review deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error deleting review: ' . $e->getMessage());
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
            $review->update(['approved' => true]);

            return redirect()
                ->back()
                ->with('success', 'Review approved successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error approving review: ' . $e->getMessage());
        }
    }

    /**
     * Check if a review can still be edited.
     *
     * @param Review $review The review to check.
     * @return bool True if within edit time limit.
     */
    private function canEdit(Review $review): bool
    {
        $createdAt = $review->created_at;
        $minutesSinceCreation = now()->diffInMinutes($createdAt);

        return $minutesSinceCreation <= self::EDIT_TIME_LIMIT;
    }
}
