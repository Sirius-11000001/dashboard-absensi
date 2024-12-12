<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Koneksi ke database
include "config-dashboard.php";

// Setel ke zona waktu default
date_default_timezone_set('Asia/Jakarta');


// Mengambil data karyawan yang belum pulang
$belum_pulang_query = "
    SELECT e.id_karyawan, e.nama 
    FROM attendance a 
    JOIN employees e ON a.karyawan_id = e.id_karyawan 
    WHERE a.jam_pulang = '00:00:00' OR a.jam_pulang IS NULL
";
$belum_pulang_result = mysqli_query($conn, $belum_pulang_query);

// Mengambil data karyawan yang belum absen hari ini
$belum_absen_query = "
    SELECT e.id_karyawan, e.nama 
    FROM employees e 
    LEFT JOIN attendance a ON e.id_karyawan = a.karyawan_id AND a.tanggal = CURDATE()
    WHERE a.id_attendance IS NULL
";
$belum_absen_result = mysqli_query($conn, $belum_absen_query);

// Mengambil data karyawan yang sudah masuk hari ini
$sudah_masuk_query = "
    SELECT e.id_karyawan, e.nama 
    FROM attendance a 
    JOIN employees e ON a.karyawan_id = e.id_karyawan 
    WHERE a.tanggal = CURDATE() AND a.jam_masuk IS NOT NULL
";
$sudah_masuk_result = mysqli_query($conn, $sudah_masuk_query);

// Menghitung total absensi hari ini
$total_absensi_query = "
    SELECT COUNT(*) AS total_absensi 
    FROM attendance 
    WHERE tanggal = CURDATE()
";
$total_absensi_result = mysqli_query($conn, $total_absensi_query);
$total_absensi = mysqli_fetch_assoc($total_absensi_result)['total_absensi'];

// Menghitung total karyawan
$total_karyawan_query = "
    SELECT COUNT(*) AS total_karyawan 
    FROM employees
";
$total_karyawan_result = mysqli_query($conn, $total_karyawan_query);
$total_karyawan = mysqli_fetch_assoc($total_karyawan_result)['total_karyawan'];

// Menghitung absensi saya (contoh untuk karyawan dengan id_karyawan = 1)
$absensi_saya_query = "
    SELECT COUNT(*) AS absensi_saya 
    FROM attendance 
    WHERE karyawan_id = 1 AND tanggal = CURDATE()
";
$absensi_saya_result = mysqli_query($conn, $absensi_saya_query);
$absensi_saya = mysqli_fetch_assoc($absensi_saya_result)['absensi_saya'];
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/main-style.css">
  </head>
  <body>
    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Header -->
    <header class="header-dashboard">
        <div id="current-time" class="current-time"></div>
        <div class="halo">
        <h5><strong>
            Selamat datang, 
            <?php 
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'employee') {
                echo htmlspecialchars($nama);
            } else if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                echo htmlspecialchars($_SESSION['username']);
            } else {
                echo "Pengguna Tidak Dikenal";
            }
            ?>
            </strong></h5>
        </div>
    </header>


    <!-- sesi hero -->
    <div id="hero" class="hero">
        <!-- hero 1 -->
        <div class="main-hero1">
            <h5 class="nav-icon material-symbols-rounded">Dashboard</h5>
            <span>Dashboard</span>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-info row">
      <div class="card bg-danger col">
          <div class="card-body">
              <h4 class="card-title"><?php echo $absensi_saya; ?></h4>
              <p class="card-text">Absensi Saya</p>
              <a href="absensi-saya.php" class="text-black">More..</a>
          </div>
      </div>
      <div class="card bg-primary col">
          <div class="card-body">
              <h4 class="card-title"><?php echo $total_absensi; ?></h4>
              <p class="card-text">Total Absensi Hari Ini</p>
              <a href="total-absensi.php" class="text-black">More..</a>
          </div>
      </div>
      <div class="card bg-success col">
          <div class="card-body">
              <h4 class="card-title"><?php echo $total_karyawan; ?></h4>
              <p class="card-text">Total Karyawan</p>
              <a href="total-karyawan.php" class="text-black">More..</a>
          </div>
      </div>
    </div>
    
    <!-- Data Tables -->
    <div class="row">
        <div class="col-md-12 data-table">
            <!-- Belum Absen Hari Ini -->
            <div class="card">
                <div class="card-header text-center">
                    <strong>BELUM ABSEN HARI INI</strong>
                </div>
                <div class="card-body table-responsive text-center">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Nama Karyawan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($belum_absen_result && $belum_absen_result->num_rows > 0) {
                                while ($row = $belum_absen_result->fetch_assoc()) {
                                    echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['id_karyawan']}</td>
                                    <td>{$row['nama']}</td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='3'>Semua karyawan telah absen hari ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sudah Absen Hari Ini -->
        <div class="col-md-12 data-table">
            <div class="card">
                <div class="card-header text-center">
                    <strong>SUDAH MASUK HARI INI</strong>
                </div>
                <div class="card-body table-responsive text-center">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Nama Karyawan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($sudah_masuk_result && $sudah_masuk_result->num_rows > 0) {
                                while ($row = $sudah_masuk_result->fetch_assoc()) {
                                    echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['id_karyawan']}</td>
                                    <td>{$row['nama']}</td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='3'>Belum ada karyawan yang absen masuk hari ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Belum Absen Pulang -->
        <div class="col-md-12 data-table">
            <div class="card">
                <div class="card-header text-center">
                    <strong>BELUM ABSEN PULANG</strong>
                </div>
                <div class="card-body table-responsive text-center">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Nama Karyawan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($belum_pulang_result && $belum_pulang_result->num_rows > 0) {
                                while ($row = $belum_pulang_result->fetch_assoc()) {
                                    echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['id_karyawan']}</td>
                                    <td>{$row['nama']}</td>
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='3'>Semua karyawan telah absen pulang hari ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "footer.php"; ?>

    <!-- component-->
    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>