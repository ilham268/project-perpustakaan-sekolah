<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255', 'unique:kelas,nama_kelas'],
            'jurusan' => ['required', 'string', 'max:255'],
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'jurusan' => $request->jurusan,
        ]);

        return redirect()->route('users.index')->with('success', true);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255', 'unique:kelas,nama_kelas,' . $kelas->id],
            'jurusan' => ['required', 'string', 'max:255'],
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'jurusan' => $request->jurusan,
        ]);

        return redirect()->route('users.index')->with('updated', true);
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('users.index')->with('deleted', true);
    }
}