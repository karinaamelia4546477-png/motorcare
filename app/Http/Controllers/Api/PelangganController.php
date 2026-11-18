<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    // Menampilkan semua data pelanggan
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil ditampilkan',
            'data' => Pelanggan::all()
        ], 200);
    }

    // Menambahkan pelanggan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'pekerjaan' => 'required|string|max:255',
        ]);

        $pelanggan = Pelanggan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan',
            'data' => $pelanggan
        ], 201);
    }

    // Menampilkan satu pelanggan
    public function show($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $pelanggan
        ], 200);
    }

    // Mengubah data pelanggan
    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'sometimes|required|string|max:255',
            'no_hp' => 'sometimes|required|string|max:20',
            'alamat' => 'sometimes|required|string',
            'pekerjaan' => 'sometimes|required|string|max:255',
        ]);

        $pelanggan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil diperbarui',
            'data' => $pelanggan
        ], 200);
    }

    // Menghapus pelanggan
    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil dihapus'
        ], 200);
    }
}
