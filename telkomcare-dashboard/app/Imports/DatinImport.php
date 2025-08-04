<?php

namespace App\Imports;

use App\Models\DatinRawData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DatinImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan nama header di file Excel Anda SAMA PERSIS dengan key di sini (huruf kecil).
        // Contoh: header di Excel 'customer_name' akan dipetakan ke kolom 'CUSTOMER_NAME' di database.
        return DatinRawData::updateOrCreate(
            ['INCIDENT' => $row['incident']], // Kolom unik untuk dicari
            [
                'CUSTOMER_NAME'      => $row['customer_name'],
                'CUSTOMER_ADDRESS'   => $row['customer_address'],
                'WITEL'              => $row['witel'],
                'STO'                => $row['sto'],
                'SOURCE'             => $row['source'],
                'SEGMENT'            => $row['segment'],
                'CHANNEL'            => $row['channel'],
                'TROUBLE_CATEGORY'   => $row['trouble_category'],
                'TROUBLE_SUBCATEGORY'=> $row['trouble_subcategory'],
                'TROUBLE_OPENTIME'   => isset($row['trouble_opentime']) ? Date::excelToDateTimeObject($row['trouble_opentime']) : null,
                'TROUBLE_DESCRIPTION'=> $row['trouble_description'],
                'TROUBLE_STATUS'     => $row['trouble_status'],
                'TECHNICIAN_NAME'    => $row['technician_name'],
                'TECHNICIAN_PHONE'   => $row['technician_phone'],
                'RESOLUTION_CODE'    => $row['resolution_code'],
                'CLOSURE_MESSAGE'    => $row['closure_message'],
                'CLOSED_GROUP'       => $row['closed_group'],
                'LAST_UPDATE_TIME'   => isset($row['last_update_time']) ? Date::excelToDateTimeObject($row['last_update_time']) : null,
                'CLOSED_TIME'        => isset($row['closed_time']) ? Date::excelToDateTimeObject($row['closed_time']) : null,
                'TTR_CUSTOMER'       => $row['ttr_customer'],
                'TTR_INFRA'          => $row['ttr_infra'],
                'TTR_TOTAL'          => $row['ttr_total'],
                'REGIONAL'           => $row['regional'],
                'HSA'                => $row['hsa'],
                'WORKZONE'           => $row['workzone'],
            ]
        );
    }

    public function chunkSize(): int
    {
        return 500; // Proses 500 baris sekali jalan untuk efisiensi memori
    }
}