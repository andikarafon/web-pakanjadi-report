<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Throwable;

class OracleTest extends BaseCommand
{
    protected $group = 'Database';

    protected $name = 'db:oracle-test';

    protected $description = 'Test koneksi Oracle melalui CodeIgniter PDO_OCI.';

    public function run(array $params)
    {
        try {
            CLI::write('Menguji koneksi Oracle melalui CodeIgniter...', 'yellow');

            $db = Database::connect('default');

            $db->initialize();

            if (! $db->connID) {
                CLI::error('Koneksi database gagal diinisialisasi.');
                CLI::write('Periksa implementasi driver App\Database\PDOOCI.', 'yellow');
                return EXIT_ERROR;
            }

            $query = $db->query(
                'SELECT SYSDATE AS CURRENT_DATE FROM DUAL'
            );

            $result = $query->getRowArray();

            CLI::write('Koneksi Oracle berhasil.', 'green');
            CLI::write('Hasil query:');

            print_r($result);

            return EXIT_SUCCESS;
        } catch (Throwable $e) {
            CLI::error('Koneksi Oracle gagal.');
            CLI::error('Jenis error: ' . get_class($e));
            CLI::error('Pesan: ' . $e->getMessage());
            CLI::error('File: ' . $e->getFile());
            CLI::error('Baris: ' . $e->getLine());

            return EXIT_ERROR;
        }
    }
}
