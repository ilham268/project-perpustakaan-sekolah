<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function store(Request $request)
    {
        $request->merge([
            'jurusan' => trim($request->jurusan),
        ]);

        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'in:X,XI,XII',
                Rule::unique('kelas', 'nama_kelas')
                    ->where(function ($query) use ($request) {
                        return $query->where('jurusan', $request->jurusan);
                    }),
            ],
            'jurusan' => ['required', 'string', 'max:255'],
        ], [
            'nama_kelas.required' => 'Kelas wajib dipilih.',
            'nama_kelas.in' => 'Kelas harus X, XI, atau XII.',
            'nama_kelas.unique' => 'Kombinasi kelas dan jurusan ini sudah ada.',
            'jurusan.required' => 'Jurusan wajib diisi.',
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'jurusan' => $request->jurusan,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', true);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->merge([
            'jurusan' => trim($request->jurusan),
        ]);

        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'in:X,XI,XII',
                Rule::unique('kelas', 'nama_kelas')
                    ->where(function ($query) use ($request) {
                        return $query->where('jurusan', $request->jurusan);
                    })
                    ->ignore($kelas->id),
            ],
            'jurusan' => ['required', 'string', 'max:255'],
        ], [
            'nama_kelas.required' => 'Kelas wajib dipilih.',
            'nama_kelas.in' => 'Kelas harus X, XI, atau XII.',
            'nama_kelas.unique' => 'Kombinasi kelas dan jurusan ini sudah ada.',
            'jurusan.required' => 'Jurusan wajib diisi.',
        ]);

        DB::transaction(function () use ($kelas, $request) {
            $oldNamaKelas = $kelas->nama_kelas;
            $oldJurusan = $kelas->jurusan;
            $newNamaKelas = $request->nama_kelas;
            $newJurusan = $request->jurusan;

            $kelas->update([
                'nama_kelas' => $newNamaKelas,
                'jurusan' => $newJurusan,
            ]);

            User::query()
                ->where('role', 'siswa')
                ->where('kelas', $oldNamaKelas)
                ->where('jurusan', $oldJurusan)
                ->update([
                    'kelas' => $newNamaKelas,
                    'jurusan' => $newJurusan,
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route('users.index')
            ->with('updated', true);
    }

    public function destroy(Kelas $kelas)
    {
        if (User::query()
            ->where('role', 'siswa')
            ->where('kelas', $kelas->nama_kelas)
            ->where('jurusan', $kelas->jurusan)
            ->exists()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih digunakan oleh data siswa. Pindahkan siswa ke kelas lain terlebih dahulu.');
        }

        $kelas->delete();

        return redirect()
            ->route('users.index')
            ->with('deleted', true);
    }
}
