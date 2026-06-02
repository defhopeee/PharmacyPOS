<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $users = User::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('owner.users.index', compact('users', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'role' => ['required', 'in:owner,attendant'],
        ]);

        $password = $this->generatePassword();
        $data['phone'] = $this->normalisePhone($data['phone']);
        $data['password'] = $password;
        User::create($data);

        return $this->respond($request, "Staff member created. Temporary password for {$data['name']}:", $password);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone,'.$user->id],
            'role' => ['required', 'in:owner,attendant'],
        ]);

        $data['phone'] = $this->normalisePhone($data['phone']);
        $user->update($data);

        return $this->respond($request, 'Staff member updated.');
    }

    /**
     * Generate a fresh password for a staff member and reveal it once.
     */
    public function resetPassword(Request $request, User $user)
    {
        $password = $this->generatePassword();
        $user->update(['password' => $password]);

        return $this->respond($request, "New password for {$user->name}:", $password);
    }

    /**
     * Delete a staff member. It's a soft delete under the hood, so their
     * past sales stay on record (attributed to them) for accountability.
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return $this->error($request, 'You cannot delete your own account.');
        }

        if ($user->role === 'owner' && User::where('role', 'owner')->count() <= 1) {
            return $this->error($request, 'You cannot delete the last remaining owner.');
        }

        $user->delete();

        return $this->respond($request, 'Staff member deleted. Their past sales stay on record.');
    }

    private function generatePassword(): string
    {
        // Strong, no dashes/underscores, avoids look-alike characters.
        $sets = ['ABCDEFGHJKLMNPQRSTUVWXYZ', 'abcdefghijkmnpqrstuvwxyz', '23456789', '!@#$%*?'];
        $pw = '';
        foreach ($sets as $set) {
            $pw .= $set[random_int(0, strlen($set) - 1)];
        }
        $all = implode('', $sets);
        for ($i = 0; $i < 6; $i++) {
            $pw .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($pw);
    }

    private function normalisePhone(string $phone): string
    {
        return preg_replace('/\s+/', '', $phone);
    }

    private function respond(Request $request, string $message, ?string $password = null)
    {
        if ($request->wantsJson()) {
            return response()->json(array_filter([
                'message' => $message,
                'password' => $password,
            ], fn ($v) => $v !== null));
        }

        return redirect()->route('owner.users.index')
            ->with('status', $message.($password ? ' '.$password : ''));
    }

    private function error(Request $request, string $message)
    {
        return $request->wantsJson()
            ? response()->json(['message' => $message], 422)
            : redirect()->route('owner.users.index')->with('error', $message);
    }
}
