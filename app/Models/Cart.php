<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    protected $connection = "tenant";

    protected $guarded = ["id"];

    protected $fillable = ["user_id", "is_checked_out", "coupon_id", "total"];

    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
