<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function saveReview(Request $request)
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|numeric|between:0,5',
                'review' => 'nullable|string|max:255',
                'teacher_id' => 'required|exists:teachers,id'
            ]);

            if ($this->hasExistingReview($validated['teacher_id'])) {
                return response()->json([
                    'message' => 'You have already reviewed this teacher'
                ], 422);
            }

            $review = Review::create([
                'user_id' => auth()->id(),
                'teacher_id' => $validated['teacher_id'],
                'rating' => $validated['rating'],
                'review' => $validated['review'] ?? null,
            ]);

            return response()->json([
                'message' => 'Review saved successfully',
                'review' => $review
            ], 201);

        } catch (\Exception $e) {
            Log::error('Review creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to save review'], 500);
        }
    }

    public function getTeacherReviews($teacherId)
    {
        try {
            $reviews = Review::where('teacher_id', $teacherId)
                           ->with('user:id,name')
                           ->latest()
                           ->get();

            return response()->json([
                'reviews' => $reviews->take(2),
                'averageRating' => $reviews->avg('rating') ?? 0,
                'totalReviews' => $reviews->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch reviews', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch reviews'], 500);
        }
    }

    private function hasExistingReview($teacherId): bool
    {
        return Review::where('user_id', auth()->id())
                    ->where('teacher_id', $teacherId)
                    ->exists();
    }
}
