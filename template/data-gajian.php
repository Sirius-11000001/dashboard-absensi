<?php
// koneksi
include "config-dashboard.php";

// Tambahkan data ke tabel salary jika diperlukan (opsional)
// Menghapus data gaji dari tabel salary
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt_delete = $conn->prepare("DELETE FROM salary WHERE id_salary = ?");
    $stmt_delete->bind_param("i", $delete_id);

    if ($stmt_delete->execute()) {
        echo "<script>alert('Data gaji berhasil dihapus!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt_delete->error . "');</script>";
    }
    $stmt_delete->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Gajian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/data-gajian-style.css">
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
        <h2>Data Gaji <small>Semua data gaji akan muncul disini</small></h2>
        <div class="table-container">
            <div class="table-header text-center">
                <h4><strong>DATA GAJI KARYAWAN</strong></h4>
            </div>
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal Gaji</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Bonus</th>
                        <th>Potongan</th>
                        <th>Total Gaji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Ambil data gaji dari tabel salary
                    $query = "SELECT s.*, e.nama as nama_karyawan 
                              FROM salary s 
                              JOIN employees e ON s.karyawan_id = e.id_karyawan";
                    $result_gajian = mysqli_query($conn, $query);
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result_gajian)) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama_karyawan']}</td>
                                <td>{$row['tanggal_gaji']}</td>
                                <td>Rp. " . number_format($row['gaji_pokok'], 0, ',', '.') . "</td>
                                <td>Rp. " . number_format($row['tunjangan'], 0, ',', '.') . "</td>
                                <td>Rp. " . number_format($row['bonus'], 0, ',', '.') . "</td>
                                <td>Rp. " . number_format($row['potongan'], 0, ',', '.') . "</td>
                                <td>Rp. " . number_format($row['total_gaji'], 0, ',', '.') . "</td>
                                <td>
                                    <a href='detail.php?id={$row['karyawan_id']}' class='btn btn-info btn-sm'>Detail</a>
                                    <a href='data-gajian.php?delete_id={$row['id_salary']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Delete</a>
                                </td>
                            </tr>";
                        $no++;
                    }
                ?>
                </tbody>
            </table>
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
