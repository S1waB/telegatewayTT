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
        if (! Schema::hasColumn('alerts', 'admin_response')) {
            Schema::table('alerts', function (Blueprint $table) {
                $table->text('admin_response')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('alerts', 'admin_response')) {
            Schema::table('alerts', function (Blueprint $table) {
                $table->dropColumn('admin_response');
            });
        }
    }
};
