<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseGood extends Model
{
    use SoftDeletes;

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouseGoodBalances()
    {
        return $this->hasMany(WarehouseGoodBalance::class);
    }

    public function rrGood()
    {
        return $this->belongsTo(RrGood::class);
    }
}
