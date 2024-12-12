<?php
include "config-dashboard.php";  // Menghubungkan ke database
include "auth.php";
checkAdmin();

// Cek apakah ada permintaan untuk tambah data
if (isset($_POST['tambah'])) {
    $kriteria = $_POST['kriteria'];
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];

    // Menggunakan prepared statement untuk menambah data ke tabel salary_criteria
    $stmt = $conn->prepare("INSERT INTO salary_criteria (kriteria, jenis, jumlah) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $kriteria, $jenis, $jumlah);  // Menyambungkan parameter

    if ($stmt->execute()) {
        echo "";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Cek apakah ada permintaan untuk update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $kriteria = $_POST['kriteria'];
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];

    // Menggunakan prepared statement untuk mengupdate data
    $stmt = $conn->prepare("UPDATE salary_criteria SET kriteria = ?, jenis = ?, jumlah = ? WHERE id_salary_criteria = ?");
    $stmt->bind_param("ssii", $kriteria, $jenis, $jumlah, $id);

    if ($stmt->execute()) {
        echo "";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Cek apakah ada permintaan untuk hapus data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Menggunakan prepared statement untuk menghapus data
    $stmt = $conn->prepare("DELETE FROM salary_criteria WHERE id_salary_criteria = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Mengambil data dari tabel salary_criteria
$sql = "SELECT * FROM salary_criteria";
$result = $conn->query($sql);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kriteria Gaji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/kriteria-gaji-style.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include "sidebar.php"; ?> 
    
    <!-- Header -->
    <header class="header-dashboard">
        <div id="current-time" class="current-time"></div>
    </header>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2><strong>Kriteria Gaji</strong></h2>
        <p>Semua data kriteria gaji akan muncul di sini</p>

        <!-- tambah data -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Kriteria Gaji</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="kriteria" class="form-label">Kriteria Gaji</label>
                                <input type="text" class="form-control" id="kriteria" name="kriteria" required>
                            </div>
                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="Potongan">Potongan</option>
                                    <option value="Bonus">Bonus</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary">Tambah Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- data tabel -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Tambah Data</button>
                        <table class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kriteria Gaji</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id_salary_criteria']; ?></td>
                                            <td><?php echo $row['kriteria']; ?></td>
                                            <td><?php echo $row['jenis']; ?></td>
                                            <td>Rp. <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                                            <td>
                                                <!-- tombol edit -->
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id_salary_criteria']; ?>">Edit</button>
                                                <!-- tombol delete -->
                                                <a href="?delete=<?php echo $row['id_salary_criteria']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Delete</a>
                                            </td>
                                        </tr>

                                        <!-- Edit Data -->
                                        <div class="modal fade" id="editModal<?php echo $row['id_salary_criteria']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['id_salary_criteria']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel<?php echo $row['id_salary_criteria']; ?>">Edit Kriteria Gaji</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST">
                                                            <input type="hidden" name="id" value="<?php echo $row['id_salary_criteria']; ?>">
                                                            <div class="mb-3">
                                                                <label for="kriteria" class="form-label">Kriteria Gaji</label>
                                                                <input type="text" class="form-control" id="kriteria" name="kriteria" value="<?php echo $row['kriteria']; ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="jenis" class="form-label">Jenis</label>
                                                                <select class="form-select" id="jenis" name="jenis" required>
                                                                    <option value="Potongan" <?php echo $row['jenis'] == 'Potongan' ? 'selected' : ''; ?>>Potongan</option>
                                                                    <option value="Bonus" <?php echo $row['jenis'] == 'Bonus' ? 'selected' : ''; ?>>Bonus</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="jumlah" class="form-label">Jumlah</label>
                                                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?php echo $row['jumlah']; ?>" required>
                                                            </div>
                                                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">Tidak ada data.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
