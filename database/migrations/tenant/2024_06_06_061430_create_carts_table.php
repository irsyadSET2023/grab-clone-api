<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $db = DB::connection('grabclone')->getDatabaseName();
            $table->id();
            $table->foreignId('user_id')->references('id')->on($db . '.users')->constrained()->onDelete('cascade');
            $table->boolean("is_checked_out")->default(0);
            $table->float("total")->default(0);
            $table->foreignId("coupon_id")->constrained()->nullable();
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
        Schema::dropIfExists('carts');
    }
}
