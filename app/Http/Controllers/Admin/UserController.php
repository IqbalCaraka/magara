<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'nip_nik' => 'nullable|string|unique:users,nip_nik',
            'role' => 'required|in:superadmin,admin,pic,pkl,magang,viewer',
        ]);

        $validated['password'] = 'ditakasnbkn';

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan. Password default: ditakasnbkn');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => 'ditakasnbkn']);

        return back()->with('success', "Password {$user->nama} berhasil direset ke default.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'nip_nik' => 'nullable|string|unique:users,nip_nik,' . $user->id,
            'role' => 'required|in:superadmin,admin,pic,pkl,magang,viewer',
            'password' => 'nullable|string|min:3|confirmed',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function changePassword()
    {
        return view('admin.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        auth()->user()->update(['password' => $request->password]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
