<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;
use InvalidArgumentException;

class LaporanStokPakanJadiModel extends Model
{
    protected $DBGroup = 'default';

    /**
     * Mengambil laporan harian stok pakan jadi.
     *
     * @param string $tanggal Format YYYY-MM-DD
     * @return array
     */
    public function getLaporanHarian(string $tanggal): array
    {
        $tanggalObj = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $tanggal
        );

        if (
            ! $tanggalObj ||
            $tanggalObj->format('Y-m-d') !== $tanggal
        ) {
            throw new InvalidArgumentException(
                'Format tanggal laporan tidak valid.'
            );
        }

        $tanggalOracle = $tanggalObj->format('Ymd');

        $db = Database::connect('default');

        $sql = <<<'SQL'
SELECT TO_DATE(:PTGL:, 'YYYYMMDD') AS TANGGAL, gg.KODEPJ, gg.NAMAPJ, gg.KELOMPOK, NVL(gg.AWAL, 0) AS AWAL_ZAK, ROUND(NVL(gg.AWAL, 0) * 50 / 1000, 2) AS AWAL_TON, NVL(gg.HPUNITI, 0) AS HPUNITI, NVL(gg.HPUNITII, 0) AS HPUNITII, NVL(gg.HPUNITIII, 0) AS HPUNITIII, NVL(gg.PENGELUARAN, 0) AS PENGELUARAN, NVL(gg.ADJ, 0) AS ADJ, NVL(gg.ADJ2, 0) AS ADJ2, (NVL(gg.AWAL, 0) + NVL(gg.HPUNITI, 0) + NVL(gg.HPUNITII, 0) + NVL(gg.HPUNITIII, 0) - NVL(gg.PENGELUARAN, 0) + NVL(gg.ADJ, 0) - NVL(gg.ADJ2, 0)) AS AKHIR_ZAK, ROUND((NVL(gg.AWAL, 0) + NVL(gg.HPUNITI, 0) + NVL(gg.HPUNITII, 0) + NVL(gg.HPUNITIII, 0) - NVL(gg.PENGELUARAN, 0) + NVL(gg.ADJ, 0) - NVL(gg.ADJ2, 0)) * 50 / 1000, 2) AS TON_ZAK, NVL(dp.SAK, 0) AS AFKIR
FROM (
    SELECT stok.KODEPJ, stok.NAMAPJ, stok.KELOMPOK, stok.AWAL, h.HPUNITI, h.HPUNITII, h.HPUNITIII, h.PENGELUARAN, h.ADJ, h.ADJ2
    FROM (
        SELECT m.KODEPJ, m.NAMAPJ, m.KELOMPOK, NVL(s.AWAL, 0) AS AWAL
        FROM M_PAKANJADI m
        LEFT JOIN (
            SELECT KODEPJ, SUM(LOLOS + PENDING + AFKIR + INDEN) AS AWAL
            FROM (
                SELECT s.KODEPJ, DECODE(s.STATUSPJ, 'LOLOS', NVL(s.AKHIR_STOK_LOLOS, 0), 0) AS LOLOS, DECODE(s.STATUSPJ, 'PENDING', NVL(s.AKHIR_STOK_PENDING, 0), 0) AS PENDING, DECODE(s.STATUSPJ, 'AFKIR', NVL(s.AKHIR_STOK_AFKIR, 0), 'TOLAK', NVL(s.AKHIR_STOK_AFKIR, 0), 0) AS AFKIR, DECODE(s.STATUSPJ, 'INDEN', NVL(s.AKHIR_STOK_INDEN, 0), 0) AS INDEN
                FROM STOK_PAKANJADI_1 s
                JOIN (
                    SELECT s.KODEPJ, NVL(s.STATUSPJ, 'PENDING') AS STATUSPJ, s.TGLTRANS, MAX(s.KODESTOK) AS KODESTOK
                    FROM STOK_PAKANJADI_1 s
                    JOIN (
                        SELECT KODEPJ, NVL(STATUSPJ, 'PENDING') AS STATUSPJ, MAX(TGLTRANS) AS TGLTRANS
                        FROM STOK_PAKANJADI_1
                        WHERE TGLTRANS < TO_DATE(:PTGL:, 'YYYYMMDD')
                        GROUP BY KODEPJ, NVL(STATUSPJ, 'PENDING')
                    ) tgl ON s.KODEPJ = tgl.KODEPJ AND NVL(s.STATUSPJ, 'PENDING') = tgl.STATUSPJ AND s.TGLTRANS = tgl.TGLTRANS
                    GROUP BY s.KODEPJ, NVL(s.STATUSPJ, 'PENDING'), s.TGLTRANS
                ) kodstok ON s.KODESTOK = kodstok.KODESTOK
            )
            GROUP BY KODEPJ
        ) s ON m.KODEPJ = s.KODEPJ
    ) stok
    LEFT JOIN (
        SELECT KODEPJ, SUM(NVL(HPUNITI, 0)) AS HPUNITI, SUM(NVL(HPUNITII, 0)) AS HPUNITII, SUM(NVL(HPUNITIII, 0)) AS HPUNITIII, SUM(NVL(PENGELUARAN, 0)) AS PENGELUARAN, SUM(NVL(ADJ, 0)) AS ADJ, SUM(NVL(ADJ2, 0)) AS ADJ2
        FROM LAP_HARSTOK
        WHERE TGL >= TO_DATE(:PTGL:, 'YYYYMMDD') AND TGL < TO_DATE(:PTGL:, 'YYYYMMDD') + 1
        GROUP BY KODEPJ
    ) h ON stok.KODEPJ = h.KODEPJ
) gg
LEFT JOIN (
    SELECT KODEPJ, SUM(SAK) AS SAK
    FROM (
        SELECT KODEPJ, TANGGAL, SUM(JUMLAHSAK) AS SAK
        FROM DETAIL_PETAK
        WHERE STATUS = 'TOLAK'
        GROUP BY KODEPJ, TANGGAL
    )
    GROUP BY KODEPJ
) dp ON dp.KODEPJ = gg.KODEPJ
WHERE gg.KODEPJ LIKE :PKODE: AND gg.KELOMPOK = 'PA' AND (NVL(gg.AWAL, 0) <> 0 OR NVL(gg.HPUNITI, 0) <> 0 OR NVL(gg.HPUNITII, 0) <> 0 OR NVL(gg.HPUNITIII, 0) <> 0 OR NVL(gg.PENGELUARAN, 0) <> 0 OR NVL(gg.ADJ, 0) <> 0 OR NVL(gg.ADJ2, 0) <> 0)
ORDER BY gg.KODEPJ
SQL;

        $query = $db->query($sql, [
            'PTGL'  => $tanggalOracle,
            'PKODE' => '%',
        ]);

        return $query->getResultArray();
    }
}