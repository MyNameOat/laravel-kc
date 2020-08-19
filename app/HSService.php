<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HSService extends Model
{
    public function informationReceiptService(){
        return $this->belongsTo(InformationReceiptService::class);
    }
}
