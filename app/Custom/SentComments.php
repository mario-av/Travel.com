<?php

namespace App\Custom;

/**
 * SentComments - Manages temporary edit permissions for reviews.
 * Reviews can only be edited within 10 minutes of creation.
 */
class SentComments
{
    /**
     * Check if a review was sent recently (editable for 10 minutes).
     *
     * @param int $reviewId The review ID to check.
     * @return bool True if review is still editable.
     */
    public static function isComment(int $reviewId): bool
    {
        $comments = session('sent_comments', []);

        if (!isset($comments[$reviewId])) {
            return false;
        }

        $sentTime = $comments[$reviewId];
        $now = time();
        $tenMinutes = 10 * 60; // 10 minutes in seconds

        return ($now - $sentTime) <= $tenMinutes;
    }

    /**
     * Register a review as sent (starts the edit timer).
     *
     * @param int $reviewId The review ID to register.
     * @return void
     */
    public static function addComment(int $reviewId): void
    {
        $comments = session('sent_comments', []);
        $comments[$reviewId] = time();
        session(['sent_comments' => $comments]);
    }

    /**
     * Remove a review from the edit registry.
     *
     * @param int $reviewId The review ID to remove.
     * @return void
     */
    public static function removeComment(int $reviewId): void
    {
        $comments = session('sent_comments', []);
        unset($comments[$reviewId]);
        session(['sent_comments' => $comments]);
    }
}
