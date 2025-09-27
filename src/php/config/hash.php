<?php
include "database.php"; // koneksi PDO Anda

// Fungsi register user
function registerUser(PDO $pdo, string $name, string $username, string $password): bool {
    // Validasi sederhana
    if (empty($name) || empty($username) || empty($password)) {
        return false;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Query sesuai tabel 'users'
    $sql = "INSERT INTO tb_users_opik (name, username, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    return $stmt->execute([$name, $username, $hashedPassword]);
}

// Contoh penggunaan
$name = "Taufikhan Rayana";   // <-- sesuaikan nama
$username = "opikk";          // <-- sesuaikan username
$password = "rahasia123";     // <-- sesuaikan password

if (registerUser($pdo, $name, $username, $password)) {
    echo "User berhasil didaftarkan!";
} else {
    echo "Gagal mendaftar user!";
}
