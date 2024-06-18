<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $connection = "tenant";

    protected $guarded = ["id"];

    protected $fillable = ["type", "total_price", "discount_price", "status", "user_id", "cart_id"];

    use HasFactory;

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
