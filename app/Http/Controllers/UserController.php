<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Jangan filter admin, tampilkan semua user
        $query = User::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_identitas', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter Kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter Jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nomor_identitas' => 'required|string|max:20|unique:users,nomor_identitas',
            'kelas' => 'nullable|string|in:10,11,12',
            'jurusan' => 'nullable|string',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas,siswa',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        User::create([
            'name' => $request->name,
            'nomor_identitas' => $request->nomor_identitas,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nomor_identitas' => 'required|string|max:20|unique:users,nomor_identitas,' . $user->id,
            'kelas' => 'nullable|string|in:10,11,12',
            'jurusan' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,petugas,siswa',
        ]);

        $user->name = $validated['name'];
        $user->nomor_identitas = $validated['nomor_identitas'];
        $user->kelas = $validated['kelas'];
        $user->jurusan = $validated['jurusan'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // NOTIFIKASI DIHAPUS
        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();

        // NOTIFIKASI DIHAPUS
        return redirect()->route('users.index');
    }
}