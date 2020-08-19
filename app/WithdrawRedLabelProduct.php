<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawRedLabelProduct extends Model
{
    use softDeletes;
    public function good()
    {
        return $this->belongsTo(Good::class);
    }
}
