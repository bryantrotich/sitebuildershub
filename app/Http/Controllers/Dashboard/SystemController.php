<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SystemRequest;
use App\Models\Company;
use App\Services\PesaPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SystemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This method displays a list of system settings such as mail and company logo/icon.
     */
    public function index()
    {
        // Get the first row of the company table
        $company = Company::first();

        // Get the mail settings from the config file
        $mail = [
            'host'     => config('mail.mailers.smtp.host'),
            'port'     => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
        ];

        // Get the logo and icon images
        $images = [
            'logo' => $company->logo,
            'icon' => $company->icon
        ];

        // Get the Google Maps settings saved on the company
        $maps = [
            'google_maps_api_key' => $company->google_maps_api_key,
            'google_maps_id'      => $company->google_maps_id,
            'env_api_key'         => !empty(config('services.google.api_key')),
        ];

        // Get the PesaPal settings from the config file; the secret is only shown masked
        $pesapal = config('services.pesapal.configuration');
        $payment = [
            'consumer_key'    => $pesapal['consumer_key'],
            'consumer_secret' => !empty($pesapal['consumer_secret']) ? str_repeat('•', 8).substr($pesapal['consumer_secret'], -4) : null,
            'environment'     => $pesapal['status'],
            'base_url'        => config('services.pesapal.base_url.'.$pesapal['status']),
        ];

        // Render the view and pass the data
        return Inertia::render('Dashboard/System', [
            'mail'    => $mail,
            'maps'    => $maps,
            'payment' => $payment,
            'images' => $images,
            // Pass the status of the session, if it exists
            'status' => session('status')
        ]);
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
    public function store(SystemRequest $request)
    {
        //
        $form_data = $request->validated();
                
        if( $form_data['tab'] == 'images' ){

            $company = Company::first();

            foreach( ['logo','icon'] as $field ){

                if( !$request->hasFile($field) ){ continue; }

                // Unique names so a logo and icon uploaded together never overwrite each other.
                $name = Str::uuid().'.'.$request->file($field)->getClientOriginalExtension();

                Storage::disk('public')->putFileAs('images', $request->file($field), $name);

                $previous = $company->getRawOriginal($field);

                $company->update([ $field => $name ]);

                if( !empty($previous) ){
                    Storage::disk('public')->delete('images/'.$previous);
                }
            }

            return back()->with('message', 'Logo and icon updated successfully.');
        }

        if( $form_data['tab'] == 'maps' ){

            Company::first()->update([
                'google_maps_api_key' => $form_data['google_maps_api_key'],
                'google_maps_id'      => $form_data['google_maps_id'] ?? null,
            ]);

            return back()->with('message', 'Google Maps settings updated successfully.');
        }

        if( $form_data['tab'] == 'payment' ){

            // Request a token with the credentials from the config file to confirm they are authorized.
            try {
                $auth = app(PesaPalService::class)->authenticate();
            } catch(\Throwable $error) {
                return back()->withErrors(['pesapal' => 'Could not reach PesaPal. Please try again.']);
            }

            if( empty($auth->token) ){
                return back()->withErrors(['pesapal' => 'PesaPal rejected the credentials: '.(($auth->error->message ?? null) ?: ($auth->error->code ?? 'unknown error')).'.']);
            }

            return back()->with('message', 'Connected to PesaPal '.config('services.pesapal.configuration.status').' successfully.');
        }
        
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
