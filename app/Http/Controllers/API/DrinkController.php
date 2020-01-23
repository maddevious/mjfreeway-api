<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\DrinkRepository;
use App\Repositories\OutputRepository;
use Illuminate\Http\Request;

class DrinkController extends Controller
{
    protected $output;
    protected $repository;

    public function __construct(OutputRepository $output)
    {
        $this->output = $output;
        $output->setModel(new DrinkRepository());
    }
}
