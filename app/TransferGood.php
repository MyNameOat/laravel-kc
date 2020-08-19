<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferGood extends Model
{
    protected $guarded = ['id'];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }
}
