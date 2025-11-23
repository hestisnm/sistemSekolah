<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

{{-- ========================================================= --}}
{{-- LOAD SEMUA DATA PALING ATAS (WAJIB AGAR ADMIN TIDAK ERROR) --}}
{{-- ========================================================= --}}
@php
    use App\Models\siswa;
    use App\Models\guru;
    use App\Models\kbm;

    // Data siswa lengkap
    $allSiswa = siswa::with(['kelas.walas'])->get();

    // Semua jadwal
    $jadwals = kbm::with(['guru', 'walas'])->get();

    $role = session('role');
@endphp


<h2>Halo, {{ ucfirst($role) }} {{ $data->nama ?? session('username') }}</h2>
<a href="{{ route('logout') }}">Logout</a>
<hr>


{{-- ========================================================= --}}
{{-- ====================== ROLE GURU ========================= --}}
{{-- ========================================================= --}}
@if($role == 'guru')

    @php
        $guru = guru::with(['walas.kelas.siswa'])->find(session('guru_id'));
        $isWalas = $guru && $guru->walas;
    @endphp

    <h3>Data Guru</h3>
    <ul>
        <li>Nama: {{ $guru->nama ?? '-' }}</li>
        <li>Mata Pelajaran: {{ $guru->mapel ?? '-' }}</li>
    </ul>

    {{-- Jika guru wali kelas --}}
    @if($isWalas)
        @php $walas = $guru->walas; @endphp

        <h4>Wali Kelas</h4>
        <ul>
            <li>Kelas: {{ $walas->jenjang }} {{ $walas->nama_kelas }}</li>
            <li>Tahun Ajaran: {{ $walas->tahunajaran }}</li>
        </ul>

        <h4>Daftar Siswa di Kelas Ini</h4>
        @if($walas->kelas->count() > 0)
            <table border="1" cellpadding="8">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tinggi Badan</th>
                    <th>Berat Badan</th>
                </tr>

                @foreach($walas->kelas as $i => $kelas)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $kelas->siswa->nama }}</td>
                        <td>{{ $kelas->siswa->tb }} cm</td>
                        <td>{{ $kelas->siswa->bb }} kg</td>
                    </tr>
                @endforeach
            </table>
        @else
            <p>Belum ada siswa.</p>
        @endif
    @endif



{{-- ========================================================= --}}
{{-- ====================== ROLE SISWA ======================== --}}
{{-- ========================================================= --}}
@elseif($role == 'siswa')

    @php
        $siswa = siswa::with(['kelas.walas.guru'])->find(session('siswa_id'));
        $kelas = $siswa->kelas;
        $walas = $kelas ? $kelas->walas : null;
        $guruWalas = $walas ? $walas->guru : null;
    @endphp

    <h3>Data Siswa</h3>

    <ul>
        <li>Nama: {{ $siswa->nama }}</li>

        @if($kelas && $walas)
            <li>Kelas: {{ $walas->jenjang }} {{ $walas->nama_kelas }}</li>
            <li>Wali Kelas: {{ $guruWalas->nama }}</li>
        @else
            <li>Kelas: Belum ada</li>
        @endif
    </ul>



{{-- ========================================================= --}}
{{-- ====================== ROLE ADMIN ======================== --}}
{{-- ========================================================= --}}
@elseif($role == 'admin')

    <h3>Dashboard Admin</h3>
    <p>Silakan kelola data siswa.</p>

    {{-- Tombol tambah --}}
    <a href="{{ route('siswa.create') }}">+ Tambah Siswa</a>
    <br><br>

    <p><label>Cari Siswa: </label><input type="text" id="search" placeholder="Ketik nama..."></p>

    {{-- Tabel CRUD --}}
    <table border="1" cellpadding="8" id="tabel-siswa">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>TB</th>
                <th>BB</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- Data will be loaded by AJAX --}}
        </tbody>
    </table>

@endif


{{-- ========================================================= --}}
{{-- =========== DAFTAR SEMUA SISWA (untuk semua role) ======= --}}
{{-- ========================================================= --}}
{{-- <hr>
<h3>Daftar Semua Siswa</h3>

@if($allSiswa->count())
<table border="1" cellpadding="8">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>TB</th>
        <th>BB</th>
    </tr>

    @foreach($allSiswa as $i => $s)
        @php
            $kelas = $s->kelas;
            $walas = $kelas ? $kelas->walas : null;
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $s->nama }}</td>
            <td>
                @if($walas)
                    {{ $walas->jenjang }} {{ $walas->namakelas }}
                @else
                    Belum ada kelas
                @endif
            </td>
            <td>{{ $s->tb }} cm</td>
            <td>{{ $s->bb }} kg</td>
        </tr>
    @endforeach
