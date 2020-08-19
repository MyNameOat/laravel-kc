<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Rr extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $visible = ['code', 'warehouse', 'document_at', 'tax_at', 'credit', 'pay_at', 'vendor', 'total_after_vat', 'vat', 'total', 'vat_status'];

    protected $dates = [
    'created_at',
    'edit_at',
    'updated_at',
    'deleted_at',
    'document_at',
    'tax_at',
    'pay_at',
    'approve_at',
    'none_approve_at',
    ];

    public function parentRrs()
    {
        return $this->hasone(Rr::class, 'parent_id');
    }

    public function parentRr()
    {
        return $this->belongsTo(Rr::class, 'parent_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function createUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function editUser()
    {
        return $this->belongsTo(User::class, 'edit_user_id');
    }

    public function deleteUser()
    {
        return $this->belongsTo(User::class, 'deleted_user_id');
    }

    public function approveUser()
    {
        return $this->belongsTo(User::class, 'approve_user_id');
    }

    public function noneApproveUser()
    {
        return $this->belongsTo(User::class, 'none_approve_user_id');
    }

    public function rrGoods()
    {
        return $this->hasMany(RrGood::class)->withTrashed();
    }

    public function rrLancosts()
    {
        return $this->hasMany(RrLancost::class);
    }

    public function rrBinGoods()
    {
        return $this->hasMany(RrGood::class)->withTrashed();
    }

    public function scopeSearch($query, $keyword)
    {
        if ($keyword['warehouse_id'] != 0) {
            $query = $query->where('warehouse_id', $keyword['warehouse_id']);
        } elseif ($keyword['warehouse_id'] == 0) {
            $query = $query;
        }

        if (isset($keyword['start_at']) && $keyword['start_at'] != '') {
            $start = Carbon::createFromFormat('d/m/Y H:i:s', $keyword['start_at'].' 00:00:00');
            $query = $query->where('document_at', '>', $start);
        }

        if (isset($keyword['end_at']) && $keyword['end_at'] != '') {
            $end = Carbon::createFromFormat('d/m/Y H:i:s', $keyword['end_at'].' 23:59:59');
            $query = $query->where('document_at', '<=', $end);
        }

        if (isset($keyword['code']) && $keyword['code'] != '') {
            $query = $query->where('code', 'like', '%'.$keyword['code'].'%');
        }

        return $query;
    }
}
