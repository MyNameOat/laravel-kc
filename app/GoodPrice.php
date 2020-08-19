<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodPrice extends Model
{
    use SoftDeletes;

    public function member_type()
    {
        return $this->belongsTo(Member_type::class);
    }
}
