<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodView extends Model
{
    public function good()
    {
        return $this->belongsTo(Good::class);
    }
}
