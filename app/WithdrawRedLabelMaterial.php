<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawRedLabelMaterial extends Model
{
    use softDeletes;
    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function withdrawRedLabelProducts()
    {
        return $this->hasMany(WithdrawRedLabelProduct::class);
    }

    public function getGoodBalanceAmountAttribute()
    {
        $goodBalanceAmount = 0;
        $goodView = GoodView::where('good_id', $this->good_id)->where('warehouse_id', $this->withdrawRedLabel->warehouse_id)->first();
        if ($goodView != null) {
            $goodBalanceAmount = $goodView->balance_amount;
        }
        return $goodBalanceAmount;
    }

    public function withdrawRedLabel()
    {
        return $this->belongsTo(WithdrawRedLabel::class);
    }
}
