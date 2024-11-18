<?php
session_start();
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
        }
        h1{
            color: white;
            font-size: 80px;
        }
        p{
            color: white;
            font-size: 20px;
        }
    </style>
</head>
<body class="d-flex">
<?php if (isset($_SESSION['username'])): ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px; height:100vh">
        <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <svg class="bi pe-none me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
        <span class="fs-4">Forum Parfume</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="#" class="nav-link active" aria-current="page">
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
    <div class="right-side" style="margin-top:5%; margin-left:15px;">
        <h1>Aplikasi Forum Parfum</h1>
            <div>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            </div>
    </div>
        
    <?php else: ?>
        <div class="d-flex flex-column" style="padding-left: 10%;
            padding-top:5%;
            width: 90%;
            display: flex;
            align-items: center;">
            <h1 class="my-4">Aplikasi Forum Parfum</h1>
            <p>Website ini adalah forum komunitas parfum yang aman dan inovatif, dirancang untuk para pecinta parfum yang ingin berdiskusi, berbagi ulasan, serta menemukan rekomendasi parfum favorit.</p>
            <div>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Login
                </button>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel" style="color: black;">Form Login</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Username</label>
                                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="write ur username here" name="username">
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Password</label>
                                <input type="password" class="form-control" id="exampleFormControlInput1" placeholder="write ur password here" name="password">
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                    </div>
                </div>
                </div>
                |
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#exampleModal1">
                Register
                </button>
                <!-- Modal --> 
                <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel" style="color: black;">Form Register</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="register.php">
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Username</label>
                                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="write ur username here" name="username">
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Password</label>
                                <input type="password" class="form-control" id="exampleFormControlInput1" placeholder="write ur password here" name="password">
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

