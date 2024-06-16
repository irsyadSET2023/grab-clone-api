<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $db = DB::connection('grabclone')->getDatabaseName();
            $table->id();
            $table->foreignId('user_id')->references('id')->on($db . '.users')->constrained()->onDelete('cascade');
            $table->string("name");
            $table->string("start_date");
            $table->string("commencement_date");
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
        Schema::dropIfExists('staff');
    }
}
