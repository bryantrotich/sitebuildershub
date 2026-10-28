<?php

namespace App\Http\Controllers\Landing;

use App\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\CreatePostingRequest;
use App\Http\Requests\Landing\CreatePromotionPostingRequest;
use App\Http\Requests\Landing\PostingFileUploadRequest;
use App\Http\Requests\Landing\QuotationUploadRequest;
use App\Mail\CreatePromotionInvoiceMail;
use App\Models\CategoryModel;
use App\Models\InvoiceModel;
use App\Models\PlacementModel;
use App\Models\PostingCategoryModel;
use App\Models\PostingModel;
use App\Models\PromotionModel;
use App\Models\SubCategoryModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\Laravel\Facades\Image;  // facade
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;

class MyPostingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user     = auth('client')->user();
        $postings = $user->postings()->paginate(10);
        $status   = session('status');

        return Inertia::render('Landing/MyPostings',compact('postings','status'));
    }

    /**
     * Show the form for creating a new resource.   
     */
    public function create()
    {    
        Gate::authorize('create-posting');

        $categories     = CategoryModel::with(['subCategories','childSubCategories'])->get();
        $placements     = PlacementModel::get();
        $maps           = Company::maps();
        $status         = session('status');

        return Inertia::render('Landing/CreatePosting',compact('categories','maps','placements','status'));
    }

    /**
     * Display the specified resource.
     */
    public function imageUpload(PostingFileUploadRequest $request)
    {
        //
        $validated = $request->validate([
            'image' => [
                'required',
                'image',
                Rule::dimensions()->minWidth(1080)->minHeight(720),
                // File::image()->types(['jpeg,png,jpg'])->max(10 * 1024 * 1024)
            ]
        ]);     

        $uploaded = $validated['image'];
        $name     = Str::uuid().'.'.$uploaded->getClientOriginalExtension();

        // $image = Image::read($uploaded);
        // $cropped = $image->cover(1024,720,50,50);

        Storage::disk('public')->put('images/'.$name,file_get_contents($uploaded));

        // $cropped->save(storage_path('app/public/images/'.$name));

        return response()->json(array('name' => $name ));
    }    

    /**
     * Display the specified resource.
     */
    public function quotationUpload(QuotationUploadRequest $request)
    {
        //
        $image = $request->file('quotation');
               
        $name  = Str::uuid().'.'.$image->getClientOriginalExtension();
        $image->move(storage_path('app/public/quotation'), $name);

        return response()->json(array('name' => $name ));
    } 
    
    /**
     * Display the specified resource.
     */
    public function quotationRemove(PostingModel $posting, Request $request)
    {
        $validated = $request->validated([
            'filename' => 'required|string'
        ]);

        $posting->update(['quotation' => ""]);

        Storage::disk('public')->delete('/quotation/'.$validated['filename']);

        return back();
    }   
    
    /**
     * Display the specified resource.
     */
    public function imageRemove(PostingModel $posting, Request $request)
    {
        $filename = $request->input('filename');

        $posting->update(['images' => collect($posting->images)->filter( fn ($image) => $image != $filename )]);

        Storage::disk('public')->delete('/images/'.$filename);

        return back();
    }       

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePostingRequest $request)
    {
        Gate::authorize('create-posting');
        
        try {
            
            $form = $request->validated();
            
            if( !empty($form['promotion_image']) ){
                $base64String = explode(',',$form['promotion_image']);
                $ext          = explode('/', explode(';',$base64String[0])[0])[1];
                $name         = Str::uuid().'.'.$ext;

                Storage::disk('public')->put('images/'.$name, base64_decode($base64String[1]));
                
                $form['promotion_image'] = $name;
            }

            $form['user_id']         = auth('client')->user()->id;

            $posting                 = PostingModel::create($form);        
    
            PostingCategoryModel::insert(
                collect($form['sub_categories'])->map( 
                    fn($value): array => [
                        'id'              => Str::uuid(),
                        'posting_id'      => $posting->id,
                        'category_id'     => $value['category_id'], 
                        'sub_category_id' => $value['sub_category_id'],
                        'child_sub_category_id' => $value['id'],
                        'created_at'      => now(),
                        'updated_at'      => now()
                    ] 
                )->toArray()
            );

            if( $form['promotion_status'] ){
                
                $promotion = PromotionModel::create([
                    'amount'       => $form['promotion_amount'],
                    'image'        => $form['promotion_image'],
                    'date_from'    => $form['promotion_date_from'],
                    'date_to'      => $form['promotion_date_to'],
                    'posting_id'   => $posting->id,
                    'placement_id' => $form['placement_id'],
                ]);

                $invoice = InvoiceModel::create([
                    'amount'          => $form['promotion_amount'],
                    'due_date'        => now()->format('Y-m-d'),
                    'invoice_number'  => intval(now()->format('Ymdhis')),
                    'sourceable_id'   => $form['placement_id'],
                    'sourceable_type' => PlacementModel::class,
                    'targetable_id'   => $promotion->id,
                    'targetable_type' => PromotionModel::class,
                    'user_id'         => $form['user_id']            
                ]);   

                Mail::to(auth('client')->user())->send( new CreatePromotionInvoiceMail($invoice) );          

                return back()->with('data',compact('invoice'));
            }
            
            if( !$form['promotion_status'] ){
                return back()->with('message', 'Posting created successfully');        
            }

        } catch(Error $error) {

            return back()->with('message', 'Something went wrong. Please try again.');

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PostingModel $posting)
    {
        $categories  = CategoryModel::with(['subCategories'])->get();
        $locations   = config('location');

        $posting->load(['categories','promotions']);
                               
        return Inertia::render('Landing/ViewMyPosting',compact('categories','locations','posting'));
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
    public function update(PostingModel $posting, Request $request)
    {
        //
        $validated = $request->validate([
            'categories'   => 'required|array',
            'description'  => 'required|string',
            'phone_number' => 'required|string',
            'images'       => 'required|array',
            'quotation'    => 'required|string',
            'title'        => 'required|string',
        ]);

        $updated_categories = collect($validated['categories'])->map( fn ($category) => $category['id'] );
        $stored_categories  = collect($posting->categories)->map( fn ($category) => $category->id );
        $add_categories     = collect($updated_categories)->diff($stored_categories);
        $delete_categories  = collect($stored_categories)->diff($updated_categories);

        if( !empty($add_categories) ){
            
            $add_categories->each( 
                function($category,$key) use($posting) {
                    $target = SubCategoryModel::with(['category'])->find($category);
                    PostingCategoryModel::create([
                        'posting_id' => $posting->id,
                        'category_id' =>  $target->category->id,
                        'sub_category_id' => $category
                    ]);
                }
            );

        }

        if( !empty($delete_categories) ){
            
            $delete_categories->each( 
                function($category,$key) use($posting) {
                    PostingCategoryModel::where([
                        'posting_id' => $posting->id,
                        'sub_category_id' => $category
                    ])->delete();
                }
            );

        }        

        return back()->with('message','Posting has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostingModel $posting)
    {
        $posting->delete();

        return back()->with('message', 'Posting has been deleted');
    }
}
