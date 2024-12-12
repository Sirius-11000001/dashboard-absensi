<?php
session_start();
include "config-dashboard.php";
require('fpdf/fpdf.php'); // FPDF

// Fungsi untuk mengambil data absensi dan shift
function getDataAbsensi() {
    global $conn;
    $query = "SELECT a.id_attendance, s.shift_name, e.nama as nama_karyawan, a.tanggal, a.jam_masuk, a.jam_pulang, TIMEDIFF(a.jam_pulang, a.jam_masuk) as lama_kerja 
              FROM attendance a 
              JOIN shift s ON a.shift_id = s.id_shift 
              JOIN employees e ON a.karyawan_id = e.id_karyawan";
    $result = mysqli_query($conn, $query);
    return $result;
}

// Fungsi untuk menghapus data absensi
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM attendance WHERE id_attendance = $id";
    mysqli_query($conn, $query);
    header("Location: data-absensi.php");
}

// Fungsi untuk merekap data absensi ke dalam PDF
if (isset($_POST['rekap'])) {
    $dataAbsensi = getDataAbsensi();
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Rekap Data Absensi', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 1);
    $pdf->Cell(30, 10, 'Shift', 1);
    $pdf->Cell(40, 10, 'Nama Karyawan', 1);
    $pdf->Cell(30, 10, 'Tanggal', 1);
    $pdf->Cell(30, 10, 'Jam Masuk', 1);
    $pdf->Cell(30, 10, 'Jam Pulang', 1);
    $pdf->Cell(30, 10, 'Lama Kerja', 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    while ($row = mysqli_fetch_assoc($dataAbsensi)) {
        $pdf->Cell(10, 10, $no++, 1);
        $pdf->Cell(30, 10, $row['shift_name'], 1);
        $pdf->Cell(40, 10, $row['nama_karyawan'], 1);
        $pdf->Cell(30, 10, $row['tanggal'], 1);
        $pdf->Cell(30, 10, $row['jam_masuk'], 1);
        $pdf->Cell(30, 10, $row['jam_pulang'], 1);
        $pdf->Cell(30, 10, $row['lama_kerja'], 1);
        $pdf->Ln();
    }
    $pdf->Output('D', 'Rekap_Data_Absensi.pdf');
    exit;
}

$dataAbsensi = getDataAbsensi();
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
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/data-absensi-style.css">
  </head>
  <body>
    <!-- sidebar -->
    <?php include "sidebar.php"; ?> 
    
    <!-- header -->
    <header class="header-dashboard">
      <div id="current-time" class="current-time"></div>
    </header>

    <!-- Main Content -->
    <div class="container mt-5">
        <span class="nav-icon material-symbols-rounded">check_circle</span>
        <span>Data Absensi</span>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <button class="btn btn-primary">Filter Data</button>
                <form method="POST" action="">
                    <button type="submit" name="rekap" class="btn btn-success">Rekap Data</button>
                </form>
            <?php } ?>
            <input type="text" class="form-control w-25" placeholder="Search">
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Shift</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Lama Kerja</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($dataAbsensi)) { 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['shift_name']; ?></td>
                        <td><?php echo $row['nama_karyawan']; ?></td>
                        <td><?php echo $row['tanggal']; ?></td>
                        <td><?php echo $row['jam_masuk']; ?></td>
                        <td><?php echo $row['jam_pulang']; ?></td>
                        <td><?php echo $row['lama_kerja']; ?></td>
                        <td>
                            <?php if ($_SESSION['role'] == 'admin') { ?>
                            <form method="POST" action="">
                                <input type="hidden" name="id" value="<?php echo $row['id_attendance']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- footer -->
    <?php include "footer.php"; ?> 

    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>
