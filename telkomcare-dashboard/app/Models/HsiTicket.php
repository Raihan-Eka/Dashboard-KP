<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HsiTicket extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     */
    protected $table = 'hsi_data_raw'; // <-- PASTIKAN NAMA TABEL INI BENAR

    /**
     * Menonaktifkan timestamp otomatis (created_at dan updated_at).
     */
    public $timestamps = false;
}