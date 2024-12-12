-- Tabel Admin
CREATE TABLE `admin` (
   `id_admin` INT NOT NULL AUTO_INCREMENT,
   `username` VARCHAR(255) NOT NULL,
   `password` VARCHAR(255) NOT NULL,
   PRIMARY KEY (`id_admin`)
);
INSERT INTO `admin` VALUES (1, 'admin', 'admin');

-- Tabel Karyawan
CREATE TABLE `employees` (
    `id_karyawan` VARCHAR(50) NOT NULL UNIQUE,
    `nama` VARCHAR(255) NOT NULL,
    `jabatan` VARCHAR(100) NOT NULL,
    `dob` DATE NOT NULL, 
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `photo` VARCHAR(255) DEFAULT NULL,
    `qr_code` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id_karyawan`)
);

-- Tabel Shift
CREATE TABLE `shift` (
    `id_shift` INT NOT NULL AUTO_INCREMENT,
    `shift_name` VARCHAR(100) NOT NULL,
    `jam_masuk` TIME NOT NULL, 
    `jam_pulang` TIME NOT NULL,
    `status` ENUM('Normal', 'Lembur') NOT NULL,
    `terdaftar` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (`id_shift`)
);

-- Tabel Absensi
CREATE TABLE `attendance` (
    `id_attendance` INT NOT NULL AUTO_INCREMENT,
    `karyawan_id` VARCHAR(50) NOT NULL,
    `shift_id` INT NOT NULL,
    `tanggal` DATE NOT NULL,
    `jam_masuk` TIME NOT NULL,
    `jam_pulang` TIME DEFAULT NULL,
    PRIMARY KEY (`id_attendance`),
    FOREIGN KEY (`karyawan_id`) REFERENCES `employees`(`id_karyawan`) ON DELETE CASCADE,
    FOREIGN KEY (`shift_id`) REFERENCES `shift`(`id_shift`) ON DELETE CASCADE
);

-- Tabel Kriteria Gaji
CREATE TABLE `salary_criteria` (
    `id_salary_criteria` INT NOT NULL AUTO_INCREMENT,
    `kriteria` VARCHAR(100) NOT NULL,
    `jenis` ENUM('Potongan', 'Bonus') NOT NULL, 
    `jumlah` INT NOT NULL,
    PRIMARY KEY (`id_salary_criteria`)
);

-- Tabel Gaji
CREATE TABLE `salary` (
    `id_salary` INT NOT NULL AUTO_INCREMENT,
    `karyawan_id` VARCHAR(50) NOT NULL,
    `gaji_pokok` INT NOT NULL,
    `bonus` INT DEFAULT 0,
    `potongan` INT DEFAULT 0,
    `tunjangan` INT DEFAULT 0,
    `total_gaji` INT NOT NULL, 
    `tanggal_gaji` DATE DEFAULT CURRENT_DATE,
    PRIMARY KEY (`id_salary`),
    FOREIGN KEY (`karyawan_id`) REFERENCES `employees`(`id_karyawan`) ON DELETE CASCADE
);

-- Tabel Roles
CREATE TABLE `roles` (
    `role_id` INT NOT NULL AUTO_INCREMENT,
    `role_name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`role_id`)
);
INSERT INTO `roles` (role_name) VALUES ('admin'), ('employee');

-- Tabel Users
CREATE TABLE `users` (
    `user_id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL,
    `id_karyawan` VARCHAR (50) NOT NULL,
    PRIMARY KEY (`user_id`),
    FOREIGN KEY (`id_karyawan`) REFERENCES `employees`(`id_karyawan`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE
);
-- tambahkan jika tidak ada
INSERT INTO `users` (username, password, role_id) VALUES ('admin', 'admin', 1), ('employee', 'employee', 2);