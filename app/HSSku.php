<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HSSku extends Model
{
    public function informationReceiptSku(){
        return $this->belongsTo(InformationReceiptSku::class);
    }

    public function good(){
        return $this->belongsTo(Good::class);
    }
    
}
