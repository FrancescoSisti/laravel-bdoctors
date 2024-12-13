<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class FilteredSearchController extends Controller
{
    public function filter(Request $request)
    {
        $specialization_id = $request->query('specialization_id');
        $input_rating = $request->query('input_rating');

        $query = User::select('users,*')
            ->join('specialization_user', 'users.id', '=', 'specialization_user.user_id')
            ->join('specializations', 'specialization.id', '=', 'specialization_user.specialization_id')
            ->where('users.specialization_id', '=', $specialization_id)
            ->groupBy('specializations.id');

        // if ($specialization_id) {
        //     $query->havingRaw('specialization_id', [$specialization_id]);
        // }

        // $query = Profile::select('profiles.*')
        //     ->join('reviews', 'profiles.id', '=', 'reviews.profile_id')
        //     ->selectRaw('AVG(reviews.votes) as average_votes')
        //     ->groupBy('profiles.id')
        //     ->orderBy('profile_id');


        // if ($input_rating) {
        //     $query->havingRaw('AVG(reviews.votes) >= ?',  [$input_rating]);
        // }

        $profiles = $query->get();


        return response()->json([
            'success' => true,
            'profiles' => $profiles
        ]);
    }
}
