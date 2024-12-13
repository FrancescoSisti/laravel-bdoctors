<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Review;
use Illuminate\Http\Request;

class FilteredSearchController extends Controller
{
    public function filter(Request $request)
    {

        $min_average_votes = $request->query('min_average_votes');

        $query = Profile::select('profiles.*')
            ->leftJoin('reviews', 'profiles.id', '=', 'reviews.profile_id')
            ->selectRaw('AVG(reviews.votes) as average_votes')
            ->groupBy('profiles.id');

        if ($min_average_votes) {
            $query->havingRaw('AVG(reviews.votes) >= 0', [$min_average_votes]);
        }

        $profiles = $query->get();


        return response()->json([
            'success' => true,
            'profiles' => $profiles
        ]);
    }
}
