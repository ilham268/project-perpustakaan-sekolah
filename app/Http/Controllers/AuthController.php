<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        // Coba login dengan role siswa
        if (Auth::attempt(['nomor_identitas' => $request->nomor_identitas, 'password' => $request->password, 'role' => 'siswa'])) {
            return $this->redirectToDashboard();
        }
        
        // Coba login dengan role petugas
        if (Auth::attempt(['nomor_identitas' => $request->nomor_identitas, 'password' => $request->password, 'role' => 'petugas'])) {
            return $this->redirectToDashboard();
        }
        
        // Coba login dengan role admin
        if (Auth::attempt(['nomor_identitas' => $request->nomor_identitas, 'password' => $request->password, 'role' => 'admin'])) {
            return $this->redirectToDashboard();
        }

        return back()->withErrors([
            'nomor_identitas' => 'Nomor Identitas atau password salah.',
        ])->withInput($request->only('nomor_identitas'));
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nomor_identitas' => 'required|string|unique:users,nomor_identitas',
            'kelas' => 'required|string', // Ubah dari in:10,11,12 jadi string
            'jurusan' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Konversi kelas jika masih dalam format angka (10,11,12)
        $kelas = $request->kelas;
        $jurusan = $request->jurusan;
        
        // Map untuk konversi angka ke Romawi
        $mapRomawi = [
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII',
        ];
        
        // Jika kelas masih angka, konversi ke format lengkap
        if (isset($mapRomawi[$kelas])) {
            // Ambil angka dari jurusan (misal: TKJ 1 -> TKJ)
            $jurusanClean = preg_replace('/\s\d+$/', '', $jurusan);
            $kelas = $mapRomawi[$kelas] . ' ' . $jurusanClean . ' 1';
        }
        
        $user = User::create([
            'name' => $request->name,
            'nomor_identitas' => $request->nomor_identitas,
            'kelas' => $kelas,
            'jurusan' => $request->jurusan,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
        ]);

        // Auto login setelah register
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