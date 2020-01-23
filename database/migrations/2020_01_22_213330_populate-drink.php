<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Drink;

class PopulateDrink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $drinks[] = [
            'name' => 'Monster Ultra Sunrise',
            'description' => 'A refreshing orange beverage that has 75mg of caffeine per serving. Every can has two servings.',
            'caffeine' => 75
        ];
        $drinks[] = [
            'name' => 'Black Coffee',
            'description' => 'The classic, the average 8oz. serving of black coffee has 95mg of caffeine.',
            'caffeine' => 95
        ];
        $drinks[] = [
            'name' => 'Americano',
            'description' => 'Sometimes you need to water it down a bit... and in comes the americano with an average of 77mg. of caffeine per serving.',
            'caffeine' => 77
        ];
        $drinks[] = [
            'name' => 'Sugar free NOS',
            'description' => 'Another orange delight without the sugar. It has 130 mg. per serving and each can has two servings.',
            'caffeine' => 260
        ];
        $drinks[] = [
            'name' => '5 Hour Energy',
            'description' => 'And amazing shot of get up and go! Each 2 fl. oz. container has 200mg of caffeine to get you going.',
            'caffeine' => 200
        ];
        foreach ($drinks as $d) {
            $drink = new Drink;
            $drink->fill([
                'name' => $d['name'],
                'description' => $d['description'],
                'caffeine' => $d['caffeine']
            ])->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Drink::getQuery()->delete();
    }
}
