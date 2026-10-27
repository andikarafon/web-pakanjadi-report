<?php

namespace App\Database\PDOOCI;

use CodeIgniter\Database\BaseBuilder;

class Builder extends BaseBuilder
{
    /**
     * Escape identifier menggunakan tanda kutip Oracle.
     */
    protected $escapeChar = '"';

    /**
     * Mengubah query ber-LIMIT menjadi sintaks Oracle 10g.
     */
    protected function _limit(string $sql, bool $offsetIgnore = false): string
    {
        $limit = $this->QBLimit;

        if ($limit === false || $limit === null) {
            return $sql;
        }

        $limit = (int) $limit;
        $offset = (int) ($this->QBOffset ?? 0);

        if ($limit < 0 || $offset < 0) {
            return $sql;
        }

        if ($offset === 0) {
            return 'SELECT * FROM (' . $sql . ') WHERE ROWNUM <= ' . $limit;
        }

        $upperLimit = $offset + $limit;

        return 'SELECT * FROM (
                    SELECT inner_query.*, ROWNUM AS ci_rownum
                    FROM (' . $sql . ') inner_query
                    WHERE ROWNUM <= ' . $upperLimit . '
                )
                WHERE ci_rownum > ' . $offset;
    }
}
