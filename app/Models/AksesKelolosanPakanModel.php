<?php

namespace App\Models;

use CodeIgniter\Model;

class AksesKelolosanPakanModel extends Model
{
    protected $table            = 'AKSES_KELOLOSANPAKAN';
    protected $primaryKey       = 'USERNAME';
    protected $returnType       = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields    = [
        'USERNAME',
        'STATUS_AKSES',
    ];

    protected $useTimestamps = false;
}
