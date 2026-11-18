<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DaftarService;
use Illuminate\Http\Request;

class DaftarServiceController extends Controller
{
    public function index()
    {
        return response()->json(
            DaftarService::with(['kendaraan', 'pelanggan'])->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'keluhan_kendaraan' => 'required|string',
            'tanggal_servis' => 'required|date',
        ]);

        $data = DaftarService::create($validated);

        return response()->json([
            'message' => 'Daftar service berhasil ditambahkan',
            'data' => $data
        ], 201);
    }

    public function show(DaftarService $daftarService)
    {
        return response()->json(
            $daftarService->load(['kendaraan', 'pelanggan'])
        );
    }

    public function update(Request $request, DaftarService $daftarService)
    {
        $validated = $request->validate([
            'kendaraan_id' => 'sometimes|required|exists:kendaraans,id',
            'pelanggan_id' => 'sometimes|required|exists:pelanggans,id',
            'keluhan_kendaraan' => 'sometimes|required|string',
            'tanggal_servis' => 'sometimes|required|date',
        ]);

        $daftarService->update($validated);

        return response()->json([
            'message' => 'Daftar service berhasil diperbarui',
            'data' => $daftarService
        ]);
    }

    public function destroy(DaftarService $daftarService)
    {
        $daftarService->delete();

        return response()->json([
            'message' => 'Daftar service berhasil dihapus'
        ]);
    }
}
