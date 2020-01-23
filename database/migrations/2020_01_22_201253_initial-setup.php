<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InitialSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE TABLE `drinks` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `uuid` char(32) NOT NULL,
              `name` varchar(50) NOT NULL,
              `description` varchar(255) NOT NULL,
              `caffeine` int(10) unsigned NOT NULL DEFAULT 0,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `deleted_at` timestamp NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              INDEX `name` (`name`) comment '',
              UNIQUE `uuid` (`uuid`) comment ''
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        DB::unprepared("
        CREATE TRIGGER `tr_drinks_uuid` BEFORE INSERT ON `drinks` FOR EACH ROW begin if NEW.uuid ='' || NEW.uuid IS NULL then SET new.uuid = md5(uuid()); end if; end
        ");

        DB::statement("
        CREATE TABLE `usages` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `drink_id` int(10) unsigned NOT NULL,
              `user_id` int(10) unsigned NOT NULL,
              `quantity` tinyint(3) unsigned NOT NULL,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `drink_id` (`drink_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `drink_id` FOREIGN KEY (`drink_id`) REFERENCES `drinks` (`id`) ON UPDATE CASCADE,
              CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usages');
        Schema::dropIfExists('drinks');
    }
}
