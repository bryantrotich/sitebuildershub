<?php

use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ChatController as DashboardChatController;
use App\Http\Controllers\Dashboard\ChildSubCategoryController;
use App\Http\Controllers\Landing\AboutUsController;
use App\Http\Controllers\Landing\ContactUsController;
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\Landing\PostingController as LandingPostingController;
use App\Http\Controllers\Dashboard\ClientsController;
use App\Http\Controllers\Dashboard\CompanyController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\OverviewController as DashboardOverviewController;
use App\Http\Controllers\Dashboard\PlacementController;
use App\Http\Controllers\Landing\OverviewController as LandingOverviewController;
use App\Http\Controllers\Dashboard\PostingController as DashboardPostingController;
use App\Http\Controllers\Dashboard\ProfileController as DashboardProfileController;
use App\Http\Controllers\Dashboard\StaffController;
use App\Http\Controllers\Dashboard\SubCategoryController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\Dashboard\SystemController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\GoogleMapsController;
use App\Http\Controllers\Landing\ChatController;
use App\Http\Controllers\Landing\CommentController;
use App\Http\Controllers\Landing\DashboardController;
use App\Http\Controllers\Landing\InvoiceController;
use App\Http\Controllers\Landing\MyPostingsController;
use App\Http\Controllers\Landing\ProfileController as LandingProfileController;
use App\Http\Controllers\Landing\ProjectsControiller;
use App\Http\Controllers\Landing\SubscriptionController as LandingSubscriptionController;
use App\Http\Controllers\Landing\TransactionController as LandingTransactionController;
use App\Models\TransactionModel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::name('landing.')->group(function () {
    
    Route::middleware('landing.auth.optional')->group(function () {

        Route::get('/',                        [HomeController::class, 'index'])->name('home');
        Route::get('/header',                  [HomeController::class, 'create'])->name('header');
        Route::get('/footer',                  [HomeController::class, 'footer'])->name('footer');
        Route::get('/security',                 [HomeController::class, 'security'])->name('security');
        Route::get('/postings/view',           [LandingPostingController::class, 'show'])->name('postings.view');
        Route::get('/postings',                [LandingPostingController::class, 'index'])->name('postings');
        Route::get('/projects',                [ProjectsControiller::class, 'index'])->name('projects');
        Route::get('/projects/view',           [ProjectsControiller::class, 'show'])->name('projects.view');
        Route::get('/aboutus',                 [AboutUsController::class, 'index'])->name('aboutus');
        Route::get('/contactus',               [ContactUsController::class, 'index'])->name('contactus');
        Route::get('/places',                  [GoogleMapsController::class, 'create'])->name('maps.places');
        
        Route::get('/subscriptions/{subscription}/checkout',[LandingSubscriptionController::class, 'create'])->name('subscription.checkout');
        Route::get('/subscriptions',           [LandingSubscriptionController::class, 'index'])->name('subscription');
        Route::post('/subscriptions',          [LandingSubscriptionController::class, 'store'])->name('subscriptions.store');
        
        Route::get('/transactions/{invoice_number}/ipn', [LandingTransactionController::class, 'ipn'])->name('transactions.invoice.ipn');
        Route::get('/transactions/subscription/{user_subscription}/complete', [LandingTransactionController::class, 'subscription_complete'])->name('transactions.subscription.complete');
        Route::post('/transactions/subscription/create',                      [LandingTransactionController::class, 'subscription'])->name('transactions.subscription.create');

    });
    
    Route::middleware('landing.auth')->group(function () {
        Route::get('/overview',                [LandingOverviewController::class, 'index'])->name('overview');

        Route::get('/mypostings/{posting}/view',             [MyPostingsController::class, 'show'])->name('mypostings.show');    
        Route::get('/mypostings/create',                     [MyPostingsController::class, 'create'])->name('mypostings.create');
        Route::get('/mypostings',                            [MyPostingsController::class, 'index'])->name('mypostings');
        Route::post('/mypostings/quotation/upload',          [MyPostingsController::class, 'quotationUpload'])->name('mypostings.quotation.upload');
        Route::post('/mypostings/images/upload',             [MyPostingsController::class, 'imageUpload'])->name('mypostings.image.upload');
        Route::post('/mypostings/promote',                   [MyPostingsController::class, 'promote'])->name('mypostings.promote');
        Route::post('/mypostings',                           [MyPostingsController::class, 'store'])->name('mypostings.store');
        Route::put('/mypostings/{posting}/quotation/remove', [MyPostingsController::class, 'quotationRemove'])->name('mypostings.quotation.remove');
        Route::put('/mypostings/{posting}/images/remove',    [MyPostingsController::class, 'imageRemove'])->name('mypostings.image.remove');
        Route::put('/mypostings/{posting}/update',           [MyPostingsController::class, 'update'])->name('mypostings.update');
        Route::delete('/mypostings/{posting}/delete',        [MyPostingsController::class, 'destroy'])->name('mypostings.delete');
        
        Route::get('transactions/{promotion}/complete',[LandingTransactionController::class, 'update'])->name('transactions.promotion.complete');
        Route::put('transactions/{promotion}/create',  [LandingTransactionController::class, 'create'])->name('transactions.promotion.create');

        Route::post('/comments/{posting}/store',[CommentController::class, 'store'])->name('comments.store');

        Route::get('/chat',                    [ChatController::class, 'index'])->name('chat');
        Route::get('/profile/{tab}',           [LandingProfileController::class, 'create'])->name('profile');
        Route::post('/profile/business/store', [LandingProfileController::class, 'business_store'])->name('profile.business.store');
        Route::post('/profile/project/store',  [LandingProfileController::class, 'project_store'])->name('profile.project.store');
        Route::post('/profile/project/file',   [LandingProfileController::class, 'project_store_file'])->name('profile.project.file');
        Route::post('/profile/personal/image', [LandingProfileController::class, 'personal_store_image'])->name('profile.personal.image');
        Route::post('/profile/business',       [LandingProfileController::class, 'business'])->name('profile.business');
        Route::post('/profile/projects',       [LandingProfileController::class, 'projects'])->name('profile.project');
        Route::post('/profile/personal',       [LandingProfileController::class, 'personal'])->name('profile.personal');
        Route::post('/profile/subscription',   [LandingProfileController::class, 'subscription'])->name('profile.subscription');
        Route::post('/forbidden',              [LandingProfileController::class, 'forbidden'])->name('forbidden');
        // Route::put('/profile/edit', [LandingProfileController::class, 'index'])->name('profile.edit');

        Route::delete('/profile/project/{project}/delete',  [LandingProfileController::class, 'project_delete'])->name('profile.project.delete');
    
        Route::get('/invoices/{invoice_number}/paid',      [InvoiceController::class, 'paid'])->name('invoices.paid');    
        Route::get('/invoices/{invoice_number}/pay',      [InvoiceController::class, 'pay'])->name('invoices.pay');    
    });
});

