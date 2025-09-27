<?php
require __DIR__ . '/database.php'; // koneksi database

$settings = $pdo->query("SELECT * FROM tb_settings_opik WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>
