<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->whereIn('role', User::MANAGED_ROLES)
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('dashboard.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('dashboard.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'role' => ['required', Rule::in(User::MANAGED_ROLES)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return redirect()->route('dashboard.users.index')
            ->with('status', __('dashboard.users_created'));
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403);
        abort_unless(in_array($user->role, User::MANAGED_ROLES, true), 403);

        $user->delete();

        return redirect()->route('dashboard.users.index')
            ->with('status', __('dashboard.users_deleted'));
    }
}
