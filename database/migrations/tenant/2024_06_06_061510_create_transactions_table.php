<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $db = DB::connection('grabclone')->getDatabaseName();
            $table->id();
            $table->uuid("reference_number")->unique();
            $table->foreignId('user_id')->references('id')->on($db . '.users')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->onDelete('cascade');
            $table->json("purchased_data");
            $table->enum("status", ["COMPLETED", "EXPIRED", "FAILED"]);
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
        Schema::dropIfExists('transactions');
    }
}
