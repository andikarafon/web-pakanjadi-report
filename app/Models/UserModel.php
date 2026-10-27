<?php

namespace App\Models;

class UserModel
{
    /**
     * Mengambil data akun berdasarkan username.
     */
    public function findByUsername(string $username): ?array
    {
        $db = db_connect();

        $sql = '
            SELECT
                USERNAME,
                NAMAUSER,
                PASS
            FROM M_USER
            WHERE USERNAME = ?
        ';

        $query = $db->query($sql, [$username]);

        $user = $query->getRowArray();

        return $user ?: null;
    }

    /**
     * Memperbarui waktu login terakhir.
     */
    public function updateLastLogin(string $username): void
    {
        $db = db_connect();

        $sql = '
            UPDATE M_USER
            SET LASTLOGIN = SYSDATE
            WHERE USERNAME = ?
        ';

        $db->query($sql, [$username]);
    }

    /**
     * Memperbarui password dan tanggal perubahan.
     */
    public function updatePassword(
        string $username,
        string $newPassword
    ): void {
        $db = db_connect();

        $sql = '
            UPDATE M_USER
            SET
                PASS = ?,
                UPDATEDDATE = SYSDATE
            WHERE USERNAME = ?
        ';

        $db->query($sql, [$newPassword, $username]);
    }
}
