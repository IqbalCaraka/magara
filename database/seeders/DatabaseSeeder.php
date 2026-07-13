<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'nama' => 'iqbal',
            'email' => 'iqbal@gmail.com',
            'nip_nik' => '123',
            'password' => '123',
            'role' => 'superadmin',
        ]);
    }
}
