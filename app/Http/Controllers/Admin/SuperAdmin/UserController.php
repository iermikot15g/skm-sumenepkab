<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of Users.
     */
    public function index()
    {
        $users = User::with('opd')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.super-admin.users.index', compact('users'));
    }

    /**
     * Show form to create new User.
     */
    public function create()
    {
        $opds = Opd::where('is_active', true)->get();
        return view('admin.super-admin.users.create', compact('opds'));
    }

    /**
     * Store a newly created User.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:super_admin,admin_opd,pimpinan_opd',
            'opd_id' => 'required_if:role,admin_opd,pimpinan_opd|nullable|exists:opds,id',
            'password' => 'required|string|min:8',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ];

        // Hanya set opd_id jika role bukan super_admin
        if (in_array($validated['role'], ['admin_opd', 'pimpinan_opd'])) {
            $data['opd_id'] = $validated['opd_id'];
        }

        User::create($data);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User berhasil ditambahkan. Password: ' . $validated['password']);
    }

    /**
     * Show form to edit User.
     */
    public function edit(User $user)
    {
        $opds = Opd::where('is_active', true)->get();
        return view('admin.super-admin.users.edit', compact('user', 'opds'));
    }

    /**
     * Update the specified User.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin_opd,pimpinan_opd',
            'opd_id' => 'required_if:role,admin_opd,pimpinan_opd|nullable|exists:opds,id',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $data['password'] = Hash::make($request->password);
        }

        // Hanya set opd_id jika role bukan super_admin
        if (in_array($validated['role'], ['admin_opd', 'pimpinan_opd'])) {
            $data['opd_id'] = $validated['opd_id'];
        } else {
            $data['opd_id'] = null;
        }

        $user->update($data);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    /**
     * Remove the specified User.
     */
    public function destroy(User $user)
    {
        // Cegah menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password for user.
     */
    public function resetPassword(User $user)
    {
        $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Password user ' . $user->name . ' telah direset menjadi: ' . $newPassword);
    }

    /**
     * Toggle active status (soft delete/restore).
     */
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($user->trashed()) {
            $user->restore();
            $message = 'User berhasil diaktifkan kembali.';
        } else {
            $user->delete();
            $message = 'User berhasil dinonaktifkan.';
        }

        return redirect()->route('super-admin.users.index')
            ->with('success', $message);
    }
}