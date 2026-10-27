
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $formatAngka = static function ($nilai, int $desimal = 0): string {
        return number_format((float) ($nilai ?? 0), $desimal, ',', '.');
    };

    $kolomTotal = [
        'AWAL_ZAK',
        'AWAL_TON',
        'HPUNITI',
        'HPUNITII',
        'HPUNITIII',
        'PENGELUARAN',
        'ADJ',
        'ADJ2',
        'AKHIR_ZAK',
        'TON_ZAK',
        'AFKIR',
    ];

    $total = array_fill_keys($kolomTotal, 0);

    foreach ($dataLaporan as $item) {
        foreach ($kolomTotal as $kolom) {
            $total[$kolom] += (float) ($item[$kolom] ?? 0);
        }
    }

    $tanggalFormat = date('d-m-Y', strtotime($tanggal));
?>

<div class="report-page">

    <div class="report-heading report-heading-flex">
        <div>
            <h1>Laporan Harian Stok Pakan Jadi</h1>
            <p>
                Tanggal:
                <strong><?= esc($tanggalFormat) ?></strong>
                &nbsp;|&nbsp;
                Kelompok: <strong>PA</strong>
                &nbsp;|&nbsp;
                Semua Pakan
            </p>
        </div>

        <div class="report-actions report-actions-top">
            <a
                href="<?= site_url('laporan/stok-pakan-jadi-harian') ?>"
                class="report-button report-button-secondary"
            >
                Kembali
            </a>

            <button
                type="button"
                onclick="window.print()"
                class="report-button"
            >
                Cetak Laporan
            </button>
        </div>
    </div>

    <?php if (! empty($error)): ?>

        <div class="report-alert report-alert-error">
            <?= esc($error) ?>
        </div>

    <?php else: ?>

        <div class="report-card report-table-card">

            <div class="report-print-heading">
                <h2>LAPORAN HARIAN STOK PAKAN JADI</h2>
                <p>
                    Tanggal:
                    <?= esc($tanggalFormat) ?>
                </p>
            </div>

            <?php if (empty($dataLaporan)): ?>

                <div class="report-empty">
                    Tidak ada data stok pakan jadi untuk tanggal ini.
                </div>

            <?php else: ?>

                <div class="report-table-wrapper">

                    <table class="report-table">

                        <thead>
                            <tr>
                                <th rowspan="2">No.</th>
                                <th rowspan="2">Jenis Pakan Jadi</th>
                                <th colspan="2">Stock Awal</th>
                                <th colspan="3">Produksi (Zak)</th>
                                <th colspan="2">Pengeluaran</th>
                                <th rowspan="2">(+) Zak</th>
                                <th rowspan="2">(-) Zak</th>
                                <th colspan="2">Stock Akhir</th>
                                <th rowspan="2">Afkir (Zak)</th>
                            </tr>

                            <tr>
                                <th>Zak</th>
                                <th>Ton</th>
                                <th>Unit I</th>
                                <th>Unit II</th>
                                <th>Unit III</th>
                                <th>Zak</th>
                                <th>Ton</th>
                                <th>Zak</th>
                                <th>Ton</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($dataLaporan as $index => $row): ?>

                                <tr>
                                    <td class="text-center">
                                        <?= $index + 1 ?>
                                    </td>

                                    <td class="product-name">
                                        <?= esc($row['NAMAPJ'] ?? '') ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['AWAL_ZAK'] ?? 0) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['AWAL_TON'] ?? 0, 2) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['HPUNITI'] ?? 0) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['HPUNITII'] ?? 0) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['HPUNITIII'] ?? 0) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['PENGELUARAN'] ?? 0) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka(
                                            (float) ($row['PENGELUARAN'] ?? 0) * 50 / 1000,
                                            2
                                        ) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['ADJ'] ?? 0) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['ADJ2'] ?? 0) ?>
                                    </td>

                                    <td class="number stock-ending">
                                        <?= $formatAngka($row['AKHIR_ZAK'] ?? 0) ?>
                                    </td>

                                    <td class="number stock-ending">
                                        <?= $formatAngka($row['TON_ZAK'] ?? 0, 2) ?>
                                    </td>

                                    <td class="number">
                                        <?= $formatAngka($row['AFKIR'] ?? 0) ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                            <!-- BARIS TOTAL -->
                            <tr class="report-total-row">
                                <td colspan="2" class="text-center">
                                    TOTAL
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['AWAL_ZAK']) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['AWAL_TON'], 2) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['HPUNITI']) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['HPUNITII']) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['HPUNITIII']) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['PENGELUARAN']) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka(
                                        $total['PENGELUARAN'] * 50 / 1000,
                                        2
                                    ) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['ADJ']) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['ADJ2']) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['AKHIR_ZAK']) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['TON_ZAK'], 2) ?>
                                </td>

                                <td class="number">
                                    <?= $formatAngka($total['AFKIR']) ?>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <div class="report-table-info">
                    Jumlah jenis pakan:
                    <?= count($dataLaporan) ?>
                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>

