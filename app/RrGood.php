<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RrGood extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function rr()
    {
        return $this->belongsTo(Rr::class);
    }
}
