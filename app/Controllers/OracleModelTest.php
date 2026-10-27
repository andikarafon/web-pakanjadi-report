<?php

namespace App\Controllers;

use App\Models\AksesKelolosanPakanModel;
use CodeIgniter\Controller;
use Throwable;

class OracleModelTest extends Controller
{
    public function index()
    {
        $data = [
            'status'  => 'GAGAL',
            'message' => '',
            'rows'    => [],
        ];

        try {
            $model = new AksesKelolosanPakanModel();

            // Tes 1: mengambil semua data
            $rows = $model
                ->orderBy('USERNAME', 'ASC')
                ->findAll(5);

            $data['status']  = 'BERHASIL';
            $data['message'] = 'Model CI4 berhasil membaca data Oracle.';
            $data['rows']    = $rows;
        } catch (Throwable $e) {
            log_message(
                'error',
                'OracleModelTest: {message}',
                ['message' => $e->getMessage()]
            );

            $data['message'] = $e->getMessage();
        }

        return view('oracle_model_test_result', $data);
    }
}
