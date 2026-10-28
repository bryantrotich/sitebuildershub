<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\CategoryRequest;
use App\Models\CategoryModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CategoryModel::withCount(['subCategories','childSubCategories'])->paginate(10);

        return Inertia::render('Dashboard/Category',[
            'status'     => session('status'),
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        // Retrieve the categories with their subcategories count
        // and paginate them
        $categories = CategoryModel::withCount(['subCategories'])->paginate(10);

        // Return the categories as a JSON response
        return back()->with(compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        // Validate the request
        $validated = $request->validated();

        // Create the category with UUID
        CategoryModel::create($validated);

        return back()->with('status', 'Category created successfully');
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
    public function destroy(CategoryModel $category)
    {
        // Delete the category
        $category->delete();

        return back()->with('message', 'Category deleted successfully');
    }
}
