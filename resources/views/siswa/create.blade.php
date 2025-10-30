<!DOCTYPE html>
<html>

<head>
    <title>Tambah Siswa</title>
</head>

<body>
    <h2>Tambah Siswa</h2>
    <form method="POST" action="{{ route('siswa.store') }}">
        @csrf
        <input type="text" name="nama" placeholder="Nama" required><br>
        <input type="number" name="tb" placeholder="Tinggi Badan (cm)" required><br>
        <input type="number" name="bb" placeholder="Berat Badan (kg)" required><br>
        <button type="submit">Simpan</button>
    </form>
</body>

</html>