<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $users = User::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $archived = User::onlyTrashed()->orderBy('name')->get();

        return view('owner.users.index', compact('users', 'archived', 'search'));
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

        return $this->respond($request, 'Staff member created.');
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

        return $this->respond($request, 'Staff member updated.');
    }

    /**
     * Soft delete: the account is archived and its sales history is kept
     * intact (sales stay attributed to the archived staff member).
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            $msg = 'You cannot archive your own account.';

            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect()->route('owner.users.index')->with('error', $msg);
        }

        if ($user->role === 'owner' && User::where('role', 'owner')->count() <= 1) {
            $msg = 'You cannot archive the last remaining owner.';

            return $request->wantsJson()
                ? response()->json(['message' => $msg], 422)
                : redirect()->route('owner.users.index')->with('error', $msg);
        }

        $user->delete();

        return $this->respond($request, 'Staff member archived. Their sales history is preserved.');
    }

    public function restore(Request $request, int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return $this->respond($request, 'Staff member restored.');
    }

    private function respond(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()->route('owner.users.index')->with('status', $message);
    }
}
