<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class PopulateUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = new User;
        $user->fill([
            'name' => 'Jimmy Morgan',
            'email' => 'jimmymorgan@yahoo.com',
            'password' => Hash::make('Testing123')
        ])->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        User::where('email', 'jimmymorgan@yahoo.com')->forceDelete();
    }
}
