<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $dates = [
        'created_at',
        'edit_at',
        'updated_at',
        'deleted_at',
        'document_at',
        'approve_at',
        'none_approve_at',
        'ream_at',
        'deliver_at',
    ];

    public function warehouseFrom()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function warehouseTo()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
}
