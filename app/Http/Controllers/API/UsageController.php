<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\UsageRepository;
use App\Repositories\OutputRepository;

class UsageController extends Controller
{
    protected $output;

    public function __construct(OutputRepository $output)
    {
        $this->output = $output;
        $output->setModel(new UsageRepository());
    }
}
