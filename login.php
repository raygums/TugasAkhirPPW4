<?php
session_start();

if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ['email' => 'admin@greenpower.com', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'nama' => 'Admin GreenPower']
    ];
}

$errors = [];
$success = '';

if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong";
    }
    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong";
    }
    
    if (empty($errors)) {
        $found = false;
        foreach ($_SESSION['users'] as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nama'] = $user['nama'];
                header('Location: index.php');
                exit;
            }
        }
        if (!$found) {
            $errors[] = "Email atau password salah";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GreenPower SRE</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <div class="logo">
            <div class="logo-icon">▲</div>
            <div class="logo-text">
                GreenPower <span class="sre">SRE</span>
            </div>
        </div>
    </div>
    
    <div class="container" style="max-width: 500px; margin: 60px auto;">
        <div class="page-title">
            <h1>Login Admin</h1> 
            <p>Masuk ke Sistem Manajemen Kontak GreenPower SRE</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>Sukses!</strong> <?= $success ?>
            </div>
        <?php endif; ?>
        
        <div class="auth-card">
            <h2>Login</h2>
            <p>Masuk ke akun admin Anda</p>
            
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           placeholder="admin@greenpower.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" 
                           placeholder="••••••••" required>
                </div>
                
                <div class="forgot-password">
                    <a href="#">Lupa password?</a>
                </div>
                
                <button type="submit" class="btn btn-login">Masuk</button>
            </form>
            
            <div class="register-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
