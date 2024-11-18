<?php
include 'functions/file_encryption.php';
include '../tugasakhirkripto/functions/db.php'; // Pastikan Anda sudah memiliki file koneksi ke database
session_start();
if(!isset($_SESSION['username'])){
    header('Location:index.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['file'])) {
        $originalFileName = $_FILES['file']['name'];
        $filePath = $_FILES['file']['tmp_name'];
        $fileType = $_FILES['file']['type'];
        $fileSize = $_FILES['file']['size'] / 1024; // Size in KB
        $key = $_POST['key'];

        // Encrypt the file
        $encryptedFileName = 'Enkrip_' . $originalFileName;
        encryptFile($filePath, $key, $encryptedFileName);

        // Read encrypted file
        $encryptedFilePath = $encryptedFileName; // File hasil enkripsi
        $filePointer = fopen($encryptedFilePath, 'r');
        $fileData = fread($filePointer, filesize($encryptedFilePath));
        fclose($filePointer);
        $fileData = addslashes($fileData); // Escape binary data for database insertion

        // Ambil id_user dari session
        $id_user = $_SESSION['id'];

        // Masukkan data ke dalam database
        $stmt = $conn->prepare("INSERT INTO catatan (id_user, nama_file, tipe_file, file, key_file) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_user, $originalFileName, $fileType, $fileData, $key);

        if ($stmt->execute()) {
            $message = "File successfully encrypted and saved to database!";
        } else {
            $message = "Failed to save file to database: " . $stmt->error;
        }

    } else {
        echo "Please upload a file to encrypt.";
    }
}
    $id = $_SESSION['id'];
    // Ambil file dari database
    $query = "SELECT id_catatan, nama_file FROM catatan Where id_user='$id'";
    $result = $conn->query($query);
    $files = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $files[] = $row;
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
        .drop-zone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            font-size: 16px;
            color: #aaa;
            cursor: pointer;
        }
        .drop-zone.dragover {
            border-color: #333;
            color: #333;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #e0f7fa;
            border-radius: 5px;
        }
        body{
            background-image: url(v1016-b-09.jpg);
            background-size: cover;
            backdrop-filter: blur(5px); /* Menambahkan efek blur */
            color: white;
        }
    </style>
</head>
<body class="d-flex">

<div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px; height:100vh">
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
            <a href="list_forum.php" class="nav-link text-white">
            <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#speedometer2"></use></svg>
            Forum
            </a>
        </li>
        <li>
            <a href="#.php" class="nav-link active">
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
    <div class="right-side" style="width: 80vw;">
        <h1>Simpan Catatan Anda Di Sini</h1>
        <form id="encrypt-form" method="POST" enctype="multipart/form-data">
            <div id="drop-zone" class="drop-zone">
                Drag and drop a file here or click to upload
            </div>
            <input type="file" name="file" id="file-input" style="display: none;">
            <br><br>
            Key: <input type="number" name="key" required>
            <button class="btn btn-light" type="submit">Encrypt File</button>
        </form>
        <div class="uploaded-files">
        <h2>Uploaded Files</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?= htmlspecialchars($file['nama_file']) ?></td>
                        <td>
                            <form action="download.php" method="POST" style="display: inline;">
                                <input type="hidden" name="file_id" value="<?= $file['id_catatan'] ?>">
                                <input type="number" name="key" placeholder="Enter key" required>
                                <?php 
                                if(isset($_GET['pesan'])){ ?>
                                    <p style="color: red;">Key salah!</p>
                                <?php }?>
                                <button type="submit" class="btn btn-primary">Download</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        
        // Handle drag and drop
        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            dropZone.textContent = e.dataTransfer.files[0].name;
        }
    });

    // Display file name after selection
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            dropZone.textContent = fileInput.files[0].name;
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

