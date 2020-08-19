<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodDetailPoint extends Model
{
    use SoftDeletes;
    //

    public function goodRatio()
    {
        return $this->hasOne(GoodRatio::class, 'good_detail_point_id');
    }
}
