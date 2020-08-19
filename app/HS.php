<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HS extends Model
{
    use SoftDeletes;

    protected $table = 'h_s_s';

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function memberBenefit()
    {
        return $this->hasMany(MemberBenefit::class, 'h_s_id');
    }

    public function memberPoint()
    {
        return $this->hasMany(MemberPoint::class, 'h_s_id');
    }

    public function hsSku()
    {
        return $this->hasMany(HSSku::class, 'h_s_id');
    }

    public function hsProduct()
    {
        return $this->hasMany(HSProduct::class, 'h_s_id');
    }

    public function hsDoor()
    {
        return $this->hasMany(HSDoor::class, 'h_s_id');
    }

    public function memberPointBenefit()
    {
        return $this->hasMany(MemberPointBenefit::class, 'h_s_id');
    }

}
