<?php

header('Content-Type: text/plain; charset=utf-8');

echo 'PHP SAPI: ' . PHP_SAPI . PHP_EOL;
echo 'PHP binary: ' . (PHP_BINARY ?: '(unknown)') . PHP_EOL;
echo 'Loaded php.ini: ' . (php_ini_loaded_file() ?: '(none)') . PHP_EOL;
echo 'Scanned ini: ' . (php_ini_scanned_files() ?: '(none)') . PHP_EOL;
echo 'Extension dir: ' . ini_get('extension_dir') . PHP_EOL;
echo 'PHPRC: ' . (getenv('PHPRC') ?: '(not set)') . PHP_EOL;
echo 'PHP_INI_SCAN_DIR: ' . (getenv('PHP_INI_SCAN_DIR') ?: '(not set)') . PHP_EOL;
echo 'PDO drivers: ' . implode(', ', PDO::getAvailableDrivers()) . PHP_EOL;
echo 'PDO_OCI loaded: ' . (extension_loaded('pdo_oci') ? 'YES' : 'NO') . PHP_EOL;
echo 'OCI8 loaded: ' . (extension_loaded('oci8') ? 'YES' : 'NO') . PHP_EOL;