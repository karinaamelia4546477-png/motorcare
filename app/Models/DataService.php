<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataService extends Model
{
    protected $table = 'data_services';

    protected $fillable = [
        'daftar_service_id',
        'keluhan_kendaraan',
        'estimasi_service',
        'nama_mekanik',
        'sparepart_pengganti',
    ];

    public function daftarService()
    {
        return $this->belongsTo(DaftarService::class);
    }
}