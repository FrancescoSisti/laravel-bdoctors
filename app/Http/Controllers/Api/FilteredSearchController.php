<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class FilteredSearchController extends Controller
{
    public function filter($id, $rating)
    {
        // $input_rating = $rating->query('input_rating');

        if ($id) {
            $query = User::select('users.*', 'profiles.*', 'specializations.*')
                ->join('specialization_user', 'users.id', '=', 'specialization_user.user_id')
                ->join('specializations', 'specializations.id', '=', 'specialization_user.specialization_id')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                ->leftJoin('reviews', 'profiles.id', '=', 'reviews.profile_id')
                ->where('specialization_user.specialization_id', '=', $id)
                ->groupBy('users.id', 'profiles.id', 'specializations.id');
        }

        $query->selectRaw('COALESCE(AVG(reviews.votes), 0) as media_voti');  // Media dei voti

        if ($rating) {
            $query->havingRaw('AVG(reviews.votes) >= ?', [$rating]);
        }

        $users = $query->get();

        return response()->json($users);
    }
}
