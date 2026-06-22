<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Patch existing devices that have no device_id string, using their serial_number
        DB::table('devices')->whereNull('device_id')->orderBy('id')->each(function ($device) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $device->serial_number));
            DB::table('devices')->where('id', $device->id)->update(['device_id' => $slug]);
        });

        // 2. Make alerts.device_id nullable (raw SQL, no Doctrine needed)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('ALTER TABLE `alerts` MODIFY COLUMN `device_id` VARCHAR(255) NULL;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `alerts` MODIFY COLUMN `device_id` VARCHAR(255) NOT NULL;');
    }
};
