<?php

namespace App\Jobs;

use App\Models\Employment;
use App\Models\Restaurant;
use App\Models\Tenant;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Throwable;

class CreateTenantJob implements ShouldQueue, NotTenantAware
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userData;
    protected $restaurantData;

    /**
     * The number of seconds the job can run before timing out.
     * Assume 10 minutes for migration to complete.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $userData, array $restaurantData)
    {
        $this->userData = $userData;
        $this->restaurantData = $restaurantData;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * Delete any overlapping jobs so that they will not be retried to
     * prevent multiple tenant spam by same user.
     *
     * @return array
     */
    // public function middleware()
    // {
    //     return [(new WithoutOverlapping($this->userData["email"]))];
    // }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();

        try {
            $userId = DB::connection('grabclone')
                ->table('users')->insertGetId([
                    "name" => $this->userData["name"],
                    "email" => $this->userData["email"],
                    "password" => Hash::make($this->userData["password"])
                ]);

            Log::info("Data", ["user" => $userId]);

            $restaurant = Restaurant::create([
                "name" => $this->restaurantData["restaurant_name"],
                "owner_id" => $userId,
                "organization_number" => $this->restaurantData["organization_number"]
            ]);

            $tenant = Tenant::create([
                'name' => $this->restaurantData["restaurant_name"],
                'database' => $this->restaurantData["restaurant_name"] . \Str::random(10),
            ]);

            $newId = $tenant->id;

            $dbName = config('database.naming.tenant.prefix') . "_" . $newId;

            $tenant->update(['database' => $dbName]);

            /** Register Employment at HR */
            Employment::insert([
                'user_id' => $userId,
                'tenant_id' => $newId,
                'employment_status' => 1,
            ]);
            /**
             * Create Tenant DB and migrate Tables
             */
            $dbExist = DB::connection('landlord')
                ->select(
                    "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :name",
                    ['name' => $dbName]
                );

            if (empty($dbExist)) {
                /** Create DB */
                DB::connection('landlord')->statement("CREATE DATABASE " . $dbName);

                $exitCode = Artisan::call("tenants:artisan 'migrate:fresh --path=database/migrations/tenant' --tenant=" . $newId);

                if ($exitCode < 0) {
                    throw new Exception('Failed to migrate tables to unregistered DB.', $dbName);
                } else {
                    Tenant::find($newId)->makeCurrent();
                }
            } else {
                throw new Exception('Database Already Exists. Failed to create DB.');
            }
            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th; // Rethrow the exception so it can be caught by the failed method
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $th
     * @return void
     */
    public function failed(Throwable $th)
    {
        Log::error("Error in creating tenant", ["Message" => $th->getMessage()]);
    }
}
