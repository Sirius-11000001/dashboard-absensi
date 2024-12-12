<?php
// koneksi
include "connector.php";

// Total karyawan
$total_karyawan_query = "SELECT COUNT(*) as total FROM employees";
$total_karyawan_result = $conn->query($total_karyawan_query);
$total_karyawan = $total_karyawan_result->fetch_assoc()['total'];

// Total absensi hari ini
$total_absensi_query = "SELECT COUNT(DISTINCT karyawan_id) as total FROM attendance WHERE tanggal = CURDATE()";
$total_absensi_result = $conn->query($total_absensi_query);
$total_absensi = $total_absensi_result->fetch_assoc()['total'];

// Absensi saya
$user_id = $_SESSION['user_id'] ?? null;
$absensi_saya = 0;
if ($user_id) {
    $absensi_saya_query = "SELECT COUNT(*) as total FROM attendance WHERE karyawan_id = '$user_id' AND tanggal = CURDATE()";
    $absensi_saya_result = $conn->query($absensi_saya_query);
    $absensi_saya = $absensi_saya_result->fetch_assoc()['total'];
}
// Karyawan belum absen hari ini
$belum_absen_query = "
SELECT id_karyawan, nama 
FROM employees 
WHERE id_karyawan NOT IN (SELECT DISTINCT karyawan_id FROM attendance WHERE tanggal = CURDATE());
";
$belum_absen_result = $conn->query($belum_absen_query);

// Karyawan sudah absen masuk hari ini
$sudah_masuk_query = "
SELECT e.id_karyawan, e.nama 
FROM employees e
JOIN attendance a ON e.id_karyawan = a.karyawan_id
WHERE a.tanggal = CURDATE() AND a.jam_masuk IS NOT NULL;
";
$sudah_masuk_result = $conn->query($sudah_masuk_query);

// Karyawan belum absen pulang hari ini
$belum_pulang_query = "
SELECT e.id_karyawan, e.nama 
FROM employees e
JOIN attendance a ON e.id_karyawan = a.karyawan_id
WHERE a.tanggal = CURDATE() AND a.jam_masuk IS NOT NULL AND a.jam_pulang IS NULL;
";
$belum_pulang_result = $conn->query($belum_pulang_query);

// Proses absen jika user login
if ($user_id) {
    $absen_check_query = "
    SELECT jam_masuk 
    FROM attendance 
    WHERE karyawan_id = '$user_id' AND tanggal = CURDATE();
    ";
    $absen_check_result = $conn->query($absen_check_query);
    $absen_data = $absen_check_result->fetch_assoc();

    if ($absen_data) {
        $last_scan_time = new DateTime($absen_data['jam_masuk']);
        $now = new DateTime();
        $hours_since_last_scan = $now->diff($last_scan_time)->h;

        if ($hours_since_last_scan >= 8) {
            // Update ke absensi pulang
            $update_pulang_query = "
            UPDATE attendance SET jam_pulang = NOW() 
            WHERE karyawan_id = '$user_id' AND tanggal = CURDATE();
            ";
            $conn->query($update_pulang_query);
            echo "Berhasil absen pulang!";
        } else {
            echo "";
        }
    } else {
        echo "";
    }
}
?>
