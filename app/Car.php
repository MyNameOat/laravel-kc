<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    public function logCars()
    {
        return $this->hasMany('App\Log_car');
    }
    
}
