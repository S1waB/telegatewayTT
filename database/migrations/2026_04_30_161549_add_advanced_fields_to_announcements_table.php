<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('status')->default('sent')->after('target_ids');
            $table->string('category')->nullable()->after('status');
            $table->timestamp('scheduled_at')->nullable()->after('category');
            $table->json('attachments')->nullable()->after('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['status', 'category', 'scheduled_at', 'attachments']);
        });
    }
};
