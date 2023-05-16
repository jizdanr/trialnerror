<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dokter;
use Illuminate\Support\Facades\Hash;

class DokterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Dokter::create([
            'nama_dokter' => 'Dokter',
            'spesialis' => 'Anak',
            'email' => 'dokter@gmail.com',
            'password' => Hash::make('12345678'),
            'alamat' => 'Bojongsoang',
            'no_telp' => '62812345678',
            'gender' => 'L',
            'tanggal_lahir' => '2001-12-20',
            'role' => 'dokter',
            'foto' => 'tes.jpg',
            'jam_buka' => '09:00',
            'jam_tutup' => '11:00'
        ]);
    }
}
