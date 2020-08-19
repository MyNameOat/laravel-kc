<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HSProduct extends Model
{
    public function informationReceiptProduct(){
        return $this->belongsTo(InformationReceiptProduct::class);
    }

    public function good(){
        return $this->belongsTo(Good::class);
    }
    
}
