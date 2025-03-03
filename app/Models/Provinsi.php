<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    // Secara default, nama tabel Eloquent untuk model "Provinsi" adalah "provinsis"
    // Jika kamu ingin mendefinisikan manual, uncomment baris di bawah:
    // protected $table = 'provinsis';

    // Kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'nama',
        'geojson',
    ];

    // Jika ingin casting kolom geojson ke array otomatis
    // protected $casts = [
    //     'geojson' => 'array',
    // ];
}
