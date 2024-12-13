<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class FilteredSearchController extends Controller
{
        public function filter() {

            $reviews = Review::with(['profiles'])->get();
            //dd($reviews);
            return response()->json([
                'success' => true,
                'reviews' => $reviews
        ]);
        }
}
