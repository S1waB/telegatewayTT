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
        Schema::dropIfExists('alerts');
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('subject');
            $table->text('description');
            $table->enum('severity', ['info', 'warning', 'critical']);
            $table->enum('status', ['not_viewed', 'pending', 'viewed'])->default('not_viewed');
            $table->timestamp('triggered_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('device_id')->references('device_id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
