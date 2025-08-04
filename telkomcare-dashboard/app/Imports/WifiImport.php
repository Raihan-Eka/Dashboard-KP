<?php

namespace App\Imports;

use App\Models\WifiTicket;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class WifiImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan nama header di file Excel Anda SAMA PERSIS dengan key di sini
        // (contoh: 'ticket_id', 'regional', 'witel', dll).
        // Logika ini akan mencari data berdasarkan 'ticket_id', lalu update jika ada,
        // atau buat baru jika tidak ada untuk mencegah duplikasi.
        return WifiTicket::updateOrCreate(
            ['TICKET_ID' => $row['ticket_id']], // Kolom unik untuk dicari
            [
                'REGIONAL'      => $row['regional'],
                'WITEL'         => $row['witel'],
                'STO'           => $row['sto'],
                'COMPLIANCE'    => $row['compliance'],
                // Tambahkan semua kolom lain dari Excel Anda di sini...
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000; // Proses 1000 baris sekali jalan untuk efisiensi memori
    }
}