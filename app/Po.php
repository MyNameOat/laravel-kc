<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Po extends Model
{
    use SoftDeletes;

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
