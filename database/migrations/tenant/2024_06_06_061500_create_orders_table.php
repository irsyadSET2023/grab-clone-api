<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $db = DB::connection('grabclone')->getDatabaseName();
            $table->id();
            $table->foreignId('user_id')->references('id')->on($db . '.users')->constrained()->onDelete('cascade');
            $table->foreignId("cart_id")->constrained()->onDelete('cascade');
            $table->float("total_price");
            $table->float("discount_price")->default(0);
            $table->enum("type", ["DELIVERY", "PICKUP"]);
            $table->enum("status", ["PENDING", "ACCEPTED", "PAID"])->default("PENDING");
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
        Schema::dropIfExists('orders');
    }
}
