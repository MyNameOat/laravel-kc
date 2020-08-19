<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductReam extends Model
{
    protected $dates = ['doc_date'];

    public function informationReceipt()
    {
        return $this->belongsTo(InformationReceipt::class);
    }
}
