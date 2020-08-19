<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Good extends Model
{
    use SoftDeletes;
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function goodView()
    {
        return $this->hasOne(GoodView::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function poGoods()
    {
        return $this->hasMany(PoGood::class);
    }

    public function goodSettings()
    {
        return $this->hasMany(GoodSetting::class);
    }

    public function amountMin($warehouse_id)
    {
        $goodSetting = $this->goodSettings->where('warehouse_id', $warehouse_id)->first();

        if ($goodSetting == null) {
            return 0;
        } else {
            return $goodSetting->min;
        }
    }

    public function amountMax($warehouse_id)
    {
        $goodSetting = $this->goodSettings->where('warehouse_id', $warehouse_id)->first();

        if ($goodSetting == null) {
            return 0.00;
        } else {
            return $goodSetting->max;
        }
    }

    public function goodRatio()
    {
        return $this->hasOne(GoodRatio::class);
    }

    public function goodDetail()
    {
        return $this->hasOne(GoodDetail::class);
    }

    public function goodWarehouse()
    {
        return $this->hasMany(GoodWarehouse::class);
    }

}
