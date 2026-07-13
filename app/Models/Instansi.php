<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    protected $table = 'instansi';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'nama'];
}
