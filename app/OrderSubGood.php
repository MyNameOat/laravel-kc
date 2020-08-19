<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSubGood extends Model
{
    use softDeletes;

    public function orderSubGoodDetails()
    {
        return $this->hasMany(OrderSubGoodDetail::class);
    }
}
