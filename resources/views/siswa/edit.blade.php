<!DOCTYPE html>
<html>

<head>
    <title>Edit Siswa</title>
</head>

<body>
    <h2>Edit Siswa</h2>

    <form method="POST" action="{{ route('siswa.update', $siswa->id) }}">
        @csrf
        <input type="text" name="nama" value="{{ $siswa->nama }}" required><br>
        <input type="number" name="tb" value="{{ $siswa->tb }}" required><br>
        <input type="number" name="bb" value="{{ $siswa->bb }}" required><br>
        <button type="submit">Update</button>
    </form>

</body>

</html>