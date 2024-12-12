<?php include "config-dashboard.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/profil-style.css">
</head>

<body>

    <!-- sidebar -->
    <?php include "sidebar.php"; ?>
    
    <header class="header-dashboard">
      <div id="current-time" class="current-time"></div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h2><small>Profil Pengguna</small></h2>
        <button class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Data</button>

        <div class="table-container">
            <div class="table-header">
                <div class="entries">
                    Show 
                    <select>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select> 
                    entries
                </div>
                <div class="search">
                    Search: <input type="text">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Sebagai</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Koneksi ke database
                        $conn = new mysqli("localhost", "root", "", "employee_db");

                        // Cek koneksi
                        if ($conn->connect_error) {
                            die("Koneksi gagal: " . $conn->connect_error);
                        }

                        // Ambil data karyawan dari database
                        $sql = "SELECT * FROM employees";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data dari setiap baris
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='#'>" . $row["id"] . "</td>";
                                echo "<td data-label='Foto'><img src='" . $row["photo"] . "' alt='Profile' class='profile-img'></td>";
                                echo "<td data-label='Username'>" . $row["id_karyawan"] . "</td>";
                                echo "<td data-label='Nama Lengkap'>" . $row["name"] . "</td>";
                                echo "<td data-label='Email'>" . $row["email"] . "</td>";
                                echo "<td data-label='Sebagai'>" . $row["jabatan"] . "</td>";
                                echo "<td data-label='Terdaftar'>" . $row["dob"] . "</td>";
                                echo "<td data-label='Aksi'>
                                    <form method='POST' action='profil.php' style='display:inline;'>
                                        <input type='hidden' name='action' value='reset_password'>
                                        <input type='hidden' name='user_id' value='" . $row["id"] . "'>
                                        <button type='submit' class='btn btn-warning btn-sm'><i class='fas fa-key'></i> Reset Password</button>
                                    </form>
                                    <form method='POST' action='profil.php' style='display:inline;'>
                                        <input type='hidden' name='action' value='delete'>
                                        <input type='hidden' name='user_id' value='" . $row["id"] . "'>
                                        <button type='submit' class='btn btn-danger btn-sm'><i class='fas fa-trash-alt'></i> Delete</button>
                                    </form>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>Tidak ada data karyawan</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- footer -->
    <?php include "footer.php"; ?>

    <!-- Library -->
    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
include "connector.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $user_id = $_POST['user_id'];

    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "employee_db");

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    if ($action == 'reset_password') {
        // Logika untuk reset password
        $new_password = password_hash('newpassword', PASSWORD_DEFAULT);
        $query = "UPDATE employees SET password='$new_password' WHERE id='$user_id'";
        if ($conn->query($query) === TRUE) {
            echo "Password untuk user ID $user_id telah direset.";
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
    } elseif ($action == 'delete') {
        // Logika untuk menghapus user
        $query = "DELETE FROM employees WHERE id='$user_id'";
        if ($conn->query($query) === TRUE) {
            echo "User ID $user_id telah dihapus.";
        } else {
            echo "Error: " . $query . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>
