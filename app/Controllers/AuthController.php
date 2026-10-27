<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    /**
     * Menampilkan halaman login.
     */
    public function login()
    {
        if (session()->get('logged_in') === true) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('auth/login');
    }

    /**
     * Memproses autentikasi pengguna.
     */
    public function attempt()
    {
        $rules = [
            'username' => 'required|max_length[10]',
            'password' => 'required|max_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Username dan password wajib diisi.');
        }

        $username = trim(
            (string) $this->request->getPost('username')
        );

        $password = (string) $this->request->getPost('password');

        try {
            $userModel = new UserModel();
            $user = $userModel->findByUsername($username);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Kesalahan database saat login: {message}',
                ['message' => $e->getMessage()]
            );

            return redirect()->to(site_url('login'))
                ->with('error', 'Login gagal diproses. Silakan coba lagi.');
        }

        // Validasi username dan password.
        if (
            ! is_array($user) ||
            ! isset($user['USERNAME'], $user['PASS']) ||
            ! is_string($user['PASS']) ||
            ! hash_equals((string) $user['PASS'], $password)
        ) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Username atau password salah.');
        }

        // Memperbarui waktu login terakhir di Oracle.
        try {
            $userModel->updateLastLogin((string) $user['USERNAME']);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Gagal memperbarui LASTLOGIN: {message}',
                ['message' => $e->getMessage()]
            );
        }

        // Regenerasi session setelah login berhasil.
        session()->regenerate(true);

        session()->set([
            'logged_in' => true,
            'username'  => (string) $user['USERNAME'],
            'nama'      => (string) (
                $user['NAMAUSER'] ?? $user['USERNAME']
            ),
        ]);

        return redirect()->to(site_url('dashboard'))
            ->with('success', 'Login berhasil.');
    }

    /**
     * Mengakhiri session pengguna.
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'))
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Menampilkan form dan memproses perubahan password.
     */
    public function changePassword()
    {
        // Pastikan pengguna sudah login.
        if (session()->get('logged_in') !== true) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // GET: tampilkan form ubah password.
        if (strtolower($this->request->getMethod()) === 'get') {
            return view('auth/change_password');
        }

        // POST: validasi input password.
        $rules = [
            'old_password' => 'required|max_length[10]',
            'new_password' => 'required|max_length[10]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('ubah-password'))
                ->withInput()
                ->with(
                    'error',
                    'Periksa kembali input password Anda.'
                );
        }

        $username = (string) session()->get('username');

        $oldPassword = (string) $this->request->getPost(
            'old_password'
        );

        $newPassword = (string) $this->request->getPost(
            'new_password'
        );

        // Password baru harus berbeda dari password lama.
        if ($newPassword === $oldPassword) {
            return redirect()->to(site_url('ubah-password'))
                ->with('error', 'Password baru harus berbeda dari password lama.');
        }

        try {
            $userModel = new UserModel();

            // Ambil ulang akun dari database Oracle.
            $user = $userModel->findByUsername($username);

            // Verifikasi password lama.
            if (
                ! is_array($user) ||
                ! isset($user['PASS']) ||
                ! is_string($user['PASS']) ||
                ! hash_equals($user['PASS'], $oldPassword)
            ) {
                return redirect()->to(site_url('ubah-password'))
                    ->with('error', 'Password lama tidak sesuai.');
            }

            // Simpan password baru ke database.
            $userModel->updatePassword($username, $newPassword);

        } catch (\Throwable $e) {
            log_message(
                'error',
                'Gagal mengubah password: {message}',
                ['message' => $e->getMessage()]
            );

            return redirect()->to(site_url('ubah-password'))
                ->with('error', 'Password gagal diubah. Silakan coba lagi.');
        }

        // Akhiri session lama setelah password berhasil diubah.
        session()->destroy();

        return redirect()->to(site_url('login'))
            ->with(
                'success',
                'Password berhasil diubah. Silakan login kembali.'
            );
    }
}
