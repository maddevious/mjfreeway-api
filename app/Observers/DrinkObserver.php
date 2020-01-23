<?php

namespace App\Observers;

use App\Models\Drink;

class DrinkObserver extends Observer
{
    public function __construct(Drink $drink)
    {
        $this->setModel($drink);
    }
}
