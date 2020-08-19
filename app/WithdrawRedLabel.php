<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawRedLabel extends Model
{
    use softDeletes;
    protected $dates = ['document_at', 'edit_at', 'approve_at', 'none_approve_at'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function editUser()
    {
        return $this->belongsTo(User::class, 'edit_user_id');
    }

    public function deletedUser()
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

    public function parentPr()
    {
        return $this->belongsTo(WithdrawRedLabel::class, 'parent_id')->withTrashed();
    }

    public function withdrawRedLabelMaterials()
    {
        return $this->hasMany(WithdrawRedLabelMaterial::class);
    }

    public function withdrawRedLabelProducts()
    {
        return $this->hasManyThrough(WithdrawRedLabelProduct::class, WithdrawRedLabelMaterial::class);
    }
}
