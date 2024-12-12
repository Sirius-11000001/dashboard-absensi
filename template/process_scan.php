<?php
// Koneksi ke database
include "connector.php";

// Ambil data dari query string
$data = $_GET['data'] ?? '';

if ($data) {
    // Misalnya, data QR code berisi "Name: John Doe\nDOB: 1990-01-01"
    $lines = explode("\n", $data);
    $name = str_replace('Name: ', '', $lines[0]);
    $dob = str_replace('DOB: ', '', $lines[1]);

    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM employees WHERE name = ? AND dob = ?");
    $stmt->bind_param("ss", $name, $dob);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Data cocok, redirect ke login.php
        header('Location: login.php');
        exit();
    } else {
        // Data tidak cocok, tampilkan pesan error
        echo "<script>
            alert('Employee not found. Please check the QR code again.');
            window.history.back();
        </script>";
    }
} else {
    // Jika data kosong, tampilkan pesan error
    echo "<script>
        alert('No data received. Please scan a valid QR code.');
        window.history.back();
    </script>";
}
?>
