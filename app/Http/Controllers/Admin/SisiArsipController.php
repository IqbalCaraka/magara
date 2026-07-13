<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SisiArsipController extends Controller
{
    public function index()
    {
        return view('admin.sisi-arsip.index');
    }
}
