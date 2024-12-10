<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class IndexReviewController extends Controller
{
    public function index(){

        $reviews = Review::with(['profiles'])->get();
        //dd($reviews);
        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }

}
