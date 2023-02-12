<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTempKasir extends Model
{
    use HasFactory;
    protected $table = 'temp_kasirs';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
