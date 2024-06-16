<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Artisan;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // static::created(function ($tenant) {
        //     $tenant->createDatabase($tenant->id);
        // });
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

    /**
     * Create the tenant database.
     *
     * @return void
     */
    // public function createDatabase($tenantId)
    // {
    //     $dbName = $this->database;
    //     try {
    //         DB::statement("CREATE DATABASE $dbName");
    //         // Tenant::find($tenantId)->makeCurrent();
    //         Artisan::call('tenants:artisan', [
    //             'artisanCommand' => 'migrate:fresh --path=database/migrations/tenant --force',
    //             '--tenant' => $this->id
    //         ]);
    //         Log::info("Database created successfully", ["database" => $dbName]);
    //     } catch (\Exception $e) {
    //         Log::error("Failed to create database", ["database" => $dbName, "error" => $e->getMessage()]);
    //         throw $e;
    //     }
    // }
}
