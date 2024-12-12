<?php
session_start();
include "connector.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek di tabel admin terlebih dahulu
    $sql_admin = "SELECT * FROM admin WHERE username = ?";
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("s", $username);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows > 0) {
        $admin = $result_admin->fetch_assoc();
        if ($password == $admin['password']) { // Verifikasi password tanpa hash
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = 'admin';
            $_SESSION['user_id'] = $admin['user_id']; // Pastikan user_id diambil dari database
            header('Location: http://localhost/dashboard-absensi/template/index.php');
            exit();
        } else {
            $_SESSION['error_message'] = 'Invalid password';
        }
    } else {
        // Jika tidak ditemukan di tabel admin, cek di tabel users
        $sql_user = "SELECT * FROM users WHERE username = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("s", $username);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows > 0) {
            $user = $result_user->fetch_assoc();
            if (password_verify($password, $user['password'])) { // Verifikasi password yang di-hash
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['user_id']; // Pastikan user_id diambil dari database
                header('Location: http://localhost/dashboard-absensi/template/index.php');
                exit();
            } else {
                $_SESSION['error_message'] = 'Invalid password';
            }
        } else {
            $_SESSION['error_message'] = 'User not found';
        }
    }
    header('Location: login.php');
    exit();
}
?>
