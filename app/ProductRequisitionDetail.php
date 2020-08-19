<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRequisitionDetail extends Model
{
    public function productRequisition()
    {
        return $this->belongsTo(ProductRequisition::class);
    }
}
