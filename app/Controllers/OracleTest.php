<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Throwable;

class OracleTest extends Controller
{
    public function index()
    {
        $data = [
            'status' => 'GAGAL',
            'message' => '',
            'rows' => [],
            'version' => '',
        ];

        try {
            $db = \Config\Database::connect();
            $db->initialize();

            // Test 1: koneksi dan query Oracle
            $query = $db->query(
                'SELECT 1 AS HASIL FROM DUAL'
            );

            $result = $query->getRowArray();
            $query->freeResult();

            if ($result === null) {
                throw new \RuntimeException(
                    'Query DUAL tidak menghasilkan data.'
                );
            }

            // Test 2: Query Builder
            $builder = $db->table('AKSES_KELOLOSANPAKAN');

            $query = $builder
                ->select('USERNAME, STATUS_AKSES')
                ->orderBy('USERNAME', 'ASC')
                ->limit(5)
                ->get();

            $rows = $query->getResultArray();
            $query->freeResult();

            $data['status'] = 'BERHASIL';
            $data['message'] = 'Koneksi Oracle dan Query Builder berhasil.';
            $data['rows'] = $rows;

        } catch (Throwable $e) {
            log_message(
                'error',
                'OracleTest: {message}',
                ['message' => $e->getMessage()]
            );

            $data['message'] = $e->getMessage();
        }

        return view('oracle_test_result', $data);
    }
}
