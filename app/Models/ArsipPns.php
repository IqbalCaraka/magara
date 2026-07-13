<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipPns extends Model
{
    protected $table = 'arsip_pns';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'nip', 'nama', 'instansi_kerja_id', 'is_kedhuk_aktif',
        'status_arsip', 'kategori_kelengkapan_2026', 'skor_arsip_2026',
    ];

    protected function casts(): array
    {
        return [
            'status_arsip' => 'json',
            'skor_arsip_2026' => 'decimal:2',
            'is_kedhuk_aktif' => 'boolean',
        ];
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_kerja_id');
    }
}
