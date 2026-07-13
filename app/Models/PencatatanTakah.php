<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencatatanTakah extends Model
{
    protected $table = 'pencatatan_takah';

    protected $fillable = [
        'nip', 'kode_rak', 'posisi_takah', 'created_by',
        'date_created', 'status', 'is_different',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'is_different' => 'boolean',
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
