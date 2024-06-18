<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    protected $connection = "landlord";

    protected $guarded = ["id"];

    protected $fillable = ["name", "price"];

    use HasFactory;

    public function carts(): BelongsToMany
    {
        return  $this->belongsToMany(Cart::class);
    }
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
