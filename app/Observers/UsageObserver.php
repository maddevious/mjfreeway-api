<?php

namespace App\Observers;

use App\Models\Usage;

class UsageObserver extends Observer
{
    public function __construct(Usage $usage)
    {
        $this->setModel($usage);
    }
}
