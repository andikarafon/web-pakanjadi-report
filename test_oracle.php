<?php

$host = '10.100.2.244';
$port = '1521';
$serviceName = 'XE';

$dsn = "oci:dbname=//$host:$port/$serviceName;charset=AL32UTF8";

$username = 'pakanjadi';
$password = 'pakanjadi';

try {
    $pdo = new PDO($dsn, $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Koneksi Oracle berhasil!";

    $stmt = $pdo->query('SELECT * FROM v$version');

    foreach ($stmt as $row) {
        echo '<br>' . htmlspecialchars($row[0]);
    }
} catch (PDOException $e) {
    echo "Koneksi gagal: " . htmlspecialchars($e->getMessage());
}