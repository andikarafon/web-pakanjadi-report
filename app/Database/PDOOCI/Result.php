<?php

namespace App\Database\PDOOCI;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Entity\Entity;
use PDO;
use PDOStatement;
use stdClass;

class Result extends BaseResult
{
    /**
     * Mendapatkan jumlah kolom dari hasil query.
     */
    public function getFieldCount(): int
    {
        if (! $this->resultID instanceof PDOStatement) {
            return 0;
        }

        return $this->resultID->columnCount();
    }

    /**
     * Mendapatkan nama-nama kolom.
     */
    public function getFieldNames(): array
    {
        $fields = [];

        if (! $this->resultID instanceof PDOStatement) {
            return $fields;
        }

        for ($i = 0; $i < $this->getFieldCount(); $i++) {
            $meta = $this->resultID->getColumnMeta($i);

            if ($meta !== false && isset($meta['name'])) {
                $fields[] = $meta['name'];
            }
        }

        return $fields;
    }

    /**
     * Mendapatkan metadata kolom.
     */
    public function getFieldData(): array
    {
        $fields = [];

        if (! $this->resultID instanceof PDOStatement) {
            return $fields;
        }

        for ($i = 0; $i < $this->getFieldCount(); $i++) {
            $meta = $this->resultID->getColumnMeta($i);

            if ($meta !== false) {
                $fields[] = (object) [
                    'name'       => $meta['name'] ?? '',
                    'type'       => $meta['native_type'] ?? 'UNKNOWN',
                    'max_length' => $meta['len'] ?? 0,
                ];
            }
        }

        return $fields;
    }

    /**
     * Membebaskan resource hasil query.
     */
    public function freeResult()
    {
        if ($this->resultID instanceof PDOStatement) {
            $this->resultID->closeCursor();
        }

        $this->resultID = false;
    }

    /**
     * PDO_OCI tidak menyediakan dataSeek yang dapat
     * digunakan secara umum untuk memindahkan cursor.
     */
    public function dataSeek(int $n = 0)
    {
        return false;
    }

    /**
     * Mengambil satu baris sebagai associative array.
     */
    protected function fetchAssoc()
    {
        if (! $this->resultID instanceof PDOStatement) {
            return false;
        }

        return $this->resultID->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil satu baris sebagai object atau Entity.
     */
    protected function fetchObject(string $className = stdClass::class)
    {
        if (! $this->resultID instanceof PDOStatement) {
            return false;
        }

        if ($className === stdClass::class) {
            return $this->resultID->fetch(PDO::FETCH_OBJ);
        }

        $row = $this->resultID->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return false;
        }

        if (is_subclass_of($className, Entity::class)) {
            return (new $className())->injectRawData($row);
        }

        $instance = new $className();

        foreach ($row as $key => $value) {
            $instance->{$key} = $value;
        }

        return $instance;
    }
}
