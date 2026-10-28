<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use App\Models\PlacementModel;
use App\Models\PostingModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $postings    = PostingModel::with(['categories']);
        $categories  = CategoryModel::with(['subCategories','childSubCategories'])->get();
        $placements  = PlacementModel::whereIn('section',['advert'])->filterPromotion()->get();
        $queryParams = $request->query();
        $locations   = config('location');
        $status      = session('status');

        if( empty($queryParams) ){
            $postings   = $postings->orderBy('created_at', 'desc')->paginate(10);
        }

        if( !empty($queryParams) ){

            if( !empty($queryParams['categories']) ){
                $postings = $postings->categories(explode(',',$queryParams['categories']));
            }
            
            if( !empty($queryParams['cities']) ){
                $postings = $postings->cities(explode(',',$queryParams['cities']));
            }
            
            if( !empty($queryParams['price_range']) ){
                $postings = $postings->range(explode(',',$queryParams['price_range']));
            }

            if( !empty($queryParams['name']) ){
                $postings = $postings->title($queryParams['name']);
            }
            
            $postings   = $postings->orderBy('created_at', 'desc')->paginate(10);
            
        }

        return Inertia::render('Landing/Postings',compact('categories','locations','placements','postings','status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

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
    public function show(Request $request)
    {
        $title   = $request->query('title');
        $posting = PostingModel::with(['categories','comments.user'])->where('title', $title)->first();
        $visited = $posting->views()->where('ip',$request->ip())->whereDate('created_at', '=',now()->format('Y-m-d'))->first();


        if( empty($visited) ){
            $posting->views()->create([ 'ip' => $request->getClientIp() ]);
        }

        return Inertia::render('Landing/ViewPosting',compact('posting'));
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
