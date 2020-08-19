<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodAudit extends Model
{
    public function prGoods()
    {
        return $this->hasMany(PrGood::class, 'good_id', 'good_id');
    }
}
