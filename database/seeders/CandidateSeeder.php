<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CandidateSeeder extends Seeder
{
    /**
     * Sepuluh kandidat contoh untuk uji tampilan grid 5 kolom x 2 baris.
     *
     * Foto diambil dari gambar contoh yang sudah ada di proyek, lalu disalin
     * ke storage/app/public/kandidat/ dengan nama seed-XX.jpg. Setiap kandidat
     * punya file sendiri supaya menghapus salah satunya tidak merusak yang lain.
     */
    public function run(): void
    {
        $sources = $this->sourceImages();

        if ($sources === []) {
            $this->command?->warn('Tidak ada gambar contoh di public/img/. Seeder kandidat dilewati.');

            return;
        }

        $targetDir = storage_path('app/public/kandidat');
        File::ensureDirectoryExists($targetDir);

        $names = [
            'Andira Kusuma',   'Bagas Prasetyo',  'Citra Maharani', 'Dimas Ramadhan',
            'Elvira Nasution', 'Fajar Nugroho',   'Gita Anggraini', 'Hafiz Ardiansyah',
            'Intan Permata',   'Joko Wicaksono',
        ];

        $companies = [
            'AirNav Indonesia', 'Taspen', 'Asuransi Jasindo',
            'Danantara Indonesia', 'Cipta Kharisma',
        ];

        foreach ($names as $i => $name) {
            $foto = sprintf('kandidat/seed-%02d.jpg', $i + 1);

            // Sumber dipilih acak, jadi grid tidak tampil seragam saat diuji.
            File::copy($sources[array_rand($sources)], storage_path('app/public/' . $foto), true);

            Candidate::updateOrCreate(
                ['nama' => $name],
                [
                    'company'   => $companies[array_rand($companies)],
                    'foto'      => $foto,
                    'urutan'    => $i + 1,
                    'is_active' => true,
                ],
            );
        }

        $this->command?->info(count($names) . ' kandidat contoh dibuat.');
    }

    /**
     * @return list<string> Path absolut gambar yang bisa dipakai sebagai foto.
     */
    private function sourceImages(): array
    {
        // Gambar contoh di public/img/, ditambah foto kandidat yang sudah
        // pernah diupload (kecuali hasil seeder sebelumnya).
        $files = array_merge(
            File::glob(public_path('img/*.jpg')) ?: [],
            array_filter(
                File::glob(storage_path('app/public/kandidat/*.jpg')) ?: [],
                fn (string $path) => ! str_starts_with(basename($path), 'seed-'),
            ),
        );

        return array_values($files);
    }
}
