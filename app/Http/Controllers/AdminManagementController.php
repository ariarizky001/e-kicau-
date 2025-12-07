<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminManagementController extends Controller
{
    public function index()
    {
        // Ambil semua user (termasuk super_admin)
        $users = User::orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin-management.index', compact('users'));
    }

    public function create()
    {
        return view('admin-management.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,operator',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'role.required' => 'Role harus dipilih',
            'role.in' => 'Role tidak valid',
        ]);

        // Create user
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin-management.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }

    public function edit(User $admin)
    {
        return view('admin-management.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,operator',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'role.required' => 'Role harus dipilih',
            'role.in' => 'Role tidak valid',
        ]);

        // Update user
        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $admin->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()->route('admin-management.index')
            ->with('success', 'Admin berhasil diupdate');
    }

    public function destroy(User $admin)
    {
        // Cegah delete jika user yang dihapus adalah user yang sedang login
        if ($admin->id === \Illuminate\Support\Facades\Auth::id()) {
            return redirect()->route('admin-management.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        $admin->delete();

        return redirect()->route('admin-management.index')
            ->with('success', 'Admin berhasil dihapus');
    }
}

