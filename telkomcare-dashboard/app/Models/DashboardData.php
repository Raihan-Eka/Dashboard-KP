<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardData extends Model
{
    use HasFactory;
    protected $table = 'dashboard_data'; // Pastikan nama tabel benar

    protected $fillable = [
        'city_id',
        'category',
        'entry_date',
        'sid',
        'comply',
        'not_comply',
        'total',
        'target',
        'ttr_comply',
        'achievement',
        'ticket_count',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'target' => 'float',
        'achievement' => 'float',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}