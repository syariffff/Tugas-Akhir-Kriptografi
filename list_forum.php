<?php
session_start();
if(!isset($_SESSION['username'])){
    header('Location:index.php');
}
include '../tugasakhirkripto/functions/db.php'; // Pastikan Anda sudah memiliki file koneksi ke database

// Ambil data kartu dari database
$sql = "SELECT * FROM parfum";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forumId = $_POST['forum_id'];
    $enteredCode = $_POST['access_code'];

    // Ambil kode forum dari database berdasarkan ID forum
    $stmt = $conn->prepare("SELECT kode_forum FROM parfum WHERE id_parfum = ?");
    $stmt->bind_param("i", $forumId);
    $stmt->execute();
    $stmt->bind_result($correctCode);
    $stmt->fetch();
    $stmt->close();

    // Validasi kode
    if ($correctCode && $correctCode === $enteredCode) {
        header("Location: forum.php?id=$forumId&code=$enteredCode");
        exit;
    } else {
        $errorMessage = "Kode salah!";
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
        <?php if (isset($errorMessage)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                        <?php endif; ?>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                $counter = 0;
                while ($row = $result->fetch_assoc()) {
                    $row['deskripsi'] = strlen($row['deskripsi']) > 100 ? substr($row['deskripsi'], 0, 110) . "..." : $row['deskripsi'];

                    // Set up a new row every 3 cards
                    if ($counter > 0 && $counter % 3 == 0) {
                        echo '</div><div class="row mt-4">';
                    }
                    echo '
                    <div class="col-md-4">
                        <div class="card mb-4" style="width: 100%;">
                            <img src="' . $row['gambar'] . '" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title">' . $row['nama'] . '</h5>
                                <p class="card-text">' . $row['deskripsi'] . '</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#accessModal" data-forum-id="' . $row['id_parfum'] . '">Lihat Forum</button>
                            </div>
                        </div>
                    </div>';
                    $counter++;
                }
            } else {
                echo "<p>No perfume found in the database.</p>";
            }
            ?>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="accessModal" tabindex="-1" aria-labelledby="accessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accessModalLabel">Masukkan Kode Akses</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="forum_id" id="forum-id">
                        <div class="mb-3">
                            <label for="access-code" class="form-label">Kode Akses</label>
                            <input type="text" class="form-control" id="access-code" name="access_code" required placeholder="huruf kecil semua dan tanpa spasi">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Ambil data forum ID dan masukkan ke dalam form modal
        const modal = document.getElementById('accessModal');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const forumId = button.getAttribute('data-forum-id');
            const forumIdInput = document.getElementById('forum-id');
            forumIdInput.value = forumId;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

