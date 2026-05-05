<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('devices');
        \Illuminate\Support\Facades\DB::table('commands')->truncate();
        \Illuminate\Support\Facades\DB::table('device_data')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->nullable()->unique();
            $table->string('serial_number')->nullable();
            $table->string('name');
            $table->string('category')->nullable();
            $table->foreignId('device_type_id')->nullable()->constrained('device_types')->onDelete('set null');
            $table->string('status');
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
