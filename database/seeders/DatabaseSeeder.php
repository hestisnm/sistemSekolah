<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        // ... (existing code)

        // After creating walas and before distributing students
        $waliKelasIds = walas::pluck('idwalas')->toArray();

        // Get all guru IDs
        $guruIds = \App\Models\guru::pluck('idguru')->toArray();

        if (!empty($guruIds) && !empty($waliKelasIds)) {
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            $times = [
                ['07:00', '08:30'],
                ['08:30', '10:00'],
                ['10:00', '11:30'],
                ['13:00', '14:30'],
                ['14:30', '16:00']
            ];

            // Clear existing KBM data
            DB::table('datakbm')->truncate();

            // Create multiple schedules per teacher, allowing multiple gurus per walas
            $usedGurus = [];
            
            // Number of schedules to create per teacher (adjust as needed)
            $schedulesPerTeacher = 2;

            foreach ($guruIds as $guruId) {
                // Skip if this guru already has the maximum number of schedules
                if (isset($usedGurus[$guruId]) && $usedGurus[$guruId] >= $schedulesPerTeacher) {
                    continue;
                }

                // Select a random walas for this schedule
                $idwalas = $waliKelasIds[array_rand($waliKelasIds)];

                // Track how many schedules this guru has
                if (!isset($usedGurus[$guruId])) {
                    $usedGurus[$guruId] = 0;
                }
                $usedGurus[$guruId]++;

                // Create schedule
                $time = $times[array_rand($times)];
                $day = $days[array_rand($days)];

                DB::table('datakbm')->insert([
                    'idguru' => $guruId,
                    'idwalas' => $idwalas,
                    'hari' => $day,
                    'mulai' => $time[0],
                    'selesai' => $time[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info('KBM data seeded successfully!');
        }

        // Continue with student distribution
        $randomSiswas = $siswas->shuffle();
        // ... (rest of your existing code)

        //mengambil data semua walas
        $waliKelasIds = walas::pluck('idwalas')->toArray();

        //mengacak urutan siswa
        $randomSiswas = $siswas->shuffle();

        //mendistribusikan siswa menjadi 3 kelompok sesuai jumlah wali kelas
        $chunks = $randomSiswas->chunk(ceil($randomSiswas->count() /
            count($waliKelasIds)));

        // Get all walas with their data
        $allWalas = walas::with('guru')->get();

        //perulangan tiap wali kelas dan siswanya
        foreach ($waliKelasIds as $index => $idwalas) {
            // Find the walas data including the guru relationship
            $walas = $allWalas->firstWhere('idwalas', $idwalas);

            if (isset($chunks[$index]) && $walas) {
                foreach ($chunks[$index] as $siswa) {
                    Kelas::create([
                        'idwalas' => $idwalas,
                        'idsiswa' => $siswa->idsiswa,
                        'namakelas' => $walas->namakelas ?? 'Kelas ' . ($index + 1) // Use the walas's namakelas or generate one
                    ]);
                }
            }
        }

        // Create KBM records after all required relations exist
        kbm::factory(25)->create();
    }
}
