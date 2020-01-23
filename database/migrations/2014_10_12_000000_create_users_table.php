<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->Increments('id');
            $table->char('uuid', 32);
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
        DB::statement("
            ALTER TABLE users ADD UNIQUE uuid (uuid)
        ");
        DB::unprepared("
        CREATE TRIGGER `tr_users_uuid` BEFORE INSERT ON `users` FOR EACH ROW begin if NEW.uuid ='' || NEW.uuid IS NULL then SET new.uuid = md5(uuid()); end if; end
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
