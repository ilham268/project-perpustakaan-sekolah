<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
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

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'jurusan' => $request->jurusan,
        ]);

        return redirect()
            ->route('users.index')
            ->with('updated', true);
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()
            ->route('users.index')
            ->with('deleted', true);
    }
}