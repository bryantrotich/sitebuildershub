<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\PlacementModel;
use App\Models\PostingModel;
use App\Models\PostingViewModel;
use App\Models\PromotionModel;
use App\Models\SubCategoryModel;
use App\Models\SubscriptionModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories     = SubCategoryModel::with(['category'])->withCount(['postings'])->get();
        $subscriptions  = SubscriptionModel::get();
        $postings       = PostingModel::get()->sortBy('views_count');
        $placements     = PlacementModel::whereIn('section',['top-banner'])->filterPromotion()->get();
        $count_postings = PostingModel::count();

        // $poromotions = PromotionModel::active()->whereBetween()
        $session     = session('status');

        return Inertia::render('Landing/Home',compact('categories','count_postings','postings','session','subscriptions','placements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        $placements  = PlacementModel::whereIn('section',['leader-banner'])->filterPromotion()->get();

        return response()->json(compact('placements'),200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function footer()
    {
        //

        $placements  = PlacementModel::whereIn('section',['footer'])->filterPromotion()->get();

        return response()->json(compact('placements'),200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function security()
    {
        //

        $placements  = PlacementModel::whereIn('section',['signup','login'])->filterPromotion()->get();

        return response()->json(compact('placements'),200);

    }    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
