<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformationReceiptSku extends Model
{
    public function informationReceipt(){
        return $this->belongsTo(InformationReceipt::class);
    }

}
