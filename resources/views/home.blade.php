<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>
    <h2>Halo, {{ ucfirst(session('role')) }} {{ $data->nama ?? session('username') }}</h2>
    <a href="{{ route('logout') }}">Logout</a>

    {{-- ROLE GURU --}}
    @if(session('role') == 'guru')
        @php
            // Get teacher with their homeroom class and students
            $guru = \App\Models\guru::with(['walas.kelas.siswa'])->find(session('guru_id'));
            $isWalas = $guru && $guru->walas;
        @endphp

        <h3>Data Guru</h3>
        <p>Selamat datang guru, di sini kamu bisa mengelola data siswa dan jadwal.</p>

        {{-- Informasi Guru --}}
        <h4>Informasi Guru</h4>
        <ul>
            <li>Nama: {{ $data->nama ?? '-' }}</li>
            <li>Mata Pelajaran: {{ $data->mapel ?? '-' }}</li>
        </ul>

        {{-- Jika guru juga wali kelas --}}
        @if($isWalas)
            @php $walas = $guru->walas; @endphp
            <h4>Wali Kelas</h4>
            <ul>
                <li>Kelas: {{ $walas->jenjang ?? '' }} {{ $walas->namakelas ?? '' }}</li>
                <li>Tahun Ajaran: {{ $walas->tahunajaran ?? '-' }}</li>
            </ul>

            {{-- Daftar siswa di kelas tersebut --}}
            <h4>Daftar Siswa</h4>
            @if($walas->kelas && $walas->kelas->count() > 0)
                <table border="1" cellpadding="8" cellspacing="0">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Tinggi Badan</th>
                        <th>Berat Badan</th>
                    </tr>
                    @foreach($walas->kelas as $index => $kelas)
                        @if($kelas->siswa)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $kelas->siswa->nama }}</td>
                                <td>{{ $walas->jenjang }} {{ $walas->namakelas }}</td>
                                <td>{{ $kelas->siswa->tb }} cm</td>
                                <td>{{ $kelas->siswa->bb }} kg</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @else
                <p>Belum ada siswa di kelas ini.</p>
            @endif
        @endif

    {{-- ROLE SISWA --}}
    @elseif(session('role') == 'siswa')
        @php
            // Get student with their class and homeroom teacher information
            $siswa = \App\Models\siswa::with(['kelas.walas.guru'])->find(session('siswa_id'));
            $kelas = $siswa ? $siswa->kelas : null;
            $walas = $kelas ? $kelas->walas : null;
            $guru = $walas ? $walas->guru : null;
        @endphp

        <h3>Data Siswa</h3>
        <p>Selamat datang siswa, di sini kamu bisa melihat nilai dan materi.</p>

        {{-- Informasi Siswa --}}
        <h4>Informasi Siswa</h4>
        <ul>
            <li>Nama: {{ $siswa->nama ?? '-' }}</li>
            <li>NISN: {{ $siswa->id ?? '-' }}</li>
            @if($kelas && $walas)
                <li>Kelas: {{ $walas->jenjang ?? '' }} {{ $walas->namakelas ?? '' }}</li>
                @if($guru)
                    <li>Wali Kelas: {{ $guru->nama ?? '-' }}</li>
                    <li>Mata Pelajaran: {{ $guru->mapel ?? '-' }}</li>
                @endif
            @else
                <li>Kelas: Belum terdaftar di kelas</li>
            @endif
        </ul>

    {{-- ROLE LAIN (misal admin) --}}
    @else
        <h3>Selamat Datang di Sistem</h3>
    @endif

    <hr>

       {{-- Daftar Siswa (untuk semua role) --}}
    <hr>
    <h3>Daftar Semua Siswa</h3>
    @php
        $allSiswa = \App\Models\siswa::with(['kelas.walas'])->get();
        $jadwals = \App\Models\kbm::with(['guru', 'walas'])->get();
    @endphp
    
    @if($allSiswa->count() > 0)
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Tinggi Badan</th>
                <th>Berat Badan</th>
            </tr>
            @foreach($allSiswa as $index => $s)
                @php
                    $kelasSiswa = $s->kelas;
                    $walasSiswa = $kelasSiswa ? $kelasSiswa->walas : null;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $s->nama }}</td>
                    <td>
                        @if($kelasSiswa && $walasSiswa)
                            {{ $walasSiswa->jenjang ?? '' }} {{ $walasSiswa->namakelas ?? '' }}
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
    @endif

    {{-- 📚 Tambahan: Daftar Jadwal KBM --}}
    <hr>
    <h2>📚 Daftar Jadwal KBM</h2>
    @if($jadwals->count() > 0)
        <table border="1" cellpadding="6">
            <tr>
                <th>No</th>
                <th>Guru</th>
                <th>Wali Kelas</th>
                <th>Kelas</th>
                <th>Mapel</th>
                <th>Hari</th>
                <th>Jam</th>
            </tr>
            @foreach($jadwals as $i => $j)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $j->guru->nama ?? '-' }}</td>
                    <td>{{ $j->walas->guru->nama ?? '-' }}</td>
                    <td>{{ $j->walas->nama_kelas ?? '-' }}</td>
                    <td>{{ $j->mapel ?? '-' }}</td>
                    <td>{{ $j->hari ?? '-' }}</td>
                    <td>{{ $j->jam ?? '-' }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p>Belum ada jadwal KBM yang tercatat.</p>
    @endif
</body>
</html>
