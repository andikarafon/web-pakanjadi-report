<?php

namespace App\Controllers;

use App\Models\LaporanStokPakanJadiModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Throwable;

class LaporanStokPakanJadiController extends BaseController
{
    /**
     * Menampilkan halaman laporan stok pakan jadi harian.
     */
        /**
         * Menampilkan halaman laporan stok pakan jadi harian.
         */
        public function index(): string|ResponseInterface
        {
            $tanggal = date('Y-m-d');

            return view('laporan/stok_pakan_jadi/index', [
                'tanggal'   => $tanggal,
                'pageTitle' => 'Laporan Stok Pakan Jadi Harian',
            ]);
        }

    /**
     * Menampilkan preview laporan stok pakan jadi harian.
     */
    public function preview(): string|ResponseInterface
    {
        $requestId = date('YmdHis') . '-' . bin2hex(random_bytes(5));

        log_message(
            'error',
            '[STOK-REPORT][{id}] Preview dipanggil. URL: {url}',
            [
                'id'  => $requestId,
                'url' => (string) $this->request->getUri(),
            ]
        );

        try {
            /*
             * 1. Validasi tanggal laporan.
             */
            $tanggal = trim((string) $this->request->getGet('tanggal'));

            if ($tanggal === '') {
                $tanggal = Time::today('Asia/Jakarta')->toDateString();
            }

            $tanggalValid = false;

            if (
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) === 1
            ) {
                $dateObject = \DateTimeImmutable::createFromFormat(
                    '!Y-m-d',
                    $tanggal
                );

                $tanggalValid = $dateObject !== false
                    && $dateObject->format('Y-m-d') === $tanggal;
            }

            if (! $tanggalValid) {
                log_message(
                    'error',
                    '[STOK-REPORT][{id}] Format tanggal tidak valid: {tanggal}',
                    [
                        'id'      => $requestId,
                        'tanggal' => $tanggal,
                    ]
                );

                return $this->response
                    ->setStatusCode(400)
                    ->setBody('Format tanggal tidak valid. Gunakan YYYY-MM-DD.');
            }

            log_message(
                'error',
                '[STOK-REPORT][{id}] Tanggal laporan: {tanggal}',
                [
                    'id'      => $requestId,
                    'tanggal' => $tanggal,
                ]
            );

            /*
             * 2. Ambil data dari model Oracle.
             */
            $model = new LaporanStokPakanJadiModel();

            log_message(
                'error',
                '[STOK-REPORT][{id}] Model: {model}',
                [
                    'id'    => $requestId,
                    'model' => get_class($model),
                ]
            );

            $data = $model->getLaporanHarian($tanggal);

            if (! is_array($data)) {
                throw new \RuntimeException(
                    'Model getLaporanHarian() tidak mengembalikan array.'
                );
            }

            log_message(
                'error',
                '[STOK-REPORT][{id}] Jumlah baris data: {jumlah}',
                [
                    'id'     => $requestId,
                    'jumlah' => count($data),
                ]
            );

            /*
             * 3. Log lokasi file view dan layout.
             */
            log_message(
                'error',
                '[DEBUG-VIEW] Render view laporan/stok_pakan_jadi/preview'
            );

            log_message(
                'error',
                '[DEBUG-VIEW] Layout path: '
                    . APPPATH . 'Views/layouts/main.php'
            );

            log_message(
                'error',
                '[STOK-REPORT][{id}] File view tersedia: {status}',
                [
                    'id'     => $requestId,
                    'status' => is_file(
                        APPPATH . 'Views/laporan/stok_pakan_jadi/preview.php'
                    ) ? 'YA' : 'TIDAK',
                ]
            );

            log_message(
                'error',
                '[STOK-REPORT][{id}] File layout tersedia: {status}',
                [
                    'id'     => $requestId,
                    'status' => is_file(
                        APPPATH . 'Views/layouts/main.php'
                    ) ? 'YA' : 'TIDAK',
                ]
            );

            /*
             * 4. Render view menjadi HTML.
             */
            $html = view('laporan/stok_pakan_jadi/preview', [
                'tanggal'     => $tanggal,
                'dataLaporan' => $data,
                'error'       => null,
                'pageTitle'   => 'Laporan Stok Pakan Jadi Harian',
            ]);

            /*
             * 5. Debug HTML hasil render view.
             */
            log_message(
                'error',
                '[DEBUG-VIEW] Panjang HTML: ' . strlen($html)
            );

            log_message(
                'error',
                '[DEBUG-VIEW] Ada appJsScript: '
                    . (strpos($html, 'appJsScript') !== false
                        ? 'YA'
                        : 'TIDAK')
            );

            log_message(
                'error',
                '[DEBUG-VIEW] Ada assets/js/app.js: '
                    . (strpos($html, 'assets/js/app.js') !== false
                        ? 'YA'
                        : 'TIDAK')
            );

            log_message(
                'error',
                '[DEBUG-VIEW] Ada judul Dashboard: '
                    . (strpos($html, 'judulDashboard') !== false
                        ? 'YA'
                        : 'TIDAK')
            );

            log_message(
                'error',
                '[DEBUG-VIEW] Ada penutup body: '
                    . (strpos($html, '</body>') !== false
                        ? 'YA'
                        : 'TIDAK')
            );

            log_message(
                'error',
                '[STOK-REPORT][{id}] HTML sebelum setBody: {panjang}',
                [
                    'id'      => $requestId,
                    'panjang' => strlen($html),
                ]
            );

            log_message(
                'error',
                '[STOK-REPORT][{id}] appJsScript sebelum setBody: {status}',
                [
                    'id'     => $requestId,
                    'status' => strpos($html, 'appJsScript') !== false
                        ? 'ADA'
                        : 'TIDAK ADA',
                ]
            );

            /*
             * 6. Buat response dan pasang HTML hasil render.
             */
            $response = service('response');

            $response->setStatusCode(200);
            $response->setContentType('text/html', 'UTF-8');
            $response->setHeader('X-Debug-Request-ID', $requestId);
            $response->setHeader(
                'X-Debug-AppJS',
                strpos($html, 'assets/js/app.js') !== false
                    ? 'present'
                    : 'missing'
            );

            $response->setBody($html);

            /*
             * 7. Debug isi body setelah setBody().
             */
            $finalBody = (string) $response->getBody();

            log_message(
                'error',
                '[DEBUG-FINAL] HTML setelah setBody: '
                    . strlen($finalBody)
            );

            log_message(
                'error',
                '[DEBUG-FINAL] appJsScript setelah setBody: '
                    . (strpos($finalBody, 'appJsScript') !== false
                        ? 'ADA'
                        : 'TIDAK ADA')
            );

            log_message(
                'error',
                '[STOK-REPORT][{id}] assets/js/app.js setelah setBody: {status}',
                [
                    'id'     => $requestId,
                    'status' => strpos($finalBody, 'assets/js/app.js') !== false
                        ? 'ADA'
                        : 'TIDAK ADA',
                ]
            );

            log_message(
                'error',
                '[STOK-REPORT][{id}] Panjang response final: {panjang}',
                [
                    'id'      => $requestId,
                    'panjang' => strlen($finalBody),
                ]
            );

            log_message(
                'error',
                '[STOK-REPORT][{id}] Mengembalikan response HTTP 200',
                ['id' => $requestId]
            );

            return $response;
        } catch (Throwable $e) {
            log_message(
                'critical',
                '[STOK-REPORT][{id}] Exception: {message} pada {file}:{line}',
                [
                    'id'      => $requestId,
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]
            );

            return $this->response
                ->setStatusCode(500)
                ->setBody(
                    'Terjadi kesalahan saat memproses laporan. '
                    . 'Request ID: '
                    . esc($requestId)
                );
        }
    }
}
