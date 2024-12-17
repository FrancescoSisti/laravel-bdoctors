<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\Request;

class FilteredSearchController extends Controller
{
    public function filter($id, $rating, $reviews)
    {
        // $input_rating = $rating->query('input_rating');

        if ($id) {
            $query = User::select('users.*', 'profiles.*', 'specializations.id as specializations_id', 'specializations.name as specializations_name',)
                ->join('specialization_user', 'users.id', '=', 'specialization_user.user_id')
                ->join('specializations', 'specializations.id', '=', 'specialization_user.specialization_id')
                ->join('profiles', 'users.id', '=', 'profiles.user_id')
                // sponsorship
                // ->join('profile_sponsorship', 'profiles.id', '=', 'profile_sponsorship.profile_id')
                // ->join('sponsorships', 'sponsorships.id', '=', 'profile_sponsorship.sponsorship_id')
                ->leftJoin('reviews', 'profiles.id', '=', 'reviews.profile_id')
                ->where('specialization_user.specialization_id', '=', $id)
                ->groupBy('users.id', 'profiles.id', 'specializations.id');
        }

        $query->selectRaw(
            'ceil(avg(reviews.votes)) as media_voti,
        COALESCE(count(reviews.id), 0) as totalReviews'
        );

        if ($rating !== null) {
            $query->havingRaw('COALESCE(AVG(reviews.votes), 0) >= ?', [$rating]);
        }

        if ($reviews !== null) {
            $query->havingRaw('COALESCE(COUNT(reviews.id), 0) >= ?', [$reviews]);
        }


        $users = $query->get();

        return response()->json($users);
    }
}
