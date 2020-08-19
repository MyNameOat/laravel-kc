<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiveTransfer extends Model
{
    protected $dates = ["document_at"];
    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }
}
