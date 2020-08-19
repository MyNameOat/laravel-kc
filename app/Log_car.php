<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_car extends Model
{
    public function car()
    {
        return $this->belongsTo('App\Car', 'car_id');
    }
    
    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse','warehouses_id');
    }

    public function final_warehouse()
    {
        return $this->belongsTo('App\Warehouse','final_warehouses_id');
    }

    
}