Route::name('dashboard.')->prefix('dashboard')->group(function () {
    Route::middleware('admin.auth')->group(function () {
        Route::get('/',                             [DashboardOverviewController::class, 'index'])->name('overview');
        Route::get('/chat',                         [DashboardChatController::class, 'index'])->name('chat');
        Route::get('/notifications',                [NotificationController::class, 'index'])->name('notifications');
        Route::get('/company',                      [CompanyController::class, 'index'])->name('company');
        Route::get('/clients',                      [ClientsController::class, 'index'])->name('clients');

        Route::get('/placements',                       [PlacementController::class, 'index'])->name('placements');
        Route::put('/placements/{placement}/show',      [PlacementController::class, 'show'])->name('placements.edit');
        Route::post('/placements/{placement}/update',   [PlacementController::class, 'update'])->name('placements.update');        
        Route::post('/placements',                      [PlacementController::class, 'store'])->name('placements.store');
        Route::delete('/placements/{placement}/delete', [PlacementController::class, 'destroy'])->name('placements.delete');

        Route::get('/subscriptions',                          [SubscriptionController::class, 'index'])->name('subscriptions');
        Route::put('/subscriptions/{subscription}/show',      [SubscriptionController::class, 'show'])->name('subscriptions.edit');
        Route::post('/subscriptions/{subscription}/update',   [SubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::post('/subscriptions',                         [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::delete('/subscriptions/{subscription}/delete', [SubscriptionController::class, 'destroy'])->name('subscriptions.delete');
        
        Route::get('/transactions',                         [TransactionController::class, 'index'])->name('transactions');

        Route::get('/categories/fetch',                     [CategoryController::class, 'create'])->name('categories.fetch');
        Route::get('/categories',                           [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories',                          [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}/delete',      [CategoryController::class, 'destroy'])->name('categories.delete');

        Route::get('/subcategories',                          [SubCategoryController::class, 'index'])->name('sub_categories');
        Route::get('/categories/fetch',                       [SubCategoryController::class, 'create'])->name('sub_categories.fetch');
        Route::post('/subcategories',                         [SubCategoryController::class, 'store'])->name('sub_categories.store');
        Route::delete('/subcategories/{sub_category}/delete', [SubCategoryController::class, 'destroy'])->name('sub_categories.delete');

        Route::get('/childsubcategories',                            [ChildSubCategoryController::class, 'index'])->name('child_sub_categories');
        Route::post('/childsubcategories',                           [ChildSubCategoryController::class, 'store'])->name('child_sub_categories.store');
        Route::post('/childsubcategories/{childsubcategory}/delete', [ChildSubCategoryController::class, 'destroy'])->name('child_sub_categories.destroy');
       
        Route::get('/postings/{posting}', [DashboardPostingController::class, 'show'])->name('postings.show');
        Route::get('/postings',           [DashboardPostingController::class, 'index'])->name('postings');

        Route::get('/staff',                 [StaffController::class,    'index'])->name('staff');
        Route::post('/staff',                [StaffController::class,    'store'])->name('staff.store');
        Route::get('/system',                [SystemController::class,   'index'])->name('system');
        Route::post('/system',               [SystemController::class,   'store'])->name('system.store');
        Route::get('/profile',               [DashboardProfileController::class,   'index'])->name('profile'); 
        Route::post('/profile',              [DashboardProfileController::class,   'store'])->name('profile.store');
    });
});
require __DIR__.'/auth.php';
