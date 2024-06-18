<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;

class Tenant extends SpatieTenant
{
    use HasFactory;

    protected $connection = "landlord";
    protected $guarded = ["id"];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tenant_identifier',
        'database'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($tenant) {
            $tenant->tenant_identifier = self::generateUniqueIdentifier();
        });
    }

    public function getRouteKeyName()
    {
        return 'tenant_identifier';
    }

    /**
     * Generate a unique identifier for the tenant.
     *
     * @return string
     */
    private static function generateUniqueIdentifier(): string
    {
        do {
            $identifier = uniqid();
        } while (self::where('tenant_identifier', $identifier)->exists());

        return $identifier;
    }
}
