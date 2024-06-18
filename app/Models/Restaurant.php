<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    protected $connection = "landlord";
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'organization_number',
        'owner_id'
    ];

    use HasFactory;

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'owner_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, "restaurant_categories");
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'restaurant_id')->select(['name', 'id', 'price', 'restaurant_id']);
    }

    public function latestMenus(): HasMany
    {
        return $this->menus()->take(2);
    }


    public function getRouteKeyName()
    {
        return 'id';
    }
}
