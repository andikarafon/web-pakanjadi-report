
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="welcome-section">
    <div>
        <div class="eyebrow">WORKSPACE</div>

        <h1>Selamat datang, <?= esc($userName ?? 'Pengguna') ?>!</h1>

        <p>
            Selamat bekerja. Silakan pilih menu untuk mengakses
            informasi dan laporan yang Anda butuhkan.
        </p>
    </div>

    <div class="welcome-decoration" aria-hidden="true">
        <div class="decoration-square decoration-square-one"></div>
        <div class="decoration-square decoration-square-two"></div>
        <div class="decoration-circle"></div>
    </div>
</section>

<section class="section-heading">
    <div>
        <h2>Ringkasan Sistem</h2>
        <p>Informasi umum aplikasi reporting.</p>
    </div>
</section>

<section class="dashboard-grid">

    <article class="dashboard-card">
        <div class="card-top">
            <div class="card-icon card-icon-blue">▤</div>
            <span class="card-label">MODUL</span>
        </div>

        <h3>Laporan Produksi</h3>
        <p class="card-description">
            Akses laporan produksi yang nantinya terhubung
            dengan database Oracle.
        </p>

        <div class="card-bottom">
            <span class="status-dot status-pending"></span>
            <span>Dalam persiapan</span>
        </div>
    </article>

    <article class="dashboard-card">
        <div class="card-top">
            <div class="card-icon card-icon-green">◉</div>
            <span class="card-label">AKUN</span>
        </div>

        <h3>Informasi Pengguna</h3>
        <p class="card-description">
            Username:
            <strong><?= esc($username ?? '-') ?></strong>
        </p>

        <div class="card-bottom">
            <span class="status-dot status-active"></span>
            <span>Sesi login aktif</span>
        </div>
    </article>

    <article class="dashboard-card">
        <div class="card-top">
            <div class="card-icon card-icon-purple">⚙</div>
            <span class="card-label">PENGATURAN</span>
        </div>

        <h3>Keamanan Akun</h3>
        <p class="card-description">
            Perbarui password untuk menjaga keamanan akun Anda.
        </p>

        <div class="card-bottom">
            <a href="<?= site_url('ubah-password') ?>" class="card-link">
                Ubah password &rarr;
            </a>
        </div>
    </article>

</section>

<section class="section-heading section-heading-spaced">
    <div>
        <h2>Akses Cepat</h2>
        <p>Pintasan menuju fitur aplikasi.</p>
    </div>
</section>

<section class="quick-access-grid">

    <a href="<?= site_url('ubah-password') ?>" class="quick-access-card">
        <div class="quick-access-icon">⚿</div>

        <div class="quick-access-text">
            <strong>Ubah Password</strong>
            <span>Perbarui password akun Anda</span>
        </div>

        <span class="quick-access-arrow">&rarr;</span>
    </a>

    <div class="quick-access-card quick-access-disabled">
        <div class="quick-access-icon">▤</div>

        <div class="quick-access-text">
            <strong>Modul Laporan</strong>
            <span>Akan tersedia setelah modul laporan dibuat</span>
        </div>

        <span class="quick-access-arrow">—</span>
    </div>

</section>

<?= $this->endSection() ?>
