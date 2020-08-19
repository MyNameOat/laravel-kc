<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberPointBenefit extends Model
{
    use SoftDeletes;

    public function good(){
        return $this->belongsTo(Good::class);
    }

    public function customer()
    {
        return $this->hasOne('App\Customer', 'member_id', 'member_id');
    }

    public function hs(){
        return $this->hasOne(HS::class,'id','h_s_id');
    }

    public function hsSku(){
        return $this->belongsTo(HSSku::class, 'h_s_sku_id');
    }

    public function hsProduct(){
        return $this->belongsTo(HSProduct::class, 'h_s_product_id');
    }

    public function hsDoor(){
        return $this->belongsTo(HSDoor::class, 'h_s_door_id');
    }

    public function hsService(){
        return $this->belongsTo(HSService::class, 'h_s_service_id');
    }
}
