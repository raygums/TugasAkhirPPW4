<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['contacts'])) {
    $_SESSION['contacts'] = [];
}

function generateId() {
    return uniqid('contact_');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $errors = [];
    
    if (empty($_POST['nama'])) {
        $errors[] = "Nama tidak boleh kosong";
    } elseif (strlen($_POST['nama']) < 3) {
        $errors[] = "Nama minimal 3 karakter";
    }
    
    if (empty($_POST['email'])) {
        $errors[] = "Email tidak boleh kosong";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (empty($_POST['telepon'])) {
        $errors[] = "Telepon tidak boleh kosong";
    } elseif (!preg_match('/^[0-9]{10,13}$/', $_POST['telepon'])) {
        $errors[] = "Telepon harus berisi 10-13 digit angka";
    }
    
    if (empty($_POST['alamat'])) {
        $errors[] = "Alamat tidak boleh kosong";
    }
    
    if (empty($_POST['perusahaan'])) {
        $errors[] = "Nama perusahaan tidak boleh kosong";
    }
    
    if (empty($_POST['jenis_layanan'])) {
        $errors[] = "Jenis layanan tidak boleh kosong";
    }
    
    if (empty($errors)) {
        $contact = [
            'id' => generateId(),
            'nama' => htmlspecialchars($_POST['nama']),
            'email' => htmlspecialchars($_POST['email']),
            'telepon' => htmlspecialchars($_POST['telepon']),
            'perusahaan' => htmlspecialchars($_POST['perusahaan']),
            'alamat' => htmlspecialchars($_POST['alamat']),
            'jenis_layanan' => htmlspecialchars($_POST['jenis_layanan']),
            'catatan' => htmlspecialchars($_POST['catatan'] ?? ''),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $_SESSION['contacts'][] = $contact;
        $_SESSION['success'] = "Kontak berhasil ditambahkan!";
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $errors = [];
    $id = $_POST['id'];
    
    if (empty($_POST['nama']) || strlen($_POST['nama']) < 3) {
        $errors[] = "Nama minimal 3 karakter";
    }
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    if (empty($_POST['telepon']) || !preg_match('/^[0-9]{10,13}$/', $_POST['telepon'])) {
        $errors[] = "Telepon harus berisi 10-13 digit angka";
    }
    if (empty($_POST['alamat'])) {
        $errors[] = "Alamat tidak boleh kosong";
    }
    if (empty($_POST['perusahaan'])) {
        $errors[] = "Nama perusahaan tidak boleh kosong";
    }
    if (empty($_POST['jenis_layanan'])) {
        $errors[] = "Jenis layanan tidak boleh kosong";
    }
    
    if (empty($errors)) {
        foreach ($_SESSION['contacts'] as $key => $contact) {
            if ($contact['id'] === $id) {
                $_SESSION['contacts'][$key]['nama'] = htmlspecialchars($_POST['nama']);
                $_SESSION['contacts'][$key]['email'] = htmlspecialchars($_POST['email']);
                $_SESSION['contacts'][$key]['telepon'] = htmlspecialchars($_POST['telepon']);
                $_SESSION['contacts'][$key]['perusahaan'] = htmlspecialchars($_POST['perusahaan']);
                $_SESSION['contacts'][$key]['alamat'] = htmlspecialchars($_POST['alamat']);
                $_SESSION['contacts'][$key]['jenis_layanan'] = htmlspecialchars($_POST['jenis_layanan']);
                $_SESSION['contacts'][$key]['catatan'] = htmlspecialchars($_POST['catatan'] ?? '');
                $_SESSION['success'] = "Kontak berhasil diupdate!";
                break;
            }
        }
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['edit_id'] = $id;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    foreach ($_SESSION['contacts'] as $key => $contact) {
        if ($contact['id'] === $id) {
            unset($_SESSION['contacts'][$key]);
            $_SESSION['contacts'] = array_values($_SESSION['contacts']);
            $_SESSION['success'] = "Kontak berhasil dihapus!";
            break;
        }
    }
    header('Location: index.php');
    exit;
}

$editContact = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editId = $_GET['id'];
    foreach ($_SESSION['contacts'] as $contact) {
        if ($contact['id'] === $editId) {
            $editContact = $contact;
            break;
        }
    }
}

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';
$oldInput = $_SESSION['old_input'] ?? [];

unset($_SESSION['errors'], $_SESSION['success'], $_SESSION['old_input']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenPower SRE - Admin Manajemen Kontak</title>
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
        <div class="header-right">
            <div class="user-info">
                <div class="user-icon">👤</div>
                <span>Admin <?= $_SESSION['user_nama'] ?></span>
            </div>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h1>Manajemen Kontak Klien</h1>
        
        <div class="session-info">
            Panel Admin | Total Kontak Klien: <?= count($_SESSION['contacts']) ?>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong>
                <ul style="margin-left: 20px; margin-top: 10px;">
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
        
        <div class="form-container">
            <h2><?= $editContact ? 'Edit Kontak' : 'Tambah Kontak Baru' ?></h2>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="<?= $editContact ? 'edit' : 'add' ?>">
                <?php if ($editContact): ?>
                    <input type="hidden" name="id" value="<?= $editContact['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nama">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" 
                           value="<?= $editContact ? $editContact['nama'] : ($oldInput['nama'] ?? '') ?>"
                           placeholder="Masukkan nama lengkap">
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?= $editContact ? $editContact['email'] : ($oldInput['email'] ?? '') ?>"
                           placeholder="contoh@email.com">
                </div>
                
                <div class="form-group">
                    <label for="telepon">Nomor Telepon * (10-13 digit)</label>
                    <input type="text" id="telepon" name="telepon" 
                           value="<?= $editContact ? $editContact['telepon'] : ($oldInput['telepon'] ?? '') ?>"
                           placeholder="08123456789">
                </div>
                
                <div class="form-group">
                    <label for="perusahaan">Nama Perusahaan *</label>
                    <input type="text" id="perusahaan" name="perusahaan" 
                           value="<?= $editContact ? $editContact['perusahaan'] : ($oldInput['perusahaan'] ?? '') ?>"
                           placeholder="Nama perusahaan klien">
                </div>
                
                <div class="form-group">
                    <label for="alamat">Alamat *</label>
                    <textarea id="alamat" name="alamat" 
                              placeholder="Masukkan alamat lengkap"><?= $editContact ? $editContact['alamat'] : ($oldInput['alamat'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="jenis_layanan">Jenis Layanan *</label>
                    <select id="jenis_layanan" name="jenis_layanan">
                        <option value="">Pilih Jenis Layanan</option>
                        <option value="Solar Panel" <?= ($editContact && $editContact['jenis_layanan'] == 'Solar Panel') || ($oldInput['jenis_layanan'] ?? '') == 'Solar Panel' ? 'selected' : '' ?>>Solar Panel</option>
                        <option value="Baterai Penyimpanan" <?= ($editContact && $editContact['jenis_layanan'] == 'Baterai Penyimpanan') || ($oldInput['jenis_layanan'] ?? '') == 'Baterai Penyimpanan' ? 'selected' : '' ?>>Baterai Penyimpanan</option>
                        <option value="Monitoring System" <?= ($editContact && $editContact['jenis_layanan'] == 'Monitoring System') || ($oldInput['jenis_layanan'] ?? '') == 'Monitoring System' ? 'selected' : '' ?>>Monitoring System</option>
                        <option value="Konsultasi Energi" <?= ($editContact && $editContact['jenis_layanan'] == 'Konsultasi Energi') || ($oldInput['jenis_layanan'] ?? '') == 'Konsultasi Energi' ? 'selected' : '' ?>>Konsultasi Energi</option>
                        <option value="Instalasi Lengkap" <?= ($editContact && $editContact['jenis_layanan'] == 'Instalasi Lengkap') || ($oldInput['jenis_layanan'] ?? '') == 'Instalasi Lengkap' ? 'selected' : '' ?>>Instalasi Lengkap</option>
                        <option value="Maintenance" <?= ($editContact && $editContact['jenis_layanan'] == 'Maintenance') || ($oldInput['jenis_layanan'] ?? '') == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="catatan">Catatan (Opsional)</label>
                    <textarea id="catatan" name="catatan" 
                              placeholder="Catatan tambahan tentang klien"><?= $editContact ? ($editContact['catatan'] ?? '') : ($oldInput['catatan'] ?? '') ?></textarea>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <?= $editContact ? 'Update Kontak' : 'Tambah Kontak' ?>
                    </button>
                    <?php if ($editContact): ?>
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="table-container">
            <h2>Daftar Kontak Klien</h2>
            <?php if (empty($_SESSION['contacts'])): ?>
                <div class="empty-state">
                    <p>Belum ada kontak klien yang tersimpan. Tambahkan kontak klien pertama!</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Perusahaan</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Jenis Layanan</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['contacts'] as $index => $contact): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $contact['nama'] ?></td>
                                <td><?= $contact['perusahaan'] ?></td>
                                <td><?= $contact['email'] ?></td>
                                <td><?= $contact['telepon'] ?></td>
                                <td><span class="badge"><?= $contact['jenis_layanan'] ?></span></td>
                                <td><?= date('d/m/Y H:i', strtotime($contact['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="index.php?action=edit&id=<?= $contact['id'] ?>" 
                                           class="btn btn-warning">Edit</a>
                                        <a href="index.php?action=delete&id=<?= $contact['id'] ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Yakin ingin menghapus kontak ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>