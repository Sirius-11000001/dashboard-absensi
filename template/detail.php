<?php
// Include koneksi ke database
include "config-dashboard.php";

// Cek jika ada ID karyawan yang diterima
if (isset($_GET['id'])) {
    $karyawan_id = intval($_GET['id']);
    
    // Ambil data karyawan berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $karyawan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data_karyawan = $result->fetch_assoc();

    // Ambil data gaji karyawan
    $stmt_gaji = $conn->prepare("SELECT * FROM data_gajian WHERE karyawan_id = ?");
    $stmt_gaji->bind_param("i", $karyawan_id);
    $stmt_gaji->execute();
    $result_gaji = $stmt_gaji->get_result();

    // Ambil data absensi karyawan
    $stmt_absensi = $conn->prepare("SELECT a.*, s.shift_name FROM attendance a JOIN data_shift s ON a.shift_id = s.id WHERE a.karyawan_id = ?");
    $stmt_absensi->bind_param("i", $karyawan_id);
    $stmt_absensi->execute();
    $result_absensi = $stmt_absensi->get_result();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/detail-style.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Header -->
    <header class="header-dashboard">
        <div id="current-time" class="current-time"></div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <?php if ($data_karyawan): ?>
            <h3><?php echo $data_karyawan['name']; ?></h3>
            <p>Email: <?php echo $data_karyawan['email']; ?></p>
            <p>Jabatan: <?php echo isset($data_karyawan['jabatan']) ? $data_karyawan['jabatan'] : 'N/A'; ?></p>

            <h4>Data Gaji</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Potongan</th>
                        <th>Total Gaji</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_gaji = $result_gaji->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row_gaji['tanggal']; ?></td>
                            <td>Rp. <?php echo number_format($row_gaji['gaji_pokok'], 0, ',', '.'); ?></td>
                            <td>Rp. <?php echo number_format($row_gaji['tunjangan'], 0, ',', '.'); ?></td>
                            <td>Rp. <?php echo number_format($row_gaji['potongan'], 0, ',', '.'); ?></td>
                            <td>Rp. <?php echo number_format($row_gaji['total_gaji'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4>Data Absensi</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Shift</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_absensi = $result_absensi->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row_absensi['tanggal']; ?></td>
                            <td><?php echo $row_absensi['shift_name']; ?></td>
                            <td><?php echo $row_absensi['jam_masuk']; ?></td>
                            <td><?php echo $row_absensi['jam_pulang']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Data karyawan tidak ditemukan.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include "footer.php"; ?>

    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
