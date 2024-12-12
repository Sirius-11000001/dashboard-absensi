<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Administrator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/style-login.css">
</head>
<body>
    <!-- Main login form -->
    <div class="main-login">
        <h3 class="hello"><strong>Kerja Keras Membawa</strong></h3><br>
        <h3 class="hello"><strong>Kesuksesan Besar</strong></h3><br>
    </div>
    
    <div class="wrapper">
        <div class="title">Login Here</div>

        <!-- Menampilkan pesan sukses jika absen berhasil -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div class="alert alert-success text-center" role="alert">
                Anda berhasil absen!
            </div>
        <?php endif; ?>

        <!-- Menampilkan pesan error jika login gagal -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Form login -->
        <form action="aktivasi-login.php" method="post">
            <div class="field">
                <input type="text" name="username" required>
                <label>Username</label>
            </div>
            <div class="field">
                <input type="password" name="password" id="password" required>
                <label>Password</label>
            </div>
            <div class="field">
                <input type="checkbox" id="show-password" onclick="togglePassword()"> Show Password
            </div>
            Not a Member yet? <a style="text-decoration: none;" href="http://localhost/dashboard-absensi/auth/component/generate_qr.php">Signup Now</a><br>
            Not Absent yet? <a style="text-decoration: none;" href="http://127.0.0.1:5000/">Scan Here</a>
            <div class="field">
                <input type="submit" value="Login"> 
            </div>
        </form>
    </div>        

    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
