<?php

namespace App\Models;

use illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WifiTicket extends Model
{
    use HasFactory;

    /**
     * 
     * Nama Tabel Yang Terhubung dengan model ini.
     * @var string
     */
    protected $table = 'wifi_tickets_raw';

    /**
     * Menonaktifkan timestamps
     * 
     * @var bool
     */
    public $timestamps = false;
    //
}
