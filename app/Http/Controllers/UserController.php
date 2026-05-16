<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
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

        $users = $query->latest()->paginate(10)->withQueryString();

        // Data kelas dari tabel kelas
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        // Data jurusan unik untuk filter
        $jurusanList = Kelas::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->select('jurusan')
            ->distinct()
            ->orderBy('jurusan')
            ->pluck('jurusan');

        return view('admin.users.index', compact('users', 'kelasList', 'jurusanList'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'nomor_identitas' => ['required', 'string', 'max:20', 'unique:users,nomor_identitas'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,petugas,siswa'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'nomor_identitas.required' => 'Nomor identitas wajib diisi.',
            'nomor_identitas.unique' => 'Nomor identitas sudah digunakan.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->role === 'siswa' && !$request->filled('kelas_id') && !$request->filled('kelas')) {
                $validator->errors()->add('kelas_id', 'Kelas wajib dipilih untuk siswa.');
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $kelasNama = null;
        $jurusanNama = null;

        // Kalau form mengirim kelas_id, ambil otomatis dari tabel kelas
        if ($request->filled('kelas_id')) {
            $kelas = Kelas::findOrFail($request->kelas_id);

            $kelasNama = $kelas->nama_kelas;
            $jurusanNama = $kelas->jurusan;
        } else {
            // Fallback kalau form lama masih mengirim kelas dan jurusan manual
            $kelasNama = $request->kelas;
            $jurusanNama = $request->jurusan;
        }

        User::create([
            'name' => $request->name,
            'nomor_identitas' => $request->nomor_identitas,
            'kelas' => $kelasNama,
            'jurusan' => $jurusanNama,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('users.index')->with('success', true);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'nomor_identitas' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'nomor_identitas')->ignore($user->id),
            ],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,petugas,siswa'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'nomor_identitas.required' => 'Nomor identitas wajib diisi.',
            'nomor_identitas.unique' => 'Nomor identitas sudah digunakan.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->role === 'siswa' && !$request->filled('kelas_id') && !$request->filled('kelas')) {
                $validator->errors()->add('kelas_id', 'Kelas wajib dipilih untuk siswa.');
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $kelasNama = null;
        $jurusanNama = null;

        // Kalau form mengirim kelas_id, ambil otomatis dari tabel kelas
        if ($request->filled('kelas_id')) {
            $kelas = Kelas::findOrFail($request->kelas_id);

            $kelasNama = $kelas->nama_kelas;
            $jurusanNama = $kelas->jurusan;
        } else {
            // Fallback kalau form lama masih mengirim kelas dan jurusan manual
            $kelasNama = $request->kelas;
            $jurusanNama = $request->jurusan;
        }

        $user->update([
            'name' => $request->name,
            'nomor_identitas' => $request->nomor_identitas,
            'kelas' => $kelasNama,
            'jurusan' => $jurusanNama,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')->with('updated', true);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('deleted', true);
    }
}