<style>
.report-page {
    padding: 24px;
    max-width: 1800px;
    margin: 0 auto;
}

.report-heading {
    margin-bottom: 22px;
}

.report-heading h1 {
    font-size: 25px;
    margin: 0 0 8px;
    font-weight: 700;
}

.report-heading p {
    margin: 0;
    color: #64748b;
}

.report-heading-flex {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.report-actions {
    display: flex;
    gap: 10px;
}

.report-actions-top {
    margin-top: 0;
}

.report-button {
    display: inline-block;
    border: 0;
    border-radius: 7px;
    padding: 11px 18px;
    background: #2563eb;
    color: white;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
}

.report-button-secondary {
    background: #e2e8f0;
    color: #334155;
}

.report-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
}

.report-table-card {
    overflow: hidden;
}

.report-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    color: #1e293b;
    white-space: nowrap;
}

.report-table th,
.report-table td {
    border: 1px solid #cbd5e1;
    padding: 9px 10px;
}

.report-table thead th {
    background: #f1f5f9;
    text-align: center;
    vertical-align: middle;
    font-weight: 700;
}

.report-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

.report-table tbody tr:hover {
    background: #eff6ff;
}

.report-table .text-center {
    text-align: center;
}

.report-table .number {
    text-align: right;
    font-variant-numeric: tabular-nums;
}

.report-table .product-name {
    font-weight: 500;
    white-space: normal;
    min-width: 160px;
}

.report-table .stock-ending {
    font-weight: 700;
}

/* Baris total */
.report-table .report-total-row td {
    background: #dbeafe;
    font-weight: 700;
    border-top: 2px solid #64748b;
    color: #0f172a;
}

.report-table-info {
    margin-top: 16px;
    font-size: 13px;
    color: #64748b;
}

.report-empty {
    padding: 32px;
    text-align: center;
    color: #64748b;
}

.report-alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
}

.report-alert-error {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

.report-print-heading {
    display: none;
}

@media (max-width: 768px) {
    .report-page {
        padding: 14px;
    }

    .report-card {
        padding: 12px;
    }

    .report-heading h1 {
        font-size: 21px;
    }
}

@media print {
    @page {
        size: landscape;
        margin: 10mm;
    }

    body {
        background: #fff !important;
    }

    .sidebar,
    .sidebar-overlay,
    .topbar,
    .navbar,
    .report-actions,
    .report-heading {
        display: none !important;
    }

    .main-content,
    .main,
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    .report-page {
        padding: 0 !important;
        max-width: none !important;
    }

    .report-card {
        border: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .report-table-wrapper {
        overflow: visible !important;
    }

    .report-table {
        font-size: 9px;
    }

    .report-table th,
    .report-table td {
        padding: 5px;
    }

    .report-table thead th {
        background: #e5e7eb !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .report-table .report-total-row td {
        background: #dbeafe !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .report-print-heading {
        display: block;
        text-align: center;
        margin-bottom: 14px;
    }

    .report-print-heading h2 {
        font-size: 16px;
        margin: 0 0 6px;
    }

    .report-print-heading p {
        font-size: 11px;
        margin: 0;
    }

    .report-table tbody tr {
        break-inside: avoid;
    }
}
</style>

<?= $this->endSection() ?>
