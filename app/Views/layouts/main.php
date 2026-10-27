
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= esc($pageTitle ?? 'Dashboard') ?> - Web Pakanjadi Report</title>

    <!-- CSS utama -->
    <link
        rel="stylesheet"
        href="<?= base_url('assets/css/app.css') ?>"
    >
</head>
<body>

<?php
    // Identitas pengguna dari session login.
    $session = session();

    $namaUser = trim((string) $session->get('nama'));
    $usernameSession = trim((string) $session->get('username'));

    $displayName = $namaUser !== ''
        ? $namaUser
        : ($usernameSession !== '' ? $usernameSession : 'Pengguna');

    $initial = strtoupper(substr($displayName, 0, 1));

    // Menentukan halaman aktif.
    $uri = service('uri');
    $segment1 = $uri->getSegment(1);
    $segment2 = $uri->getSegment(2);

    $isDashboard = $segment1 === 'dashboard';

    $isLaporanStok = (
        $segment1 === 'laporan'
        && $segment2 === 'stok-pakan-jadi-harian'
    );

    $isUbahPassword = $segment1 === 'ubah-password';
?>

<div class="app-layout">

    <!-- Overlay untuk tampilan mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">
            <div class="brand-icon">PR</div>

            <div class="brand-text">
                <strong>PAKANJADI</strong>
                <span>Reporting System</span>
            </div>
        </div>

        <div class="sidebar-section-label">MENU UTAMA</div>

        <nav class="sidebar-nav">

            <a
                href="<?= site_url('dashboard') ?>"
                class="nav-link <?= $isDashboard ? 'active' : '' ?>"
            >
                <span class="nav-icon">▦</span>
                <span>Dashboard</span>
            </a>

            <a
                href="<?= site_url('laporan/stok-pakan-jadi-harian') ?>"
                class="nav-link <?= $isLaporanStok ? 'active' : '' ?>"
            >
                <span class="nav-icon">▤</span>
                <span>Laporan Stok Harian</span>
            </a>

        </nav>

        <div class="sidebar-section-label">AKUN</div>

        <nav class="sidebar-nav">

            <a
                href="<?= site_url('ubah-password') ?>"
                class="nav-link <?= $isUbahPassword ? 'active' : '' ?>"
            >
                <span class="nav-icon">⚿</span>
                <span>Ubah Password</span>
            </a>

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-title">
                WEB PAKANJADI REPORT
            </div>

            <div class="sidebar-footer-text">
                Reporting System
            </div>
        </div>

    </aside>

    <!-- Area utama -->
    <div class="main-area">

        <!-- Header -->
        <header class="topbar">

            <div class="topbar-left">

                <!-- Tombol menu mobile -->
                <button
                    type="button"
                    class="menu-toggle"
                    id="menuToggle"
                    aria-label="Buka menu"
                    aria-expanded="false"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Tombol expand/collapse sidebar -->
                <button
                    type="button"
                    class="sidebar-collapse-toggle"
                    id="sidebarCollapseToggle"
                    aria-label="Kecilkan sidebar"
                    aria-expanded="true"
                    title="Kecilkan sidebar"
                >
                    <span id="sidebarToggleIcon">◀</span>
                </button>

                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <span>Workspace</span>
                    <span class="breadcrumb-separator">/</span>
                    <strong><?= esc($pageTitle ?? 'Dashboard') ?></strong>
                </div>

            </div>

            <!-- Identitas pengguna -->
            <div class="topbar-user">

                <div class="user-avatar">
                    <?= esc($initial) ?>
                </div>

                <div class="user-info">
                    <strong><?= esc($displayName) ?></strong>
                    <span><?= esc($usernameSession) ?></span>
                </div>

                <!-- Logout -->
                <form
                    action="<?= site_url('logout') ?>"
                    method="post"
                    class="logout-form"
                >
                    <?= csrf_field() ?>

                    <button
                        type="submit"
                        class="logout-button"
                        title="Logout"
                    >
                        Logout
                    </button>
                </form>

            </div>

        </header>

        <!-- Konten halaman -->
        <main class="page-content">

            <?php if ($session->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= esc($session->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <?php if ($session->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    <?= esc($session->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>

        </main>

        <!-- Footer -->
        <footer class="page-footer">
            <span>Web Pakanjadi Report</span>
            <span>&copy; <?= date('Y') ?> — Reporting System</span>
        </footer>

    </div>

</div>

<!-- LAYOUT_TEST_20261027 -->
<script
    id="appJsScript"
    src="<?= base_url('assets/js/app.js') ?>"
></script>

<?= $this->renderSection('scripts') ?>

</body>
</html>
