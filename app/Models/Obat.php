<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nama',
        'jenis',
        'kategori',
        'deskripsi',
        'harga',
        'qty',
        'photo',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_obat');
    }
}
