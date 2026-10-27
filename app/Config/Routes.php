<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Halaman utama diarahkan ke halaman login
$routes->get('/', 'AuthController::login');

// Route pengujian Oracle
$routes->get('oracle-test', 'OracleTest::index');
$routes->get('oracle-model-test', 'OracleModelTest::index');

// Login dan logout
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attempt');
$routes->post('logout', 'AuthController::logout');

// Dashboard hanya untuk pengguna yang sudah login
$routes->get('dashboard', 'DashboardController::index', [
    'filter' => 'auth'
]);

// Ubah password hanya untuk pengguna yang sudah login
$routes->get('ubah-password', 'AuthController::changePassword', [
    'filter' => 'auth'
]);

$routes->post('ubah-password', 'AuthController::changePassword', [
    'filter' => 'auth'
]);

// ========================================
// MODUL LAPORAN STOK PAKAN JADI
// ========================================

$routes->get(
    'laporan/stok-pakan-jadi-harian',
    'LaporanStokPakanJadiController::index',
    ['filter' => 'auth']
);

$routes->get(
    'laporan/stok-pakan-jadi-harian/preview',
    'LaporanStokPakanJadiController::preview',
    ['filter' => 'auth']
);


$routes->get('_debug/render-report', static function () {
    $html = view('laporan/stok_pakan_jadi/preview', [
        'tanggal'     => date('Y-m-d'),
        'dataLaporan' => [],
        'error'       => null,
        'pageTitle'   => 'Debug Render',
    ]);

    return response()
        ->setHeader('X-Debug-Direct-View', 'yes')
        ->setContentType('text/html', 'UTF-8')
        ->setBody($html);
});