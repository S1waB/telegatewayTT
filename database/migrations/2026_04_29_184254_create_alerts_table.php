<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $column) {
            $column->id();
            $column->foreignId('user_id')->constrained()->onDelete('cascade');
            $column->foreignId('device_id')->nullable()->constrained()->onDelete('cascade');
            $column->string('subject');
            $column->text('description');
            $column->enum('status', ['not_viewed', 'pending', 'viewed'])->default('not_viewed');
            $column->text('admin_response')->nullable();
            $column->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
