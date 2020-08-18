<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitionGood extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'requisition_id',
        'warehouse_good_id',
        'good_id',
        'amount',
        'unit_id',
        'cost_per_unit',
        'cost',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function warehouseGood()
    {
        return $this->belongsTo(WarehouseGood::class);
    }

    public function warehouseGoodBalance()
    {
        return $this->hasOne(WarehouseGoodBalance::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getWarehouseGoodFullNameAttribute()
    {
        $name = $this->warehouseGood->full_name;
        return $name;
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

//    public function getSumCostAttribure()
//    {
//        $this->warehouseGood->skus->reduceWarehouseGood->warehouseGoodCosts->sum('warehouseGoodCosts');
//    }
}
