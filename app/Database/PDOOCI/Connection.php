<?php

namespace App\Database\PDOOCI;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use PDO;
use PDOException;
use PDOStatement;

class Connection extends BaseConnection
{
    /**
     * Identitas driver.
     */
    protected $DBDriver = 'PDOOCI';

    /**
     * Nama platform database.
     */
    protected $platform = 'Oracle';

    /**
     * Koneksi PDO.
     */
    public $connID = false;

    /**
     * Membuka koneksi Oracle menggunakan PDO_OCI.
     */
    public function connect(bool $persistent = false)
    {
        if ($this->connID instanceof PDO) {
            return $this->connID;
        }

        $dsn = $this->DSN;

        if ($dsn === '') {
            $dsn = sprintf(
                'oci:dbname=//%s:%s/%s;charset=%s',
                $this->hostname,
                $this->port ?: 1521,
                $this->database,
                $this->charset ?: 'AL32UTF8'
            );
        }

        try {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_CASE               => PDO::CASE_NATURAL,
            ];

            if ($persistent) {
                $options[PDO::ATTR_PERSISTENT] = true;
            }

            return new PDO(
                $dsn,
                $this->username,
                $this->password,
                $options
            );
        } catch (PDOException $e) {
            if ($this->DBDebug) {
                throw new DatabaseException(
                    'Koneksi PDO_OCI ke Oracle gagal: ' . $e->getMessage(),
                    (int) $e->getCode(),
                    $e
                );
            }

            return false;
        }
    }

    /**
     * Mengatur nama database/service Oracle.
     */
    public function setDatabase(string $databaseName): void
    {
        $this->close();
    }
    /**
     * Mendapatkan versi server Oracle.
     */
    public function getVersion(): string
    {
        if (! $this->connID instanceof PDO) {
            $this->initialize();
        }

        $stmt = $this->connID->query(
            "SELECT banner FROM v\$version WHERE banner LIKE 'Oracle%'"
        );

        $version = $stmt->fetchColumn();

        return $version !== false ? (string) $version : 'Oracle';
    }

    /**
     * Menutup koneksi.
     */
    protected function _close()
    {
        $this->connID = false;
    }

    /**
     * Memeriksa koneksi.
     */
    protected function _ping(): bool
    {
        try {
            if (! $this->connID instanceof PDO) {
                return false;
            }

            $stmt = $this->connID->query('SELECT 1 FROM DUAL');

            return $stmt instanceof PDOStatement;
        } catch (PDOException) {
            return false;
        }
    }

    /**
     * Menjalankan query SQL.
     */
    protected function execute(string $sql)
    {
        if (! $this->connID instanceof PDO) {
            $this->initialize();
        }

        try {
            return $this->connID->query($sql);
        } catch (PDOException $e) {
            $this->resultID = false;

            if ($this->DBDebug) {
                throw new DatabaseException(
                    'Query Oracle gagal: ' . $e->getMessage(),
                    (int) $e->getCode(),
                    $e
                );
            }

            return false;
        }
    }

    /**
     * Memulai transaksi.
     */
    protected function _transBegin(): bool
    {
        return $this->connID instanceof PDO
            && $this->connID->beginTransaction();
    }

    /**
     * Commit transaksi.
     */
    protected function _transCommit(): bool
    {
        return $this->connID instanceof PDO
            && $this->connID->commit();
    }

    /**
     * Rollback transaksi.
     */
    protected function _transRollback(): bool
    {
        return $this->connID instanceof PDO
            && $this->connID->rollBack();
    }

    /**
     * Mengambil jumlah baris yang terpengaruh.
     */
    public function affectedRows(): int
    {
        if (! $this->resultID instanceof PDOStatement) {
            return 0;
        }

        return $this->resultID->rowCount();
    }

    /**
     * Mengambil informasi error terakhir.
     */
    public function error(): array
    {
        if (! $this->connID instanceof PDO) {
            return [
                'code'    => 0,
                'message' => '',
            ];
        }

        $error = $this->connID->errorInfo();

        return [
            'code'    => $error[1] ?? 0,
            'message' => $error[2] ?? '',
        ];
    }

    /**
     * PDO_OCI tidak menyediakan insert ID otomatis.
     */
    public function insertID()
    {
        return false;
    }

    /**
     * Menghasilkan SQL daftar tabel.
     *
     * Signature harus sesuai dengan BaseConnection CI4.7.5.
     */
    protected function _listTables(
        bool $constrainByPrefix = false,
        ?string $tableName = null
    ) {
        $sql = 'SELECT table_name FROM user_tables';
        $conditions = [];

        if ($tableName !== null) {
            $conditions[] = 'table_name = '
                . $this->escape(strtoupper($tableName));
        } elseif ($constrainByPrefix && $this->DBPrefix !== '') {
            $conditions[] = 'table_name LIKE '
                . $this->escape(strtoupper($this->DBPrefix) . '%');
        }

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        return $sql . ' ORDER BY table_name';
    }

    /**
     * Menghasilkan SQL daftar kolom.
     */
    protected function _listColumns($table = '')
    {
        $table = strtoupper(trim((string) $table));

        if ($table === '') {
            return false;
        }

        return 'SELECT column_name
                FROM user_tab_columns
                WHERE table_name = '
            . $this->escape($table)
            . ' ORDER BY column_id';
    }

    /**
     * Metadata kolom tabel.
     */
    protected function _fieldData(string $table): array
    {
        $sql = 'SELECT
                    column_name AS name,
                    data_type AS type,
                    data_length AS max_length,
                    data_precision AS precision,
                    data_scale AS scale,
                    nullable
                FROM user_tab_columns
                WHERE table_name = ?
                ORDER BY column_id';

        $stmt = $this->connID->prepare($sql);
        $stmt->execute([strtoupper(trim($table))]);

        $fields = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $fields[] = (object) [
                'name'       => $row['NAME'] ?? $row['name'] ?? '',
                'type'       => $row['TYPE'] ?? $row['type'] ?? '',
                'max_length' => $row['MAX_LENGTH'] ?? $row['max_length'] ?? 0,
                'precision'  => $row['PRECISION'] ?? $row['precision'] ?? null,
                'scale'      => $row['SCALE'] ?? $row['scale'] ?? null,
                'nullable'   => ($row['NULLABLE'] ?? $row['nullable'] ?? 'Y') === 'Y',
            ];
        }

        return $fields;
    }

    /**
     * Metadata index tabel.
     */
    protected function _indexData(string $table): array
    {
        $sql = 'SELECT
                    i.index_name,
                    i.column_name,
                    x.uniqueness
                FROM user_ind_columns i
                JOIN user_indexes x
                  ON x.index_name = i.index_name
                WHERE i.table_name = ?
                ORDER BY i.index_name, i.column_position';

        $stmt = $this->connID->prepare($sql);
        $stmt->execute([strtoupper(trim($table))]);

        $indexes = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $indexes[] = (object) $row;
        }

        return $indexes;
    }

    /**
     * Metadata foreign key tabel.
     */
    protected function _foreignKeyData(string $table): array
    {
        $sql = "SELECT
                    a.constraint_name,
                    a.table_name,
                    a.column_name,
                    c_pk.table_name AS foreign_table_name,
                    b.column_name AS foreign_column_name,
                    c.delete_rule AS on_delete
                FROM user_cons_columns a
                JOIN user_constraints c
                  ON a.constraint_name = c.constraint_name
                JOIN user_constraints c_pk
                  ON c.r_constraint_name = c_pk.constraint_name
                JOIN user_cons_columns b
                  ON c_pk.constraint_name = b.constraint_name
                 AND a.position = b.position
                WHERE c.constraint_type = 'R'
                  AND a.table_name = ?
                ORDER BY a.constraint_name, a.position";

        $stmt = $this->connID->prepare($sql);
        $stmt->execute([strtoupper(trim($table))]);

        $foreignKeys = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $foreignKeys[] = (object) [
                'constraint_name'     => $row['CONSTRAINT_NAME'] ?? $row['constraint_name'] ?? null,
                'table_name'          => $row['TABLE_NAME'] ?? $row['table_name'] ?? '',
                'column_name'         => [$row['COLUMN_NAME'] ?? $row['column_name'] ?? ''],
                'foreign_table_name'  => $row['FOREIGN_TABLE_NAME'] ?? $row['foreign_table_name'] ?? '',
                'foreign_column_name' => [$row['FOREIGN_COLUMN_NAME'] ?? $row['foreign_column_name'] ?? ''],
                'on_delete'           => $row['ON_DELETE'] ?? $row['on_delete'] ?? 'NO ACTION',
                'on_update'           => null,
                'match'               => null,
            ];
        }

        return $foreignKeys;
    }

    /**
     * Nama platform database.
     */
    public function getPlatform(): string
    {
        return 'Oracle';
    }

    /**
     * Escape nilai SQL.
     */
    public function escape($str)
    {
        if (is_array($str)) {
            return array_map(fn ($value) => $this->escape($value), $str);
        }

        if ($str === null) {
            return 'NULL';
        }

        if (is_bool($str)) {
            return $str ? '1' : '0';
        }

        if (is_int($str) || is_float($str)) {
            return (string) $str;
        }

        if ($str instanceof \DateTimeInterface) {
            $str = $str->format('Y-m-d H:i:s');
        }

        if (! $this->connID instanceof PDO) {
            $this->initialize();
        }

        return $this->connID->quote((string) $str);
    }
}
