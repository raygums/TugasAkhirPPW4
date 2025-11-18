<?php
session_start();

if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ['email' => 'admin@greenpower.com', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'nama' => 'Admin GreenPower']
    ];
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($nama)) {
        $errors[] = "Nama tidak boleh kosong";
    }
    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak sama";
    }
    
    foreach ($_SESSION['users'] as $user) {
        if ($user['email'] === $email) {
            $errors[] = "Email sudah terdaftar";
            break;
        }
    }
    
    if (empty($errors)) {
        $_SESSION['users'][] = [
            'email' => htmlspecialchars($email),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'nama' => htmlspecialchars($nama)
        ];
        $_SESSION['register_success'] = "Registrasi berhasil! Silakan login.";
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GreenPower SRE</title>
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
            <h1>Daftar Akun</h1>
            <p>Buat akun admin GreenPower SRE</p>
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
        
        <div class="auth-card">
            <h2>Register</h2>
            <p>Lengkapi data di bawah ini</p>
            
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="nama">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" 
                           placeholder="Nama Anda" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           placeholder="email@greenpower.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Min. 6 karakter" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           placeholder="Ulangi password" required>
                </div>
                
                <button type="submit" class="btn btn-register">Daftar Sekarang</button>
            </form>
            
            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
