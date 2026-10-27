
<?php

use CodeIgniter\Boot;
use Config\Paths;

$root = __DIR__ . DIRECTORY_SEPARATOR;

// ==================================================
// 1. BOOTSTRAP CODEIGNITER 4.7.5
// ==================================================

require_once $root . 'vendor/autoload.php';

define('FCPATH', $root . 'public' . DIRECTORY_SEPARATOR);

chdir(FCPATH);

require $root . 'app/Config/Paths.php';

$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';

if (! defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}

Boot::bootConsole($paths);

// ==================================================
// 2. KONFIGURASI PENGUJIAN
// ==================================================

$tableName = 'TABEL_UJI';
$testId    = 1001;

echo "========================================\n";
echo " TEST ORACLE CI4 - TAHAP 5\n";
echo " INSERT, UPDATE, COMMIT, ROLLBACK\n";
echo "========================================\n\n";

$db = null;
$transactionActive = false;
$testPassed = false;

try {
    // ==================================================
    // 3. KONEKSI DATABASE
    // ==================================================

    $db = \Config\Database::connect();
    $db->initialize();

    echo "[BERHASIL] Koneksi Oracle berhasil.\n";

    // Pastikan ID pengujian belum digunakan.
    $check = $db->query(
        'SELECT ID FROM TABEL_UJI WHERE ID = ?',
        [$testId]
    );

    $existing = $check->getRowArray();
    $check->freeResult();

    if ($existing !== null) {
        throw new RuntimeException(
            "ID {$testId} sudah digunakan. Pilih ID pengujian lain."
        );
    }

    // ==================================================
    // 4. TEST INSERT
    // ==================================================

    echo "\n========================================\n";
    echo "TEST 1: INSERT\n";
    echo "========================================\n";

    $db->transBegin();
    $transactionActive = true;

    $inserted = $db->query(
        "INSERT INTO TABEL_UJI
            (ID, NAMA, KETERANGAN)
         VALUES (?, ?, ?)",
        [
            $testId,
            'DATA UJI PDOOCI',
            'Pengujian INSERT melalui CI4'
        ]
    );

    if ($inserted === false) {
        throw new RuntimeException('INSERT gagal.');
    }

    echo "[BERHASIL] INSERT berhasil dijalankan.\n";

    // Pastikan baris dapat dibaca di dalam transaksi.
    $query = $db->query(
        'SELECT ID, NAMA, KETERANGAN
         FROM TABEL_UJI
         WHERE ID = ?',
        [$testId]
    );

    $row = $query->getRowArray();
    $query->freeResult();

    if ($row === null) {
        throw new RuntimeException(
            'Baris hasil INSERT tidak ditemukan.'
        );
    }

    print_r($row);

    // ==================================================
    // 5. TEST UPDATE
    // ==================================================

    echo "\n========================================\n";
    echo "TEST 2: UPDATE\n";
    echo "========================================\n";

    $updated = $db->query(
        "UPDATE TABEL_UJI
         SET NAMA = ?, KETERANGAN = ?
         WHERE ID = ?",
        [
            'DATA UJI PDOOCI UPDATE',
            'Pengujian UPDATE melalui CI4',
            $testId
        ]
    );

    if ($updated === false) {
        throw new RuntimeException('UPDATE gagal.');
    }

    echo "[BERHASIL] UPDATE berhasil dijalankan.\n";

    $query = $db->query(
        'SELECT ID, NAMA, KETERANGAN
         FROM TABEL_UJI
         WHERE ID = ?',
        [$testId]
    );

    $row = $query->getRowArray();
    $query->freeResult();

    if (
        $row === null
        || $row['NAMA'] !== 'DATA UJI PDOOCI UPDATE'
    ) {
        throw new RuntimeException(
            'Hasil UPDATE tidak sesuai harapan.'
        );
    }

    print_r($row);

    // ==================================================
    // 6. TEST COMMIT
    // ==================================================

    echo "\n========================================\n";
    echo "TEST 3: COMMIT\n";
    echo "========================================\n";

    $db->transCommit();
    $transactionActive = false;

    echo "[BERHASIL] COMMIT berhasil dijalankan.\n";

    // Pastikan data tetap ada setelah COMMIT.
    $query = $db->query(
        'SELECT ID, NAMA, KETERANGAN
         FROM TABEL_UJI
         WHERE ID = ?',
        [$testId]
    );

    $row = $query->getRowArray();
    $query->freeResult();

    if (
        $row === null
        || $row['NAMA'] !== 'DATA UJI PDOOCI UPDATE'
    ) {
        throw new RuntimeException(
            'Data tidak ditemukan setelah COMMIT.'
        );
    }

    echo "[BERHASIL] Data tersimpan setelah COMMIT.\n";
    print_r($row);

    // ==================================================
    // 7. TEST ROLLBACK
    // ==================================================

    echo "\n========================================\n";
    echo "TEST 4: ROLLBACK\n";
    echo "========================================\n";

    $db->transBegin();
    $transactionActive = true;

    $updated = $db->query(
        'UPDATE TABEL_UJI
         SET NAMA = ?
         WHERE ID = ?',
        [
            'DATA YANG HARUS DI-ROLLBACK',
            $testId
        ]
    );

    if ($updated === false) {
        throw new RuntimeException(
            'UPDATE untuk pengujian ROLLBACK gagal.'
        );
    }

    echo "Perubahan sementara berhasil dijalankan.\n";

    $db->transRollback();
    $transactionActive = false;

    echo "[BERHASIL] ROLLBACK berhasil dijalankan.\n";

    // Pastikan nilai kembali ke hasil COMMIT.
    $query = $db->query(
        'SELECT ID, NAMA, KETERANGAN
         FROM TABEL_UJI
         WHERE ID = ?',
        [$testId]
    );

    $row = $query->getRowArray();
    $query->freeResult();

    if (
        $row === null
        || $row['NAMA'] !== 'DATA UJI PDOOCI UPDATE'
    ) {
        throw new RuntimeException(
            'Nilai setelah ROLLBACK tidak sesuai harapan.'
        );
    }

    echo "[BERHASIL] ROLLBACK mengembalikan nilai sebelumnya.\n";
    print_r($row);

    $testPassed = true;

} catch (\Throwable $e) {
    echo "\n[PENGUJIAN GAGAL]\n";
    echo "Jenis error: " . get_class($e) . "\n";
    echo "Pesan      : " . $e->getMessage() . "\n";
    echo "File       : " . $e->getFile() . "\n";
    echo "Baris      : " . $e->getLine() . "\n";

} finally {
    // Jika terjadi error saat transaksi aktif,
    // batalkan transaksi yang masih berjalan.
    if ($db !== null && $transactionActive) {
        try {
            $db->transRollback();
            echo "\n[INFO] Transaksi yang belum selesai di-rollback.\n";
        } catch (\Throwable $rollbackError) {
            echo "\n[PERINGATAN] Rollback gagal: "
                . $rollbackError->getMessage() . "\n";
        }
    }

    // Bersihkan hanya baris khusus pengujian.
    // Tidak menyentuh ID = 1 atau baris lain.
    if ($db !== null) {
        try {
            $db->query(
                'DELETE FROM TABEL_UJI WHERE ID = ?',
                [$testId]
            );

            echo "[INFO] Pembersihan baris uji ID {$testId} selesai.\n";
        } catch (\Throwable $cleanupError) {
            echo "[PERINGATAN] Pembersihan gagal: "
                . $cleanupError->getMessage() . "\n";
            echo "Periksa TABEL_UJI untuk ID {$testId} secara manual.\n";
        }
    }
}

// ==================================================
// 8. RINGKASAN
// ==================================================

echo "\n========================================\n";

if ($testPassed) {
    echo " PENGUJIAN TAHAP 5 SELESAI\n";
    echo " INSERT, UPDATE, COMMIT, ROLLBACK\n";
} else {
    echo " PENGUJIAN TAHAP 5 GAGAL\n";
    echo " Periksa pesan error di atas.\n";
}

echo "========================================\n";
