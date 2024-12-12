<?php
session_start();

function checkRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {
        header('Location: tidak-ditemukan.php');
        exit();
    }
}

function checkAdmin() {
    checkRole('admin');
}

function checkEmployee() {
    checkRole('employee');
}
?>