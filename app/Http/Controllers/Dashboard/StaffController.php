<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Notifications\Dashboard\StaffInvitationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staff = User::with('role')
            ->whereHas('role',fn($query) => $query->where('state','>',0))
            ->orderByDesc('created_at')
            ->paginate(10);

        $roles = Role::where('company_id', auth('admin')->user()->company_id)
            ->where('state','>',0)
            ->orderByDesc('state')
            ->get(['id','name','state']);

        return Inertia::render('Dashboard/Staff',[
            'can_manage' => auth('admin')->user()->role?->state == 2,
            'roles'      => $roles,
            'staff'      => $staff,
            'status'     => session('status')
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
     * Add a staff member and email them a link to set their password.
     *
     * Only admins can add staff, so a staff member cannot grant themselves more access.
     */
    public function store(Request $request)
    {
        $admin = auth('admin')->user();

        abort_unless($admin->role?->state == 2, 403, 'Only administrators can add staff.');

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'role_id'      => ['required', Rule::exists('roles','id')->where(fn ($query) => $query->where('state','>',0)->where('company_id', $admin->company_id))],
        ]);

        $user = User::create([
            ...$validated,
            'company_id' => $admin->company_id,
            // Unusable until the invitation link is used to choose a password.
            'password'   => Hash::make(Str::random(40)),
            'token'      => Str::random(20),
        ]);

        $user->notify(new StaffInvitationNotification(Password::createToken($user)));

        return back()->with('message', "{$user->name} has been added and emailed a link to set their password.");
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
