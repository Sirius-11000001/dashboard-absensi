<?php
// Koneksi ke database
include "config-dashboard.php";

// set zona waktu default
date_default_timezone_set('Asia/Jakarta'); // Setel ke zona waktu Indonesia

// query untuk mendapatkan data kehadiran hari ini
$attendance_query = "
    SELECT e.id_karyawan, e.nama, 
           a.jam_masuk AS waktu_masuk,
           a.jam_pulang AS waktu_pulang,
           s.shift_name AS shift
    FROM employees e
    JOIN attendance a ON e.id_karyawan = a.karyawan_id
    JOIN shift s ON a.shift_id = s.id_shift
    WHERE DATE(a.tanggal) = CURDATE()
";

$attendance_result = mysqli_query($conn, $attendance_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/total-absensi-style.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Header -->
    <header class="header-dashboard">
      <div id="current-time" class="current-time"><span><strong>Detail Absensi Anda</strong></span></div>
    </header>

    <!-- Tabel Detail Absensi -->
    <div class="row">
        <div class="col-md-12 data-table">
            <div class="card">
                <div class="card-header text-center">
                    <h3><strong>Data Absensi Karyawan</strong></h3>
                </div>
                <div class="card-body table-responsive text-center">
                    <table border="1" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Karyawan</th>
                                <th>Nama</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Pulang</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($attendance_result && $attendance_result->num_rows > 0) {
                                // Tampilkan data karyawan
                                while($row = $attendance_result->fetch_assoc()) {
                                    $id_karyawan = $row['id_karyawan'];
                                    $nama = $row['nama'];
                                    $waktu_masuk = $row['waktu_masuk'];
                                    $waktu_pulang = $row['waktu_pulang'];
                                    $shift = $row['shift'];

                                    echo "<tr>
                                            <td>{$id_karyawan}</td>
                                            <td>{$nama}</td>
                                            <td>{$waktu_masuk}</td>
                                            <td>{$waktu_pulang}</td>
                                            <td>{$shift}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Tidak ada karyawan yang telah absen masuk dan pulang hari ini</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "footer.php";?>

    <!-- Library -->
    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
