<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenScanning extends Model
{
    protected $table = 'dokumen_scanning';

    protected $fillable = [
        'nip', 'jenis_dokumen', 'created_by', 'date_created',
    ];

    protected function casts(): array
    {
        return [
            'date_created' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function arsipPns()
    {
        return $this->belongsTo(ArsipPns::class, 'nip', 'nip');
    }
}
