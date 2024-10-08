<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeightTrafficTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('heighttraffic', function (Blueprint $table) {
            $table->id();
            $table->string('cam_ip', 255);
            $table->string('cam_name', 255);
            $table->bigInteger('detect_time');
            $table->float('height');
            $table->text('plate');
            $table->integer('plate_size');
            $table->float('plate_sharp');
            $table->text('photo');
            $table->integer('photo_size');
            $table->float('photo_sharp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('height_traffic');
    }
}
;
