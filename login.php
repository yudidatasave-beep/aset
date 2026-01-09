<?php
session_start();
require 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND status = 'aktif'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan atau akun tidak aktif.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Aset RS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e8f0fe;
        }
        .login-container {
            max-width: 800px;
            margin: 80px auto;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            display: flex;
            flex-wrap: wrap;
        }
        .login-image {
            flex: 1 1 40%;
            background-color: #1560bd;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }
        .login-image img {
            max-width: 100%;
            height: auto;
        }
        .login-form {
            flex: 1 1 60%;
            padding: 40px;
        }
        .btn-denim {
            background-color: #1560bd;
            color: white;
        }
        .btn-denim:hover {
            background-color: #104e9b;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-image">
        <img src="assets/logo.png" alt="Logo Rumah Sakit">
    </div>
    <div class="login-form">
        <h3 class="mb-4">Manajemen Pemeliharaan Aset </h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-denim w-100">Login</button>
        </form>
    </div>
</div>

</body>
</html>
