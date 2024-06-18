<?php

namespace App\Jobs;

use App\Models\Employment;
use App\Models\Staff;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class CreateTenantJob implements ShouldQueue, NotTenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dbName;
    protected $tenantId;
    protected $userData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dbName, $tenantId, $userData)
    {
        //
        $this->dbName = $dbName;
        $this->tenantId = $tenantId;
        $this->userData = $userData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $dbName = $this->dbName;
        try {
            Config::set('database.default', 'landlord');
            Employment::create([
                'user_id' => $this->userData["id"],
                'tenant_id' => $this->tenantId,
                'employment_status' => 1,
            ]);
            DB::statement("CREATE DATABASE $dbName");
            // Switch to tenant connection
            Config::set('database.default', 'tenant');
            Artisan::call("tenants:artisan 'migrate:fresh --path=database/migrations/tenant' --tenant=" . $this->tenantId);
            Log::info("Database created and migrations ran successfully", ["database" => $this->dbName]);
            Tenant::find($this->tenantId)->makeCurrent();
            Staff::create([
                "name" => $this->userData["name"],
                "work_email" => $this->userData["email"],
                "user_id" =>  $this->userData["id"]
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create database or run migrations", ["database" => $this->dbName, "error" => $e->getMessage()]);
            throw $e;
        }
    }
}
