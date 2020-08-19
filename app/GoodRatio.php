<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodRatio extends Model
{
    use SoftDeletes;

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function goodDetailPoint()
    {
        return $this->belongsTo(GoodDetailPoint::class);
    }

}
