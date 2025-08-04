<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatinRawData extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     */
    protected $table = 'datin_raw_data';

    /**
     * Nonaktifkan timestamps default (created_at, updated_at) jika tidak ada di tabel Anda.
     */
    public $timestamps = false;

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'INCIDENT',
        'CUSTOMER_NAME',
        'CUSTOMER_ADDRESS',
        'WITEL',
        'STO',
        'SOURCE',
        'SEGMENT',
        'CHANNEL',
        'TROUBLE_CATEGORY',
        'TROUBLE_SUBCATEGORY',
        'TROUBLE_OPENTIME',
        'TROUBLE_DESCRIPTION',
        'TROUBLE_STATUS',
        'TECHNICIAN_NAME',
        'TECHNICIAN_PHONE',
        'RESOLUTION_CODE',
        'CLOSURE_MESSAGE',
        'CLOSED_GROUP',
        'LAST_UPDATE_TIME',
        'CLOSED_TIME',
        'TTR_CUSTOMER',
        'TTR_INFRA',
        'TTR_TOTAL',
        'REGIONAL',
        'HSA',
        'WORKZONE',
    ];
}