<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSubGoodDetail extends Model
{
    use softDeletes;

    public function warehouseGoodBalances()
    {
        return $this->hasMany(WarehouseGoodBalance::class);
    }
}
