<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    protected $connection = "landlord";
    protected $guarded = ['id'];

    use HasFactory;
}
