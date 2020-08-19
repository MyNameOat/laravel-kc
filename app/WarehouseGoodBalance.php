<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class WarehouseGoodBalance extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function getDocumentCreateAttribute()
    {
        if ($this->requisition_good_id != null) {
            return $this->requisitionGood->requisition->document_at;
        } elseif ($this->product_requisition_detail_id != null) {
            return $this->productRequisitionDetail->productRequisition->doc_date;
        } elseif ($this->transfer_good_id != null) {
            return $this->transferGood->transfer->document_at;
        } elseif ($this->product_ream_id != null) {
            return $this->productReam->doc_date;
        } elseif ($this->hs != null) {
            return $this->hs->doc_date;
        } elseif ($this->invoice != null) {
            return $this->invoice->doc_date;
        } elseif ($this->productReceive != null) {
            return $this->productReceive->doc_date;
        } elseif ($this->warehouseGood->rrGood != null && $this->adjustGood == null) {
            return $this->warehouseGood->rrGood->rr->document_at;
        } elseif ($this->receiveTransferGood != null) {
            return $this->receiveTransferGood->receiveTransfer->document_at;
        } elseif ($this->adjustGood != null) {
            return Carbon::parse($this->adjustGood->adjust->document_at);
        }else {
            return Null;
        }
    }

    public function transferGood()
    {
        return $this->belongsTo(TransferGood::class);
    }

    public function hs()
    {
        return $this->belongsTo(HS::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiveTransferGood()
    {
        return $this->belongsTo(ReceiveTransferGood::class);
    }

    public function adjustGood()
    {
        return $this->belongsTo(AdjustGood::class);
    }
    public function requisitionGood()
    {
        return $this->belongsTo(RequisitionGood::class);
    }

    public function warehouseGood()
    {
        return $this->belongsTo(WarehouseGood::class);
    }

    public function productRequisitionDetail()
    {
        return $this->belongsTo(ProductRequisitionDetail::class);
    }

    public function productReam()
    {
        return $this->belongsTo(ProductReam::class);
    }

    public function productReceive()
    {
        return $this->belongsTo(ProductReceive::class);
    }
}
