<?php

namespace App\Repositories;

use App\Models\Drink;


class DrinkRepository extends Repository
{
    public function __construct()
    {
        $this->model = new Drink;
    }
}
