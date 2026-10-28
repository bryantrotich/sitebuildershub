<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Laravel\Facades\Image;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request)
    {
        return Inertia::render('Dashboard/Profile', [
            // 'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }
    
    /**
     * Update the signed-in admin's details and profile picture.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth('admin')->user();

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'picture'      => ['nullable', File::image()->types(['jpeg','jpg','png','webp'])->max(2048)],
        ]);

        if( $request->hasFile('picture') ){

            $name     = Str::uuid().'.'.$request->file('picture')->getClientOriginalExtension();
            $previous = $user->picture;

            // Square crop so the avatar looks right wherever it is shown.
            Image::read($request->file('picture'))
                ->cover(400, 400)
                ->save(Storage::disk('public')->path('images/'.$name));

            $validated['picture'] = $name;

            if( !empty($previous) ){
                Storage::disk('public')->delete('images/'.$previous);
            }
        } else {
            unset($validated['picture']);
        }

        $user->fill($validated);

        if( $user->isDirty('email') ){
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('message', 'Profile updated successfully.');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
