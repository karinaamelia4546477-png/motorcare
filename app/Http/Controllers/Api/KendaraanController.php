<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KendaraanController extends Controller
{
    // Menampilkan semua kendaraan
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Kendaraan::all()
        ], 200);
    }

    // Menambahkan kendaraan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_plat' => 'required|string|max:20|unique:kendaraans,no_plat',
            'jenis_kendaraan' => [
                'required',
                Rule::in(['Matic', 'Manual Transmisi'])
            ],
            'no_stnk' => 'required|string|max:100|unique:kendaraans,no_stnk',
            'tahun_pembuatan' => 'required|integer|min:1900|max:' . date('Y'),
            'nama_pemilik' => 'required|string|max:255',
            'warna_kendaraan' => 'required|string|max:100',
        ]);

        $kendaraan = Kendaraan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kendaraan berhasil ditambahkan',
            'data' => $kendaraan
        ], 201);
    }

    // Menampilkan detail kendaraan
    public function show($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $kendaraan
        ], 200);
    }

    // Mengubah kendaraan
    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $validated = $request->validate([
            'no_plat' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('kendaraans', 'no_plat')->ignore($kendaraan->id)
            ],
            'jenis_kendaraan' => [
                'sometimes',
                'required',
                Rule::in(['Matic', 'Manual Transmisi'])
            ],
            'no_stnk' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('kendaraans', 'no_stnk')->ignore($kendaraan->id)
            ],
            'tahun_pembuatan' => 'sometimes|required|integer|min:1900|max:' . date('Y'),
            'nama_pemilik' => 'sometimes|required|string|max:255',
            'warna_kendaraan' => 'sometimes|required|string|max:100',
        ]);

        $kendaraan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kendaraan berhasil diperbarui',
            'data' => $kendaraan
        ], 200);
    }

    // Menghapus kendaraan
    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $kendaraan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kendaraan berhasil dihapus'
        ], 200);
    }
}
