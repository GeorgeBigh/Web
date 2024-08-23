<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        try {
            // Check if the user has already rated this lecturer
            $existingRating = Rating::where('user_id', $request->input('user_id'))
                                    ->first();

            if ($existingRating) {
                // If rating exists, update it
                $existingRating->rating = $request->input('rating');
                $existingRating->save();
            } else {
                // If no rating exists, create a new one
                $rating = new Rating();
                $rating->user_id = $request->input('user_id');
                $rating->rating = $request->input('rating');
                $rating->save();
            }

            return response()->json(['success' => true, 'message' => 'Rating saved successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while saving the rating.'], 500);
        }
    }
}
