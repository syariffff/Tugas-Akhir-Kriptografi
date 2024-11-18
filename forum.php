<?php
session_start();
if(!isset($_SESSION['username'])){
    header('Location:index.php');
}
include '../tugasakhirkripto/functions/db.php'; // Pastikan Anda sudah memiliki file koneksi ke database
include 'functions/encryption.php';
include 'functions/steganography.php';

$id_parfum = $_GET['id']; 
// Ambil data kartu dari database
$sql = "SELECT * FROM parfum where id_parfum = '$id_parfum'";
$result = $conn->query($sql);

// Ambil ulasan dari database berdasarkan ID parfum
$sqlUlasan = "SELECT review.id_user, review.review, review.foto, users.username 
              FROM review 
              JOIN users ON review.id_user = users.id 
              WHERE review.id_parfum = ?";
$stmtUlasan = $conn->prepare($sqlUlasan);
$stmtUlasan->bind_param("i", $id_parfum);
$stmtUlasan->execute();
$resultUlasan = $stmtUlasan->get_result();

// Periksa apakah user sudah memberikan ulasan
$sqlCheckReview = "SELECT id_review, review, foto FROM review WHERE id_user = ? AND id_parfum = ?";
$stmtCheckReview = $conn->prepare($sqlCheckReview);
$stmtCheckReview->bind_param("ii", $_SESSION['id'], $id_parfum);
$stmtCheckReview->execute();
$userReview = $stmtCheckReview->get_result()->fetch_assoc();
$stmtCheckReview->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ulasan = $_POST['ulasan'];
    $id_user = $_POST['id_user'];
    $id_parfum = $_POST['id_parfum'];
    $key = $_GET['code'];
    // Enkripsi ulasan
    $encryptedReview = superEncrypt($ulasan,$key);
    // Jika ulasan sudah ada, perbarui
    if (isset($_POST['id_review'])) {
        $id_review = $_POST['id_review'];
        // Periksa apakah ada gambar baru yang diunggah
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $fileType = mime_content_type($_FILES['gambar']['tmp_name']);
            if (in_array($fileType, ['image/jpeg', 'image/png'])) {
                $gambarTmpPath = $_FILES['gambar']['tmp_name'];
                $gambarContent = file_get_contents($gambarTmpPath);
                // Periksa apakah sudah ada watermark
                try {
                    $existingWatermark = retrieveMessage($gambarTmpPath);
                    if ($existingWatermark) {
                        // Jika ada watermark, tampilkan notifikasi
                        header("Location: forum.php?id=$id_parfum&code=$key&m=nyuri");
                        exit;
                    }
                } catch (Exception $e) {
                    // Jika tidak ada watermark, lanjutkan proses
                }
                $watermarkMessage = $_SESSION['username']; // watermark
                // Proses steganografi: tambahkan watermark ke gambar
                hideMessage($gambarTmpPath, $watermarkMessage, $gambarTmpPath);
                $gambarContent = file_get_contents($gambarTmpPath); // Ambil gambar yang sudah diproses
            } else {
                $errorMessage = "File yang diunggah bukan gambar.";
            }

            // Update ulasan dengan gambar baru
            $sqlUpdate = "UPDATE review SET review = ?, foto = ? WHERE id_review = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ssi", $encryptedReview, $gambarContent, $id_review);
        }
        if ($stmtUpdate->execute()) {
            header("Location: forum.php?id=" .$id_parfum."&code=".$key);
        } else {
            $errorMessage = "Gagal memperbarui ulasan: " . $stmtUpdate->error;
        }
        $stmtUpdate->close();
    } else {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $fileType = mime_content_type($_FILES['gambar']['tmp_name']);
            if (in_array($fileType, ['image/jpeg', 'image/png'])) {
                $gambarTmpPath = $_FILES['gambar']['tmp_name'];
                $gambarContent = file_get_contents($gambarTmpPath);
                // Periksa apakah sudah ada watermark
                try {
                    $existingWatermark = retrieveMessage($gambarTmpPath);
                    if ($existingWatermark) {
                        // Jika ada watermark, tampilkan notifikasi
                        header("Location: forum.php?id=$id_parfum&code=$key&m=nyuri");
                        exit;
                    }
                } catch (Exception $e) {
                    // Jika tidak ada watermark, lanjutkan proses
                }
                $watermarkMessage = $_SESSION['username']; // watermark
                // Proses steganografi: tambahkan watermark ke gambar
                hideMessage($gambarTmpPath, $watermarkMessage, $gambarTmpPath);
                $gambarContent = file_get_contents($gambarTmpPath); // Ambil gambar yang sudah diproses
            } else {
                $errorMessage = "File yang diunggah bukan gambar.";
            }

            // Update ulasan dengan gambar baru
            $sqlInsert = "INSERT INTO review (review, foto, id_user, id_parfum) VALUES (?,?,?,?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ssii", $encryptedReview, $gambarContent, $id_user, $id_parfum);
        }
        if ($stmtInsert->execute()) {
            header("Location: forum.php?id=" .$id_parfum."&code=".$key);
        } else {
            $errorMessage = "Gagal memperbarui ulasan: " . $stmtUpdate->error;
        }
        $stmtUpdate->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Parfume</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body{
            background-image: url(v1016-b-09.jpg);
            background-size: cover;
            backdrop-filter: blur(5px); /* Menambahkan efek blur */
            color: white;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body class="d-flex">
    <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px; height:cover;">
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <svg class="bi pe-none me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
        <span class="fs-4">Forum Parfume</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" class="nav-link text-white" aria-current="page">
            <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#home"></use></svg>
            Home
            </a>
        </li>
        <li>
            <a href="list_forum.php" class="nav-link active">
            <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#speedometer2"></use></svg>
            Forum
            </a>
        </li>
        <li>
            <a href="encrypt_file.php" class="nav-link text-white">
            <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#speedometer2"></use></svg>
            Catatan
            </a>
        </li>
        </ul>
        <hr>
        <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="data:image/jpeg;base64,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" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
        </ul>
        </div>
    </div>
    <div class="container mt-5">
        <div class="row">
            <?php if(isset($_GET['m'])){ ?>
            <div class="alert alert-danger" role="alert">Dilarang mengambil foto review user lain!</div>
            <?php } ?>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="card text-center mb-3">
                        <div class="card-body">
                            <h4 class="card-title">' . $row['nama'] . '</h4>
                            <h6>Dari Brand ' . $row['brand'] . '</h6>
                            <img src="' . $row['gambar'] . '" alt="...">
                            <p>' . $row['notes'] . '</p>
                            <p class="card-text">' . $row['deskripsi'] . '</p>
                        </div>
                    </div>';
                }
            }
            ?>
    <?php if ($resultUlasan->num_rows > 0): ?>
        <div class="container mt-2">
            <h3 class="text-center mb-4">Ulasan Pengguna</h3>
            <?php while ($ulasan = $resultUlasan->fetch_assoc()): ?>
                <div class="card mb-3 shadow">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <?php if (!empty($ulasan['foto'])): ?>
                                <img src="data:image/png;base64,<?= base64_encode($ulasan['foto']); ?>" class="img-fluid rounded-start" alt="Gambar Ulasan" style="width:150px; height:150px;">
                            <?php else: ?>
                                <img src="placeholder.png" class="img-fluid rounded-start" alt="Gambar Kosong">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($ulasan['username']); ?></h5>
                                <p class="card-text"><?= superDecrypt($ulasan['review'],$_GET['code']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
    <div class="container mb-2">
        <h5 class="text-center">Belum ada ulasan untuk parfum ini.</h5>
    </div>
    <?php endif; ?>

    <div class="container">
    <?php if ($userReview): ?>
        <!-- Form Edit Ulasan -->
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="card text mb-5 shadow">
                <div class="card-body">
                    <h4 class="card-title">Edit Ulasan Anda</h4>
                    <!-- Input Ulasan -->
                    <div class="mb-3">
                        <label for="ulasan" class="form-label fw-bold">Ulasan</label>
                        <textarea class="form-control" name="ulasan" id="ulasan" rows="4" required><?= superDecrypt($userReview['review'],$_GET['code']); ?></textarea>
                    </div>
                    <!-- Input Gambar -->
                    <div class="mb-3">
                        <label for="gambar" class="form-label fw-bold">Lampirkan Gambar Baru (Opsional)</label>
                        <input type="file" class="form-control" name="gambar" id="gambar">
                        <?php if (!empty($userReview['foto'])): ?>
                            <small>Gambar sebelumnya akan digantikan jika Anda mengunggah gambar baru.</small>
                        <?php endif; ?>
                    </div>
                    <!-- Input id user dan id parfum -->
                    <input type="hidden" name="id_user" value="<?= $_SESSION['id']; ?>">
                    <input type="hidden" name="id_parfum" value="<?= $id_parfum; ?>">
                    <input type="hidden" name="id_review" value="<?= $userReview['id_review']; ?>">
                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-primary mt-3">Update Ulasan</button>
                </div>
            </div>
        </form>
    <?php else: ?>
        <!-- Form Tambah Ulasan -->
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="card text mb-5 shadow">
                <div class="card-body">
                    <h4 class="card-title">Tambah Ulasan Anda</h4>
                    
                    <!-- Input Ulasan -->
                    <div class="mb-3">
                        <label for="ulasan" class="form-label fw-bold">Ulasan</label>
                        <textarea class="form-control" name="ulasan" id="ulasan" rows="4" placeholder="Tulis ulasan Anda di sini..." required></textarea>
                    </div>
                    
                    <!-- Input Gambar -->
                    <div class="mb-3">
                        <label for="gambar" class="form-label fw-bold">Lampirkan Gambar</label>
                        <input type="file" class="form-control" name="gambar" id="gambar" required>
                    </div>
                    <!-- Input id user dan id parfum -->
                    <input type="hidden" name="id_user" value="<?= $_SESSION['id']; ?>">
                    <input type="hidden" name="id_parfum" value="<?= $id_parfum; ?>">
                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-primary mt-3">Kirim Ulasan</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
    </div>
    </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>