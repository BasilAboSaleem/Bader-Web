<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->where('role', User::ROLE_ADMIN)
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
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_ADMIN,
        ]);

        return redirect()->route('dashboard.users.index')
            ->with('status', __('dashboard.users_created'));
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403);

        $user->delete();

        return redirect()->route('dashboard.users.index')
            ->with('status', __('dashboard.users_deleted'));
    }
}
