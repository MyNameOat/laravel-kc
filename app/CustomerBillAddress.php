<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerBillAddress extends Model
{
    //
    protected $table = 'customer_bill_addresses';

    public function customer() 
    {
       return $this->belongsTo(Customer::class);
    }
}
