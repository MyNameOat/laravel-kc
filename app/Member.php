<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    //
    public function member_type(){
        return $this->belongsTo(Member_type::class);
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'id', 'member_id');
    }

    public function bank(){
        return $this->belongsTo(Bank::class);
    }
}
