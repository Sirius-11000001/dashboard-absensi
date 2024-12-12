<?php
// Koneksi database
include "config-dashboard.php";

// Ambil kriteria_id dari query string
if (isset($_GET['kriteria_id'])) {
    $kriteria_id = $_GET['kriteria_id'];

    // Query untuk mendapatkan potongan dari kriteria_gaji
    $stmt = $conn->prepare("SELECT jumlah AS potongan FROM kriteria_gaji WHERE id = ?");
    $stmt->bind_param("i", $kriteria_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row); // Mengembalikan data dalam format JSON
    } else {
        echo json_encode(['potongan' => 0]); // Jika tidak ada, kembalikan potongan 0
    }

    $stmt->close();
}
?>