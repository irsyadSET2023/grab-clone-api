<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $connection = 'landlord';
    protected $guarded = ['id'];
    protected $fillable = ['name'];

    public function restaurant(): BelongsToMany
    {
        return $this->belongsToMany('Restaurant', 'restaurant_categories');
    }
}
