<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;
    public function getFullNameAttribute()
    {
        $name = $this->code.'_'.$this->name;

        return $name;
    }

    public function goodViews()
    {
        return $this->hasMany(GoodView::class);
    }

    public function warehouseGoods()
    {
        return $this->hasMany(WarehouseGood::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
