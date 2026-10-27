<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="report-page">
    <div class="report-heading">
        <div>
            <h1>Laporan Harian Stok Pakan Jadi</h1>
            <p>Monitoring stok, produksi, dan pengeluaran pakan jadi.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="report-alert report-alert-error">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="report-card">
        <h2>Kriteria Laporan</h2>

        <form
            action="<?= site_url('laporan/stok-pakan-jadi-harian/preview') ?>"
            method="get"
        >
            <div class="report-filter-grid">
                <div class="report-field">
                    <label for="tanggal">Tanggal Laporan</label>
                    <input
                        type="date"
                        id="tanggal"
                        name="tanggal"
                        value="<?= esc($tanggal) ?>"
                        required
                    >
                </div>

                <div class="report-field">
                    <label>Kelompok Pakan</label>
                    <input type="text" value="PA - Pakan Jadi" readonly>
                </div>

                <div class="report-field">
                    <label>Filter Pakan</label>
                    <input type="text" value="Semua Pakan" readonly>
                    <small>Seluruh kode pakan kelompok PA.</small>
                </div>
            </div>

            <div class="report-actions">
                <button type="submit" class="report-button">
                    Tampilkan Laporan
                </button>

                <button
                    type="reset"
                    class="report-button report-button-secondary"
                >
                    Reset
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.report-page{padding:24px;max-width:1600px;margin:0 auto}
.report-heading{margin-bottom:24px}
.report-heading h1{font-size:25px;margin:0 0 8px;font-weight:700}
.report-heading p{margin:0;color:#64748b}
.report-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(15,23,42,.04)}
.report-card h2{font-size:18px;margin:0 0 22px}
.report-filter-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
.report-field{display:flex;flex-direction:column;gap:8px}
.report-field label{font-size:14px;font-weight:600;color:#334155}
.report-field input{box-sizing:border-box;width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:7px;font:inherit;background:#fff;color:#0f172a}
.report-field input[readonly]{background:#f8fafc}
.report-field small{color:#64748b;font-size:12px}
.report-actions{display:flex;gap:10px;margin-top:24px}
.report-button{border:0;border-radius:7px;padding:11px 18px;background:#2563eb;color:white;font-weight:600;cursor:pointer;text-decoration:none;font-size:14px}
.report-button-secondary{background:#e2e8f0;color:#334155}
.report-alert{padding:12px 16px;border-radius:8px;margin-bottom:16px}
.report-alert-error{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
@media(max-width:768px){.report-page{padding:16px}.report-filter-grid{grid-template-columns:1fr}.report-card{padding:18px}}
</style>

<?= $this->endSection() ?>
