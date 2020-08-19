<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseGoodView extends Model
{
    public function WarehouseGood()
    {
        return $this->belongsTo(WarehouseGood::class);
    }
}
