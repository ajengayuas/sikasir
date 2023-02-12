<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransLunas extends Model
{
    use HasFactory;
    protected $table = 'trans_lunas';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
