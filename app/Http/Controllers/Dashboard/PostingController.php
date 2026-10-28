<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PlacementModel;
use App\Models\PostingModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This method displays a paginated list of postings (job postings)
     * in the dashboard. It uses the PostingModel to retrieve the postings
     * and orders them by the created_at date in descending order.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Retrieve the postings with their category and subcategory
        // and paginate them
        $postings = PostingModel::with(['categories','user'])
            ->withCount(['views as views_total','comments as comments_total'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $placements = PlacementModel::get();

        // Retrieve the status message from the session
        $status = session('status');

        // Return the postings and the status message as a JSON response
        return Inertia::render('Dashboard/Postings', compact('postings', 'status','placements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(PostingModel $posting)
    {
        // Load the related category, subcategory, and user for the posting
        $posting->load(['categories','user']);

        // Retrieve the status message from the session
        $status = session('status');

        // Return the postings and the status message as a JSON response
        return Inertia::render('Dashboard/Posting', compact('posting', 'status'));
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
