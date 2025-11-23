<!DOCTYPE html>
<html>


<body>
    <h2>Edit Siswa</h2>

<form action="{{ route('siswa.update', $siswa->idsiswa) }}" method="POST">
    @csrf
    @method('PUT')

    Nama: <input type="text" name="nama" value="{{ $siswa->nama }}"><br>
    Tinggi Badan: <input type="number" name="tb" value="{{ $siswa->tb }}"><br>
    Berat Badan: <input type="number" name="bb" value="{{ $siswa->bb }}"><br>

    <button type="submit">Update</button>
</form>


</body>

</html>