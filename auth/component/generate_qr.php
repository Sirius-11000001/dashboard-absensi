<?php
// Koneksi ke database
include "connector.php";
include "phpqrcode/qrlib.php"; // Include library PHP QR Code

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_karyawan = $_POST['id_karyawan'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $photo = $_FILES['photo']['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($photo);

    // Periksa apakah direktori uploads ada, jika tidak buat direktori tersebut
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Upload foto
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        // Simpan data ke tabel employees
        $sql = "INSERT INTO employees (id_karyawan, nama, jabatan, dob, email, photo) VALUES ('$id_karyawan', '$nama', '$jabatan', '$dob', '$email', '$photo')";
        if ($conn->query($sql) === TRUE) {
            // Simpan data ke tabel users dengan role_id untuk employee
            $role_id = 2; // ID untuk role 'employee'
            $sql_user = "INSERT INTO users (username, password, id_karyawan, role_id) VALUES ('$username', '$password', '$id_karyawan', '$role_id')";
            if ($conn->query($sql_user) === TRUE) {
                // Generate QR code
                $qrContent = "ID: $id_karyawan\nName: $nama\nJabatan: $jabatan";
                $qrFileName = 'qrcodes/' . $id_karyawan . '_' . $nama . '.png';

                // Periksa apakah direktori qrcodes ada, jika tidak buat direktori tersebut
                if (!is_dir('qrcodes')) {
                    mkdir('qrcodes', 0755, true);
                }

                QRcode::png($qrContent, $qrFileName);

                // Periksa apakah file QR code berhasil dibuat
                if (file_exists($qrFileName)) {
                    // Simpan path QR code ke database
                    $sql = "UPDATE employees SET qr_code='$qrFileName' WHERE id_karyawan='$id_karyawan'";
                    if ($conn->query($sql) === TRUE) {
                        echo "QR code created and saved successfully: $qrFileName";
                    } else {
                        echo "Failed to save QR code to database: " . $conn->error;
                    }
                } else {
                    echo "Failed to create QR code: $qrFileName";
                }

                // Redirect ke halaman scan
                header('Location: qr.php');
                exit();
            } else {
                echo "Error: " . $sql_user . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR Code</title>
    <link rel="stylesheet" href="style/style-register.css">
</head>
<body>
    <h2>REGISTER EMPLOYEE</h2>
    <form method="POST" enctype="multipart/form-data">
        <!-- biodata karyawan -->
        <h1 class="biodata" align="center"><strong>BIODATA</strong></h1>
        <label for="id_karyawan">ID Karyawan:</label>
        <input type="text" id="id_karyawan" name="id_karyawan" required><br>
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" required><br>
        <label for="jabatan">Jabatan:</label>
        <input type="text" id="jabatan" name="jabatan" required><br>
        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="photo">Photo:</label>
        <input type="file" id="photo" name="photo" required><br>
        <!-- akun login karyawan -->
        <h1 class="login" align="center">AKUN LOGIN</h1>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <div class="field">
            <input type="checkbox" id="show-password" onclick="togglePassword()"> Show Password
        </div>
        <button type="submit">Register</button>
    </form>
    <script>
        function togglePassword() {
        var passwordField = document.getElementById("password");
        var checkbox = document.getElementById("show-password");

        if (checkbox.checked) {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
    </script>
</body>
</html>
