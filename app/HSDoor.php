<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HSDoor extends Model
{
    public function informationReceiptDoor(){
        return $this->belongsTo(InformationReceiptDoor::class);
    }

    public function good(){
        return $this->belongsTo(Good::class);
    }
}
