<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhReceiveGood extends Model
{
    use SoftDeletes;

    public function rrGood()
    {
        return $this->hasOne(RrGood::class);
    }
}
