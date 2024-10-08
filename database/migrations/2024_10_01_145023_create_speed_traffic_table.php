<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpeedTrafficTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('speedtraffic', function (Blueprint $table) {
            $table->id();
            $table->string('cam_ip');
            $table->string('cam_name');
            $table->bigInteger('start_time');
            $table->bigInteger('end_time');
            $table->float('speed');
            $table->text('photo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speed_traffic');
    }
}
;
