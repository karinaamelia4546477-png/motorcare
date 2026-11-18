<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataService;
use App\Models\DaftarService;
use Illuminate\Http\Request;

class DataServiceController extends Controller
{
    public function index()
    {
        $data = DataService::with([
            'daftarService.pelanggan',
            'daftarService.kendaraan'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'daftar_service_id' => 'required|exists:daftar_services,id',
            'estimasi_service' => 'required|numeric|min:0',
            'nama_mekanik' => 'required|string|max:255',
            'sparepart_pengganti' => 'nullable|string',
        ]);

        $daftarService = DaftarService::findOrFail(
            $validated['daftar_service_id']
        );

        $validated['keluhan_kendaraan'] =
            $daftarService->keluhan_kendaraan;

        $data = DataService::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data service berhasil ditambahkan',
            'data' => $data->load('daftarService')
        ], 201);
    }

    public function show(string $id)
    {
        $data = DataService::with('daftarService')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function update(Request $request, string $id)
    {
        $data = DataService::findOrFail($id);

        $validated = $request->validate([
            'daftar_service_id' => 'sometimes|required|exists:daftar_services,id',
            'estimasi_service' => 'sometimes|required|numeric|min:0',
            'nama_mekanik' => 'sometimes|required|string|max:255',
            'sparepart_pengganti' => 'nullable|string',
        ]);

        $data->fill($validated);

        $daftarService = DaftarService::findOrFail(
            $data->daftar_service_id
        );

        $data->keluhan_kendaraan =
            $daftarService->keluhan_kendaraan;

        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'Data service berhasil diperbarui',
            'data' => $data->load('daftarService')
        ]);
    }

    public function destroy(string $id)
    {
        $data = DataService::findOrFail($id);

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data service berhasil dihapus'
        ]);
    }
}
