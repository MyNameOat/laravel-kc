<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiveTransferGood extends Model
{
    public function receiveTransfer()
    {
        return $this->belongsTo(ReceiveTransfer::class);
    }
}
