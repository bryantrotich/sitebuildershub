<?php

namespace App\Http\Controllers\Landing;

use App\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\BusinessProfileRequest;
use App\Http\Requests\Landing\CreateProjectRequest;
use App\Http\Requests\Landing\CreateStoreRequest;
use App\Http\Requests\Landing\ProfileFileUploadRequest;
use App\Http\Requests\Landing\StorePersonalProfile;
use App\Models\BusinessProfileModel;
use App\Models\BusinessStoreModel;
use App\Models\ProjectModel;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Intervention\Image\Laravel\Facades\Image;  // facade
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Landing/Profile',[
            'status' => session('status')
        ]);
    }  

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $tab)
    {
        //
        $data = [
            'tab'       => $tab,
            'status'    => session('status'),
            'locations' => config('location'),
        ];

        $user           = auth('client')->user();
        $subscription   = $user->activeSubscription;

        if( !$subscription->features->for_professionals && $tab == 'projects'){
            return redirect(route('landing.subscription'));
        }

        switch($tab){
            case 'personal':
                $data['user']     = $user;
            break;
            case 'projects':
                $data['projects'] = $user->projects()->paginate(2);
            break;   
            case 'invoices':
                $data['invoices'] = $user->invoices()->paginate(10);
            break; 
            case 'subscription':
                $data['subscription'] = $user->subscription;
                $data['active_subscription'] = $user->active_subscription;
            break;                                    
            case 'business':
                $data['maps']              = Company::maps();
                $data['business_profile']  = $user->business_profile;
                $data['stores']            = !empty($data['business_profile']) ? $data['business_profile']->stores : array();
            break;
        }

        return Inertia::render('Landing/Profile',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function personal(StorePersonalProfile $request)
    {
        //

        $validated = $request->validated();
        
        $user = auth()->user();

        User::find($user->id)->update($validated);

        return back()->with([
            'message' => 'Profile Updated Successfully'
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function business(BusinessProfileRequest $request)
    {
        //
        $validated = $request->validated();

        $user = auth('client')->user();

        if( empty($user->business_profile) ){
            BusinessProfileModel::create(
                array_merge($validated, [
                    'user_id' => $user->id
                ])
            );
        }
        
        if( !empty($user->business_profile) ){
            $user->business_profile->update($validated);
        }

        return back()->with([
            'message' => 'Business Profile Updated Successfully'
        ]);
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function business_store(CreateStoreRequest $request)
    {
        //
        $validated = $request->validated();

        $user = auth('client')->user();

        $validated['coords']              = json_encode($validated['coords']);
        $validated['open_from']           = json_encode($validated['open_from']);
        $validated['open_to']             = json_encode($validated['open_to']);
        $validated['working_days']        = json_encode($validated['working_days']);
        $validated['business_profile_id'] = $user->business_profile->id;
        
        BusinessStoreModel::create($validated);

        return back()->with([
            'message' => 'Business Store Created Successfully'
        ]);
    }

        /**
     * Store a newly created resource in storage.
     */
    public function project_store(CreateProjectRequest $request)
    {
        //
        $validated = $request->validated();
        $user       = auth('client')->user();

        $validated['user_id'] = $user->id;
        $validated['files']   = json_encode($validated['files']);
        
        ProjectModel::create($validated);

        return back()->with([
            'message' => 'Project has been added'
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function project_delete(ProjectModel $project)
    {
        //
        $project->delete();

        return back()->with([
            'message' => 'Project has been deleted'
        ]);
    }

        
    /**
     * Display the specified resource.
     */
    public function personal_store_image(ProfileFileUploadRequest $request)
    {
        //
        $uploaded = $request->file('file');
        $name     = Str::uuid().'.'.$uploaded->getClientOriginalExtension();

        $image = Image::read($uploaded);

        $width = $image->width();
        $height = $image->height();

        // Create an empty canvas with a transparent background
        $canvas = Image::canvas($width, $height);

        // Draw a black circle on the canvas
        $canvas->circle($width, $width / 2, $height / 2, function ($draw) {
            $draw->background('#000000');
        });        

        $cropped = $image->cover(1200,720,50,50);

        $cropped->save(storage_path('app/public/images/'.$name));

        return response()->json(array('name' => $name ));
    } 


    
    /**
     * Display the specified resource.
     */
    public function project_store_file(ProfileFileUploadRequest $request)
    {
        //
        $uploaded = $request->file('file');
        $name     = Str::uuid().'.'.$uploaded->getClientOriginalExtension();

        $image = Image::read($uploaded);
        $cropped = $image->cover(1024,720,50,50);

        $cropped->save(storage_path('app/public/images/'.$name));

        return response()->json(array('name' => $name ));
    } 


    /**
     * Display the specified resource.
     */
    public function forbidden()
    {
        return Inertia::render('Landing/Forbidden');
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
