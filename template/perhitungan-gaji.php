<?php 
// Include koneksi ke database
include "config-dashboard.php";
include "auth.php";
checkAdmin();

// Mengambil data gaji karyawan dari database
$sql = "SELECT g.*, e.nama AS nama_lengkap 
        FROM salary g
        JOIN employees e ON g.karyawan_id = e.id_karyawan";
$result = $conn->query($sql);

// Proses untuk menambah gaji karyawan
if (isset($_POST['submit_gaji'])) {
    $karyawan_id_input = trim($_POST['karyawan_id']);
    
    // validasi karyawan_id (cek jika karyawan ada berdasarkan pada id_karyawan)
    $stmt_karyawan = $conn->prepare("SELECT id_karyawan FROM employees WHERE id_karyawan = ?");
    $stmt_karyawan->bind_param("s", $karyawan_id_input);
    $stmt_karyawan->execute();
    $result_karyawan = $stmt_karyawan->get_result();

    if ($result_karyawan->num_rows == 0) {
        echo "<script>alert('Karyawan tidak ditemukan.');</script>";
    } else {
        $row_karyawan = $result_karyawan->fetch_assoc();
        $karyawan_id = $row_karyawan['id_karyawan']; // Ambil id karyawan
        $stmt_karyawan->close(); // Close setelah selesai

        // Ambil total potongan dan total tunjangan dari tabel salary_criteria
        $sql_potongan = "SELECT SUM(jumlah) AS total_potongan FROM salary_criteria WHERE jenis = 'Potongan'";
        $sql_tunjangan = "SELECT SUM(jumlah) AS total_tunjangan FROM salary_criteria WHERE jenis = 'Bonus'";
        
        $result_potongan = $conn->query($sql_potongan);
        $result_tunjangan = $conn->query($sql_tunjangan);

        $total_potongan = $result_potongan->fetch_assoc()['total_potongan'] ?? 0;
        $total_tunjangan = $result_tunjangan->fetch_assoc()['total_tunjangan'] ?? 0;

        // Hitung total gaji
        $gaji_pokok = intval($_POST['gaji_pokok']); // Ambil dari form
        $bonus = intval($_POST['bonus']); // Ambil dari form
        $tunjangan = intval($_POST['tunjangan']); // Ambil dari form

        // Logika perhitungan gaji
        $total_gaji = $gaji_pokok + $tunjangan + $bonus - $total_potongan;

        // Menyimpan data gaji
        $stmt_insert = $conn->prepare("INSERT INTO salary (karyawan_id, gaji_pokok, bonus, potongan, tunjangan, total_gaji) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("iiiiii", $karyawan_id, $gaji_pokok, $bonus, $total_potongan, $tunjangan, $total_gaji);
        
        if ($stmt_insert->execute()) {
            echo "<script>alert('Data gaji berhasil disimpan.');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt_insert->error . "');</script>";
        }
        $stmt_insert->close(); // Close setelah selesai
    }
}

// Proses untuk mengupdate gaji karyawan
if (isset($_POST['update_gaji'])) {
    $id_gaji = intval($_POST['id_gaji']);
    $gaji_pokok = intval($_POST['gaji_pokok']);
    $tunjangan = intval($_POST['tunjangan']);
    $bonus = intval($_POST['bonus']);
    $potongan = intval($_POST['potongan']);

    $total_gaji = $gaji_pokok + $tunjangan + $bonus - $potongan;

    $stmt_update = $conn->prepare("UPDATE salary SET gaji_pokok = ?, tunjangan = ?, bonus = ?, potongan = ?, total_gaji = ? WHERE id_salary = ?");
    $stmt_update->bind_param("iiiiii", $gaji_pokok, $tunjangan, $bonus, $potongan, $total_gaji, $id_gaji);
    
    if ($stmt_update->execute()) {
        echo "<script>alert('Data gaji berhasil diperbarui.');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt_update->error . "');</script>";
    }
    $stmt_update->close();
}

// Proses untuk mengirim data gaji
if (isset($_POST['kirim_gaji'])) {
    $id_gaji = intval($_POST['id_gaji']);
    // Redirect ke data-gajian.php dengan ID gaji
    header("Location: data-gajian.php?id_gaji=$id_gaji");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perhitungan Gaji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/perhitungan-gaji-style.css">
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
        <h2>Perhitungan Gaji</h2>
        <div class="table-container">
            <div class="table-header">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSalaryModal">+ Tambah Data</button>
            </div>
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Penambahan</th>
                        <th>Potongan</th>
                        <th>Total Terima Gaji</th>
                        <th>Gajian Terakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        $total_gaji = $row['gaji_pokok'] + $row['tunjangan'] + $row['bonus'] - $row['potongan'];
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama_lengkap']}</td>
                                <td>Rp. " . number_format($row['gaji_pokok'] + $row['tunjangan'] + $row['bonus'], 0, ',', '.') . "</td>
                                <td>Rp. " . number_format($row['potongan'], 0, ',', '.') . "</td>
                                <td>Rp. " . number_format($total_gaji, 0, ',', '.') . "</td>
                                <td>" . date('d F Y', strtotime($row['tanggal_gaji'])) . "</td>
                                <td>
                                    <button class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#editSalaryModal' data-id='{$row['id_salary']}' data-gaji_pokok='{$row['gaji_pokok']}' data-tunjangan='{$row['tunjangan']}' data-bonus='{$row['bonus']}' data-potongan='{$row['potongan']}'>Setting</button>
                                    <form action='' method='POST' style='display:inline;'>
                                        <input type='hidden' name='id_gaji' value='{$row['id_salary']}'>
                                        <button type='submit' name='kirim_gaji' class='btn btn-success btn-sm'>Kirim</button>
                                    </form>
                                </td>
                            </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- tambah data Gaji -->
    <div class="modal fade" id="addSalaryModal" tabindex="-1" aria-labelledby="addSalaryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSalaryModalLabel">Tambah Data Gaji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="perhitungan-gaji.php" method="POST" id="salaryForm">
                        <div class="mb-3">
                            <label for="karyawan_id" class="form-label">ID Karyawan</label>
                            <input type="text" class="form-control" id="karyawan_id" name="karyawan_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                            <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" required>
                        </div>
                        <div class="mb-3">
                            <label for="tunjangan" class="form-label">Tunjangan</label>
                            <input type="number" class="form-control" id="tunjangan" name="tunjangan" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="bonus" class="form-label">Bonus</label>
                            <input type="number" class="form-control" id="bonus" name="bonus" value="0">
                        </div>
                        <button type="submit" name="submit_gaji" class="btn btn-primary">Simpan Gaji</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Mengedit Data Gaji -->
    <div class="modal fade" id="editSalaryModal" tabindex="-1" aria-labelledby="editSalaryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSalaryModalLabel">Edit Data Gaji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="perhitungan-gaji.php" method="POST">
                        <input type="hidden" name="id_gaji" id="edit_id_gaji">
                        <div class="mb-3">
                            <label for="edit_gaji_pokok" class="form-label">Gaji Pokok</label>
                            <input type="number" class="form-control" id="edit_gaji_pokok" name="gaji_pokok" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tunjangan" class="form-label">Tunjangan</label>
                            <input type="number" class="form-control" id="edit_tunjangan" name="tunjangan" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="edit_bonus" class="form-label">Bonus</label>
                            <input type="number" class="form-control" id="edit_bonus" name="bonus" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="edit_potongan" class="form-label">Potongan</label>
                            <input type="number" class="form-control" id="edit_potongan" name="potongan" value="0">
                        </div>
                        <button type="submit" name="update_gaji" class="btn btn-primary">Update Gaji</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- component -->
    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Script untuk mengisi modal edit dengan data yang sesuai
        const editSalaryModal = document.getElementById('editSalaryModal');
        editSalaryModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget; // Tombol yang diklik
            const id = button.getAttribute('data-id');
            const gajiPokok = button.getAttribute('data-gaji_pokok');
            const tunjangan = button.getAttribute('data-tunjangan');
            const bonus = button.getAttribute('data-bonus');
            const potongan = button.getAttribute('data-potongan');

            const modalIdInput = editSalaryModal.querySelector('#edit_id_gaji');
            const modalGajiPokokInput = editSalaryModal.querySelector('#edit_gaji_pokok');
            const modalTunjanganInput = editSalaryModal.querySelector('#edit_tunjangan');
            const modalBonusInput = editSalaryModal.querySelector('#edit_bonus');
            const modalPotonganInput = editSalaryModal.querySelector('#edit_potongan');

            modalIdInput.value = id;
            modalGajiPokokInput.value = gajiPokok;
            modalTunjanganInput.value = tunjangan;
            modalBonusInput.value = bonus;
            modalPotonganInput.value = potongan;
        });
    </script>
</body>
</html>
