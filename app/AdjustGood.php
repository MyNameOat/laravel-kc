<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdjustGood extends Model
{
    use SoftDeletes;

    public function adjust()
    {
        return $this->belongsTo(Adjust::class);
    }
}
