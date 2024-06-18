<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $dbGrabclone = DB::connection('grabclone')->getDatabaseName();
            $table->id();
            $table->foreignId('owner_id')->references('id')->on($dbGrabclone . '.users')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('organization_number');
            $table->boolean('application_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
}
