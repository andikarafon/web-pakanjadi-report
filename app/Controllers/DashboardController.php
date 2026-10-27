<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    /**
     * Menampilkan halaman dashboard.
     */
    public function index()
    {
        // Pemeriksaan tambahan untuk memastikan pengguna sudah login.
        if (session()->get('logged_in') !== true) {
            return redirect()
                ->to(site_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'userName'  => session()->get('nama')
                ?: session()->get('username'),
            'username'  => session()->get('username'),
        ]);
    }
}