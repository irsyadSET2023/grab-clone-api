<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    protected $connection = "grabclone";

    protected $guarded = "id";

    use HasFactory;
}
