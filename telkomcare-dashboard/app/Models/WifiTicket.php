<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WifiTicket extends Model
{
    use HasFactory;

    /**
     * Secara eksplisit memberitahu Laravel untuk menggunakan tabel 'wifi_tickets_raw'.
     * Ini adalah baris paling penting untuk memperbaiki error.
     */
    protected $table = 'wifi_tickets_raw';

    /**
     * Nonaktifkan timestamps default (created_at, updated_at).
     */
    public $timestamps = false;

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal.
     */
    protected $fillable = [
        'TICKET_ID', 'SERVICE_ID', 'CUSTOMER_NAME', 'CUSTOMER_ADDRESS',
        'Reported_By', 'Reported_Date', 'TECHNICIAN_NAME', 'TECHNICIAN_PHONE',
        'TROUBLE_DESCRIPTION', 'CLOSURE_MESSAGE', 'CLOSED_TIME',
        'TTR_CUSTOMER', 'TTR_INFRA', 'TTR_TOTAL', 'REGIONAL', 'WITEL',
        'HSA', 'STO', 'COMPLIANCE', 'TARGET', 'COMPLIANCE_PERCENTAGE',
        'ACHV_PERCENTAGE', 'LEVEL',
    ];
}