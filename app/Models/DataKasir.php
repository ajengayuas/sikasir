<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKasir extends Model
{
    use HasFactory;
    protected $table = 'data_kasirs';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
