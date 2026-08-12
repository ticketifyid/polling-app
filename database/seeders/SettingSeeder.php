<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'nama_acara'      => 'Pemilihan Duta',
            'polling_dibuka'  => '0',
            'admin_password'  => Hash::make('panitia123'),
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
