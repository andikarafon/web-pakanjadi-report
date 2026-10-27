
<?php

use CodeIgniter\Boot;
use Config\Paths;
use Config\Database;

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

chdir(FCPATH);

require FCPATH . '../app/Config/Paths.php';

$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';

// Jalankan bootstrap aplikasi sesuai lingkungan CodeIgniter.
Boot::bootSpark($paths);

// Uji koneksi menggunakan konfigurasi default.
try {
    $db = Database::connect('default');

    $query = $db->query('SELECT SYSDATE AS CURRENT_DATE FROM DUAL');

    echo "Koneksi CodeIgniter berhasil.\n";
    print_r($query->getResultArray());
} catch (Throwable $e) {
    echo "Koneksi CodeIgniter gagal.\n";
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
