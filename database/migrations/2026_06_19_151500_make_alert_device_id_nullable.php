<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing foreign key, modify column to nullable, then re-add FK
        DB::statement('ALTER TABLE `alerts` DROP FOREIGN KEY `alerts_device_id_foreign`');
        DB::statement('ALTER TABLE `alerts` MODIFY `device_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `alerts` ADD CONSTRAINT `alerts_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices`(`id`) ON DELETE CASCADE');
    }

    public function down(): void
    {
        // Reverse: drop FK, make not null, then re-add FK
        DB::statement('ALTER TABLE `alerts` DROP FOREIGN KEY `alerts_device_id_foreign`');
        DB::statement('ALTER TABLE `alerts` MODIFY `device_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `alerts` ADD CONSTRAINT `alerts_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices`(`id`) ON DELETE CASCADE');
    }
};
