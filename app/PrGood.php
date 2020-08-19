<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrGood extends Model
{
    use SoftDeletes;

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function goodViews()
    {
        return $this->hasMany(GoodView::class, 'good_id');
    }

    public function pr()
    {
        return $this->belongsTo(Pr::class);
    }

    public function getLastPriceAttribute()
    {
        $last_po_good = $this->good->poGoods->last();

        if ($last_po_good != null) {
            $last_price = number_format($last_po_good->price, 2);
        } else {
            $last_price = 'ยังไม่เคยสั่งซื้อ';
        }

        return $last_price;
    }

    public function getLastVendorAttribute()
    {
        $last_po_good = $this->good->poGoods->last();

        if ($last_po_good != null) {
            $last_vendor = $last_po_good->po->vendor->vendor_th_name;
        } else {
            $last_vendor = '-';
        }

        return $last_vendor;
    }

    public function getAmountInWarehouseAttribute()
    {
        $goodView = $this->goodViews->where('warehouse_id', $this->pr->warehouse_id)->first();

        if ($goodView == null) {
            return 0.00;
        } else {
            return number_format($goodView->balance_amount, 2);
        }
    }

    public function poGood()
    {
        return $this->hasOne(PoGood::class);
    }
}
