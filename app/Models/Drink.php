<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Validator;
use App\Traits\FindTrait;

class Drink extends Model
{
    use SoftDeletes, FindTrait;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'uuid',
        'name',
        'description',
        'caffeine'
    ];

    public function validateCreate($data)
    {
        $v = Validator::make($data, [
            'name' => 'required|max:50',
            'description' => 'required|max:255',
            'caffeine' => 'required|integer|between:1,1000'
        ]);
        return $v;
    }

    public function validateUpdate($data)
    {
        $v = Validator::make($data, [
            'name' => 'max:50',
            'description' => 'max:255',
            'caffeine' => 'integer|between:1,1000'
        ]);
        return $v;
    }
}