</table>
@else
<p>Tidak ada data siswa.</p>
@endif --}}



{{-- ========================================================= --}}
{{-- ======================= JADWAL KBM ======================= --}}
{{-- ========================================================= --}}
<hr>
<h3>📚 Jadwal KBM</h3>
<p><label>Cari Jadwal: </label><input type="text" id="search-kbm" placeholder="Ketik mapel atau guru"></p>

<table border="1" cellpadding="8" id="tabel-kbm">
    <thead>
        <tr>
            <th>No</th>
            @if($role != 'guru')
                <th>Guru</th>
            @endif
            <th>Mapel</th>
            @if($role != 'siswa')
                <th>Kelas</th>
            @endif
            <th>Hari</th>
            <th>Mulai</th>
            <th>Selesai</th>
        </tr>
    </thead>
    <tbody>
        {{-- KBM data will be loaded by AJAX --}}
    </tbody>
</table>




<script>
$(document).ready(function(){
    function renderTable(data) {
        let rows = '';
        if (data.length === 0) {
            rows = '<tr><td colspan="5">Tidak ada data ditemukan</td></tr>';
        } else {
            data.forEach((s, index) => {
                rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${s.nama}</td>
                    <td>${s.tb}</td>
                    <td>${s.bb}</td>
                    <td>
                        <a href="/siswa/edit/${s.idsiswa}">Edit</a> |
                        <a href="/siswa/delete/${s.idsiswa}" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                `;
            });
        }
        $('#tabel-siswa tbody').html(rows);
    }

    function loadSiswa() {
        $.ajax({
            url: "{{ route('siswa.data') }}",
            method: "GET",
            success: function(response) {
                renderTable(response);
            },
            error: function() {
                alert('Gagal memuat data siswa.');
            }
        });
    }

    function searchSiswa(keyword) {
        $.ajax({
            url: "{{ route('siswa.search') }}",
            method: "GET",
            data: { q: keyword },
            success: function(response) {
                renderTable(response);
            },
            error: function() {
                console.error('Gagal mencari data siswa.');
            }
        });
    }

    // Initial load
    if ($('#tabel-siswa').length) {
        loadSiswa();
    }

    // Search functionality
    $('#search').on('keyup', function() {
        const keyword = $(this).val().trim();
        if (keyword.length > 0) {
            searchSiswa(keyword);
        } else {
            loadSiswa();
        }
    });

    function renderKbmTable(data) {
        let rows = '';
        const userRole = '{{ $role }}';
        if (data.length === 0) {
            const colspan = userRole === 'admin' ? 7 : 6;
            rows = `<tr><td colspan="${colspan}">Tidak ada jadwal.</td></tr>`;
        } else {
            data.forEach((j, index) => {
                rows += `
                <tr>
                    <td>${index + 1}</td>
                    ${userRole !== 'guru' ? `<td>${j.guru.nama}</td>` : ''}
                    <td>${j.guru.mapel}</td>
                    ${userRole !== 'siswa' ? `<td>${j.walas.jenjang} ${j.walas.nama_kelas}</td>` : ''}
                    <td>${j.hari}</td>
                    <td>${j.mulai}</td>
                    <td>${j.selesai}</td>
                </tr>
                `;
            });
        }
        $('#tabel-kbm tbody').html(rows);
    }

    function loadKbm() {
        $.ajax({
            url: "{{ route('kbm.data') }}",
            method: "GET",
            success: function(response) {
                renderKbmTable(response);
            },
            error: function() {
                alert('Gagal memuat jadwal KBM.');
            }
        });
    }

    // Load KBM data
    if ($('#tabel-kbm').length) {
        loadKbm();
    }

    function searchKbm(keyword) {
        $.ajax({
            url: "{{ route('kbm.data') }}",
            method: "GET",
            data: { q: keyword },
            success: function(response) {
                renderKbmTable(response);
            },
            error: function() {
                console.error('Gagal mencari jadwal KBM.');
            }
        });
    }

    $('#search-kbm').on('keyup', function() {
        const keyword = $(this).val().trim();
        if (keyword.length > 0) {
            searchKbm(keyword);
        } else {
            loadKbm();
        }
    });
});
</script>
</body>
</html>
