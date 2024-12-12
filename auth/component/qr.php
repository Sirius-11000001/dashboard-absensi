<?php
// Koneksi ke database
include "connector.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="style/qr-style.css">
</head>
<body>

<!-- header -->
<header class="header-dashboard">
  <div id="current-time" class="current-time"><span><strong>Selamat datang admin!</strong></span></div>
</header>

<div class="row">
    <div class="col-md-12 data-table">
        <div class="card">
            <div class="card-header text-center">
                <h3><strong>TABLE KARYAWAN</strong></h3>
            </div>
            <div class="card-body table-responsive text-center">
                <table border="1" class="table table-striped">
                    <tr>
                        <th>ID Karyawan</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Date of Birth</th>
                        <th>Email</th>
                        <th>Photo</th>
                        <th>QR Code</th>
                    </tr>
                    <?php
                    // Ambil data karyawan dari database
                    $sql = "SELECT id_karyawan, nama, jabatan, dob, email, photo FROM employees";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Tampilkan data karyawan
                        while($row = $result->fetch_assoc()) {
                            $id_karyawan = $row['id_karyawan'];
                            $nama = $row['nama'];
                            $jabatan = $row['jabatan'];
                            $dob = $row['dob'];
                            $email = $row['email'];
                            $photo = $row['photo'];
                            $qrFileName = 'qrcodes/' . $id_karyawan . '_' . $nama . '.png';

                            // Periksa apakah file QR code ada
                            if (file_exists($qrFileName)) {
                                $qrImageTag = "<img src='{$qrFileName}' alt='QR Code for {$nama}' width='100'>";
                            } else {
                                $qrImageTag = "QR code not found: {$qrFileName}";
                            }

                            echo "<tr>
                                    <td>{$id_karyawan}</td>
                                    <td>{$nama}</td>
                                    <td>{$jabatan}</td>
                                    <td>{$dob}</td>
                                    <td>{$email}</td>
                                    <td><img src='uploads/{$photo}' alt='{$nama}' width='100'></td>
                                    <td>{$qrImageTag}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No employees found</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="field text-center">Already Register? Scan <a href="http://127.0.0.1:5000">Here</a></div>

<!-- library -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="http://localhost/dashboard-absensi/auth/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
