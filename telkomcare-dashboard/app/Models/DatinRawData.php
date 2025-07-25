<?php

namespace App\Models;

use illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatinRawData extends Model
{
    use HasFactory;
    
    /**
     * Nama tabel yang terhubung dengan model ini.
     * 
     * @var string
     */
    protected $tabel = 'datin_raw_data';
    /** 
     * @var bool
    */
    public $timestamps = false;
    //
}
