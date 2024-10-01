<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cameratraffic', function (Blueprint $table) {
            $table->id();
            $table->string('cam_ip')->nullable();
            $table->string('cam_name')->nullable();
            $table->string('scenario_name')->nullable();
            $table->integer('bus_count')->default(0);
            $table->integer('car_count')->default(0);
            $table->integer('human_count')->default(0);
            $table->integer('bike_count')->default(0);
            $table->integer('truck_count')->default(0);
            $table->integer('total_count')->default(0);
            $table->dateTime('time')->nullable();
            $table->string('last_status', 30);
            $table->string('last_update')->nullable();
            $table->string('last_offline_ping')->nullable();
            $table->string('offline_since')->nullable();
            $table->timestamp('last_offline_timestamp')->nullable();
            $table->timestamp('last_online_timestamp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera_traffic');
    }
};
