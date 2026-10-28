<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class GoogleMapsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $maps = Company::maps();
            
            $response = Http::withHeaders([
                'Access-Control-Allow-Origin' => '*'
            ])->get($maps['api_url'].'/json', [
                'query' => [
                    'key'    => $maps['api_key'],
                    'input'  => $request->input('query'),
                    'fields' => $maps['fields'],
                ],
            ]);

             // Get the data from the response
            $data = $response->json();

            return response(200)->json( array( 'places' => $data ) );

        } catch (\Exception $e) {
            // Handle the exception
            return response()->json(['error' => 'Unable to fetch data'], 500);            
        }
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
