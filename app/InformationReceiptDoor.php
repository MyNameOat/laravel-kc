<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InformationReceiptDoor extends Model
{
    public function good(){
        return $this->belongsTo(Good::class);
    }

    public function informationReceipt(){
        return $this->belongsTo(InformationReceipt::class);
    }

    public function goodDetail(){
        return $this->hasONe(GoodDetail::class, 'good_id', 'good_id');
    }
}
