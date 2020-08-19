<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformationReceiptService extends Model
{
    public function informationReceipt(){
        return $this->belongsTo(InformationReceipt::class);
    }
}
