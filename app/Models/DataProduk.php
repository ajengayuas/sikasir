<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class DataProduk extends Model
{
    use HasFactory;
    use AutoNumberTrait;
    protected $table = 'data_produks';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function getAutoNumberOptions()
    {
        return [
            'kode' => [
                'format' => 'KD?',
                'length' => 6
            ]
        ];
    }
}
