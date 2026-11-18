<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PembayaranService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PembayaranServiceController extends Controller
{
    public function index()
    {
        return response()->json(
            PembayaranService::with('daftarService')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'daftar_service_id' => 'required|exists:daftar_services,id',
            'jumlah_biaya_servis' => 'required|numeric|min:0',
            'jenis_pembayaran' => [
                'nullable',
                Rule::in(['Cash', 'Non Tunai']),
            ],
            'keterangan' => 'nullable|string',
        ]);

        $data = PembayaranService::create($validated);

        return response()->json([
            'message' => 'Pembayaran service berhasil ditambahkan',
            'data' => $data
        ], 201);
    }

    public function show(PembayaranService $pembayaranService)
    {
        return response()->json(
            $pembayaranService->load('daftarService')
        );
    }

    public function update(Request $request, PembayaranService $pembayaranService)
    {
        $validated = $request->validate([
            'daftar_service_id' => 'sometimes|required|exists:daftar_services,id',
            'jumlah_biaya_servis' => 'sometimes|required|numeric|min:0',
            'jenis_pembayaran' => [
                'sometimes',
                'nullable',
                Rule::in(['Cash', 'Non Tunai']),
            ],
            'keterangan' => 'sometimes|nullable|string',
        ]);

        $pembayaranService->update($validated);

        return response()->json([
            'message' => 'Pembayaran service berhasil diperbarui',
            'data' => $pembayaranService
        ]);
    }

    public function destroy(PembayaranService $pembayaranService)
    {
        $pembayaranService->delete();

        return response()->json([
            'message' => 'Pembayaran service berhasil dihapus'
        ]);
    }
}
