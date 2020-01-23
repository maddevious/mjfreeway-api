<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Usage extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'user_id',
        'user',
        'drink_id',
        'drink',
        'quantity',
    ];

    public function validateCreate($data)
    {
        $v = Validator::make($data, [
            'user' => 'required|exists:users,uuid',
            'drink' => 'required|exists:drinks,uuid',
            'quantity' => 'required|integer|between:1,50',
        ]);
        return $v;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function drink()
    {
        return $this->belongsTo('App\Models\Drink')->withTrashed();
    }

    public function setUserAttribute($value)
    {
        $this->attributes['user_id'] = !empty($value) ? User::findByUuid($value)->id : null;
    }

    public function setDrinkAttribute($value)
    {
        $this->attributes['drink_id'] = !empty($value) ? Drink::findByUuid($value)->id : null;
    }
}
