<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodDetailBenefit extends Model
{
    //
    use SoftDeletes;

    public function goodWarehouse()
    {
        return $this->hasOne(GoodWarehouse::class, 'good_detail_benefit_id');
    }
}
