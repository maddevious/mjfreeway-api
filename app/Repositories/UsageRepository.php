<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\Usage;
use App\Models\Drink;


class UsageRepository extends Repository
{
    public $model;

    public function __construct()
    {
        $this->model = new Usage;
    }

    public function transform($result)
    {
        $output = new \StdClass;
        $output->user = !empty($result->user) ? collect($result->user)->except('id') : null;
        $output->drink = !empty($result->drink) ? collect($result->drink)->except('id') : null;
        $output->quantity = $result->quantity;
        $output->caffeine_available = 500 - ($result->drink->caffeine * $result->quantity);
        if ($output->caffeine_available <= 0) {
            $output->caffeine_message = 'Woah.. you\'ve had enough!';
        } elseif ($output->caffeine_available >= 1 && $output->caffeine_available <= 99) {
            $output->caffeine_message = 'You\'re getting there... slow down!';
        } elseif ($output->caffeine_available >= 100 && $output->caffeine_available <= 399) {
            $output->caffeine_message = 'Your day is just getting started!';
        } elseif ($output->caffeine_available >= 400) {
            $output->caffeine_message = 'Keep \'em coming...';
        }
        return $output;
    }
}
