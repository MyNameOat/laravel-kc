<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function member_type()
    {
        return $this->belongsTo(Member_type::class);
    }

    public function customerBillAddress()
    {
        return $this->hasOne(CustomerBillAddress::class, 'customer_id');
    }

}
