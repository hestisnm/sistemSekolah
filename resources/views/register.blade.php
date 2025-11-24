<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
</head>

<body>
    <h2>Register</h2>
    @if(session('errors'))
        <p style="color:red">{{ session('errors') }}</p>
    @endif
    <form method="POST" action="{{ route('register.post') }}">
        @csrf
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <label><input type="radio" name="role" value="admin" oninput="showChoice(this.value)" required>Admin</label>
        <label><input type="radio" name="role" value="guru" oninput="showChoice(this.value)">Guru</label>
        <label><input type="radio" name="role" value="siswa" oninput="showChoice(this.value)">Siswa</label>
        <div id="guru_fields" style="display:none;">
            <input type="text" name="nama_guru" placeholder="Nama Guru"><br>
            <input type="text" name="mapel" placeholder="Mapel"><br>
        </div>
        <div id="siswa_fields" style="display:none;">
            <input type="text" name="nama_siswa" placeholder="Nama Siswa"><br>
            <input type="number" name="tb" placeholder="Tinggi Badan"><br>
            <input type="number" name="bb" placeholder="Berat Badan"><br>
        </div>
        <button type="submit">Register</button>
    </form>

    <script>
        function showChoice(role) {
            document.getElementById('guru_fields').style.display = 'none';
            document.getElementById('siswa_fields').style.display = 'none';

            if (role === 'guru') {
                document.getElementById('guru_fields').style.display = 'block';
            } else if (role === 'siswa') {
                document.getElementById('siswa_fields').style.display = 'block';
            }
        }
    </script>
</body>
</html>