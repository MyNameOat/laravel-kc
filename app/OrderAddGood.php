<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderAddGood extends Model
{
    use softDeletes;

    public function orderAddGoodDetails()
    {
        return $this->hasMany(OrderAddGoodDetail::class);
    }

    public function orderSubGoods()
    {
        return $this->hasMany(OrderSubGood::class);
    }
}
