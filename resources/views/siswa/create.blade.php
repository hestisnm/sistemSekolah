<h2>Tambah Siswa</h2>

<form action="{{ route('siswa.store') }}" method="POST">
    @csrf
    Username: <input type="text" name="nama"><br>
    Nama: <input type="text" name="nama"><br>
    Tinggi Badan: <input type="number" name="tb"><br>
    Berat Badan: <input type="number" name="bb"><br>

    <button type="submit">Simpan</button>
</form>
