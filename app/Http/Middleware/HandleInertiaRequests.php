<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        
        $request->setUserResolver(function () use($request){
            return Auth::guard(Str::contains($request->url(), 'dashboard') ? 'admin' : 'client')->user();
        });

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'company' => fn () => Company::first()?->only(['name','logo','icon']),
            'data' => session('data'),            
            'flash' => [
                'message' => session('message')
            ],
            'csrf_token' => csrf_token(),
        ];
    }
}
