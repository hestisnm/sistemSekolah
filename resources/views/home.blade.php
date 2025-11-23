<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
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

    {{-- Tabel CRUD --}}
    <table border="1" cellpadding="8">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>TB</th>
            <th>BB</th>
            <th>Aksi</th>
        </tr>

        @foreach($allSiswa as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->nama }}</td>
                <td>{{ $s->tb }}</td>
                <td>{{ $s->bb }}</td>
                <td>
                    <a href="{{ route('siswa.edit', $s->idsiswa) }}">Edit</a> |
                    <form action="{{ route('siswa.delete', $s->idsiswa) }}" 
                          method="GET"
                          style="display:inline">
                        <button onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
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

@php
    $filtered = collect();

    if ($role == 'guru') {
        $filtered = $jadwals->where('idguru', session('guru_id'));
    } elseif ($role == 'siswa') {
        if ($siswa && $kelas) {
            $filtered = $jadwals->where('idwalas', $kelas->idwalas);
        }
    } else {
        $filtered = $jadwals;
    }
@endphp


@if($filtered->count())
<table border="1" cellpadding="8">
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

    @foreach($filtered as $i => $j)
        <tr>
            <td>{{ $i + 1 }}</td>
            @if($role != 'guru')
                <td>{{ $j->guru->nama}}</td>
            @endif
            <td>{{ $j->guru->mapel}}</td>
            @if($role != 'siswa')
                <td>{{ $j->walas->jenjang }} {{ $j->walas->nama_kelas }}</td>
            @endif
            <td>{{ $j->hari }}</td>
            <td>{{ $j->mulai }}</td>
            <td>{{ $j->selesai }}</td>
        </tr>
    @endforeach
</table>
@else
<p>Tidak ada jadwal.</p>
@endif




</body>
</html>
