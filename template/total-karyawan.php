<?php include "config-dashboard.php"; ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Total Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/total-karyawan-style.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Header -->
    <header class="header-dashboard">
        <div id="current-time" class="current-time"></div>
    </header>

    <!-- Tabel data -->
    <div class="row">
        <div class="col-md-12 data-table">
            <div class="card">
                <div class="card-header text-center">
                    <strong>DAFTAR KARYAWAN</strong>
                </div>
                <div class="card-body table-responsive text-center">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Karyawan</th>
                                <th>Nama</th>
                                <th>Tanggal Lahir</th>
                                <th>Email</th>
                                <th>Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $sql = "SELECT id_karyawan, nama, dob, email, photo FROM employees";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $id_karyawan = $row['id_karyawan'];
                                    $nama = $row['nama'];
                                    $dob = $row['dob'];
                                    $email = $row['email'];
                                    $photo = $row['photo'];
                                    $photoPath = "http://localhost/dashboard-absensi/auth/component/uploads/" . $photo;

                                    echo "<tr>
                                            <td>{$no}</td>
                                            <td>{$id_karyawan}</td>
                                            <td>{$nama}</td>
                                            <td>{$dob}</td>
                                            <td>{$email}</td>
                                            <td><img src='{$photoPath}' alt='{$nama}' width='100'></td>
                                          </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='6'>Tidak ada karyawan yang terdaftar.</td></tr>";
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

    <!-- Library -->
    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
