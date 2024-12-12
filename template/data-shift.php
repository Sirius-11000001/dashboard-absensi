<?php 
include "config-dashboard.php"; 
include "auth.php";
checkAdmin();

// Set the default timezone
date_default_timezone_set('Asia/Jakarta'); // Set to your desired timezone

// Fungsi untuk mengambil data shift
function getDataShift() {
    global $conn;
    $query = "SELECT * FROM shift";
    $result = mysqli_query($conn, $query);
    return $result;
}

// Fungsi untuk menambah data shift
if (isset($_POST['add_shift'])) {
    $shift_name = $_POST['shift_name'];
    $jam_masuk = $_POST['jam_masuk'];
    $jam_pulang = $_POST['jam_pulang'];
    $status = $_POST['status'];
    $terdaftar = date('Y-m-d H:i:s');

    $query = "INSERT INTO shift (shift_name, jam_masuk, jam_pulang, status, terdaftar) VALUES ('$shift_name', '$jam_masuk', '$jam_pulang', '$status', '$terdaftar')";
    mysqli_query($conn, $query);
    header("Location: data-shift.php");
}

// Fungsi untuk mengedit data shift
if (isset($_POST['edit_shift'])) {
    $id = $_POST['id'];
    $shift_name = $_POST['shift_name'];
    $jam_masuk = $_POST['jam_masuk'];
    $jam_pulang = $_POST['jam_pulang'];
    $status = $_POST['status'];

    $query = "UPDATE shift SET shift_name = '$shift_name', jam_masuk = '$jam_masuk', jam_pulang = '$jam_pulang', status = '$status' WHERE id_shift = $id";
    mysqli_query($conn, $query);
    header("Location: data-shift.php");
}

// Fungsi untuk menghapus data shift
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    // hapus semua baris terkait di kehadiran
    $query = "DELETE FROM attendance WHERE shift_id = $id";
    mysqli_query($conn, $query);

    // hapus baris dari tabel shift
    $query = "DELETE FROM shift WHERE id_shift = $id";
    mysqli_query($conn, $query);

    header("Location: data-shift.php");
}

$dataShift = getDataShift();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/library/js/bootstrap.min.js">
    <link rel="stylesheet" href="http://localhost/dashboard-absensi/style/data-shift-style.css">
  </head>
  <body>
    <!-- sidebar -->
    <?php include "sidebar.php"; ?> 
    
    <!-- Header -->
    <header class="header-dashboard">
      <div id="current-time" class="current-time"></div>
    </header>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2>Data Shift</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addShiftModal">+ Tambah Data</button>
        <table class="table table-bordered table-striped text-center">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Shift</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($dataShift)) { 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['shift_name']; ?></td>
                    <td><?php echo $row['jam_masuk']; ?></td>
                    <td><?php echo $row['jam_pulang']; ?></td>
                    <td><span class="badge badge-<?php echo $row['status'] == 'Lembur' ? 'warning' : 'secondary'; ?>"><?php echo $row['status']; ?></span></td>
                    <td><?php echo date('d F Y H:i', strtotime($row['terdaftar'])); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editShiftModal" data-id="<?php echo $row['id_shift']; ?>" data-shift_name="<?php echo $row['shift_name']; ?>" data-jam_masuk="<?php echo $row['jam_masuk']; ?>" data-jam_pulang="<?php echo $row['jam_pulang']; ?>" data-status="<?php echo $row['status']; ?>">Edit</button>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id_shift']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <nav class="navigasi" aria-label="Page navigation">
            <ul class="pagination justify-content-end">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- tambah data shift -->
    <div class="modal fade" id="addShiftModal" tabindex="-1" aria-labelledby="addShiftModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addShiftModalLabel">Tambah Data Shift</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="">
              <div class="mb-3">
                <label for="shift_name" class="form-label">Nama Shift</label>
                <input type="text" class="form-control" id="shift_name" name="shift_name" required>
              </div>
              <div class="mb-3">
                <label for="jam_masuk" class="form-label">Jam Masuk</label>
                <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" required>
              </div>
              <div class="mb-3">
                <label for="jam_pulang" class="form-label">Jam Pulang</label>
                <input type="time" class="form-control" id="jam_pulang" name="jam_pulang" required>
              </div>
              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                  <option value="Normal">Normal</option>
                  <option value="Lembur">Lembur</option>
                </select>
              </div>
              <button type="submit" name="add_shift" class="btn btn-primary">Tambah</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- edit data shift -->
    <div class="modal fade" id="editShiftModal" tabindex="-1" aria-labelledby="editShiftModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editShiftModalLabel">Edit Data Shift</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="">
              <input type="hidden" id="edit_id" name="id">
              <div class="mb-3">
                <label for="edit_shift_name" class="form-label">Nama Shift</label>
                <input type="text" class="form-control" id="edit_shift_name" name="shift_name" required>
              </div>
              <div class="mb-3">
                <label for="edit_jam_masuk" class="form-label">Jam Masuk</label>
                <input type="time" class="form-control" id="edit_jam_masuk" name="jam_masuk" required>
              </div>
              <div class="mb-3">
                <label for="edit_jam_pulang" class="form-label">Jam Pulang</label>
                <input type="time" class="form-control" id="edit_jam_pulang" name="jam_pulang" required>
              </div>
              <div class="mb-3">
                <label for="edit_status" class="form-label">Status</label>
                <select class="form-select" id="edit_status" name="status" required>
                  <option value="Normal">Normal</option>
                  <option value="Lembur">Lembur</option>
                </select>
              </div>
              <button type="submit" name="edit_shift" class="btn btn-primary">Simpan Perubahan</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- footer -->
    <?php include "footer.php"; ?> 

    <script src="http://localhost/dashboard-absensi/auth/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
      $('#editShiftModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var shift_name = button.data('shift_name');
        var jam_masuk = button.data('jam_masuk');
        var jam_pulang = button.data('jam_pulang');
        var status = button.data('status');

        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_shift_name').val(shift_name);
        modal.find('#edit_jam_masuk').val(jam_masuk);
        modal.find('#edit_jam_pulang').val(jam_pulang);
        modal.find('#edit_status').val(status);
      });
    </script>
  </body>
</html>
