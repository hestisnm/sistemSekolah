<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\admin;
use App\Models\siswa;
use App\Models\Konten;
use App\Models\guru;
use App\Models\kelas;
use App\Models\walas;
use App\Models\kbm;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        kbm::factory(5)->create();
        //username dan password admin, admin
        admin::factory()->dataadmin1()->create();

        //username dan password guru, guru
        admin::factory()->dataadmin2()->create();

        //membuat 5 data untuk tabel konten
        konten::factory()->count(5)->create();

        //Penambahan dari scenario kali ini mulai dari baris ini
        //membuat 5 data untuk tabel guru, dan disimpan di variabel objek gurus
        $gurus = guru::factory(5)->create();

        //membuat 25 data untuk tabel siswa, dan disimpan di variabel objek siswas
        $siswas = siswa::factory(25)->create();

        //mengambil 3 data secara random dari variabel objek gurus
        $guruRandom = $gurus->random(3);

        //3 guru random dijadikan walas
        foreach ($guruRandom as $guru) {
            walas::factory()->create([
                'idguru' => $guru->idguru
            ]);
        }

        //mengambil data semua walas
        $waliKelasIds = walas::pluck('idwalas')->toArray();

        //mengacak urutan siswa
        $randomSiswas = $siswas->shuffle();

        //mendistribusikan siswa menjadi 3 kelompok sesuai jumlah wali kelas
        $chunks = $randomSiswas->chunk(ceil($randomSiswas->count() /
            count($waliKelasIds)));

        //perulangan tiap wali kelas dan siswanya
        foreach ($waliKelasIds as $index => $idwalas) {
            if (isset($chunks[$index])) {
                foreach ($chunks[$index] as $siswa) {
                    Kelas::create([
                        'idwalas' => $idwalas,
                        'idsiswa' => $siswa->idsiswa
                    ]);
                }
            }
        }
    }
}
