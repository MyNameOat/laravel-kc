<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodWarehouse extends Model
{

    use SoftDeletes;

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }

    public function goodPrice(){
        return $this->hasMany(GoodPrice::class);
    }
}
