<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoGood extends Model
{
    use SoftDeletes;

    public function po()
    {
        return $this->belongsTo(Po::class);
    }

    public function whReceiveGoods()
    {
        return $this->hasMany(WhReceiveGood::class);
    }
}
