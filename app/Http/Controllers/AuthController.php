<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nomor_identitas' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt([
            'nomor_identitas' => $request->nomor_identitas,
            'password' => $request->password,
            'role' => 'siswa'
        ])) {
            return $this->redirectToDashboard();
        }

        if (Auth::attempt([
            'nomor_identitas' => $request->nomor_identitas,
            'password' => $request->password,
            'role' => 'petugas'
        ])) {
            return $this->redirectToDashboard();
        }

        if (Auth::attempt([
            'nomor_identitas' => $request->nomor_identitas,
            'password' => $request->password,
            'role' => 'admin'
        ])) {
            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'nomor_identitas' => 'Nomor Identitas atau password salah.',
        ])->withInput($request->only('nomor_identitas'));
    }

    public function showRegisterForm()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('auth.register', compact('kelasList'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nomor_identitas' => 'required|string|max:20|unique:users,nomor_identitas',
            'kelas_id' => 'required|exists:kelas,id',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nomor_identitas.required' => 'Nomor identitas wajib diisi.',
            'nomor_identitas.unique' => 'Nomor identitas sudah terdaftar.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);

        $user = User::create([
            'name' => $request->name,
            'nomor_identitas' => $request->nomor_identitas,
            'kelas' => $kelas->nama_kelas,
            'jurusan' => $kelas->jurusan,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
        ]);

        Auth::login($user);

        return redirect()->route('peminjam.list-buku');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    private function redirectToDashboard()
    {
        $role = Auth::user()->role;

        if ($role === 'siswa') {
            return redirect()->route('peminjam.list-buku');
        }

        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');

            case 'petugas':
                return redirect()->route('petugas.dashboard');

            default:
                Auth::logout();
                return redirect()->route('login');
        }
    }
}