<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(15);

        return view('owner.users.index', compact('users'));
    }

    public function create()
    {
        return view('owner.users.form', ['user' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:owner,attendant'],
            'active' => ['nullable', 'boolean'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $data['active'] = $request->boolean('active', true);
        User::create($data);

        return redirect()->route('owner.users.index')
            ->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        return view('owner.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:owner,attendant'],
            'active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $data['active'] = $request->boolean('active');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('owner.users.index')
            ->with('status', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('owner.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('owner.users.index')
            ->with('status', 'User deleted.');
    }
}
