<?php

namespace App\Imports;

use App\Models\HsiTicket;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HsiImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan header di Excel Anda cocok (misal: 'incident', 'regional', dll).
        return HsiTicket::updateOrCreate(
            ['INCIDENT' => $row['incident']], // Kolom unik untuk dicari
            [
                'REGIONAL'          => $row['regional'],
                'WITEL'             => $row['witel'],
                'HSA'               => $row['hsa'],
                'WORKZONE'          => $row['workzone'],
                'COMPLY_4_E2E'      => $row['comply_4_e2e'],
                'COMPLY_24_E2E'     => $row['comply_24_e2e'],
                'REPORTED_DATE'     => Date::excelToDateTimeObject($row['reported_date']),
                // Tambahkan kolom lain dari Excel HSI Anda di sini...
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}