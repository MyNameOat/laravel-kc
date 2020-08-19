<?php

namespace App\Http;


use App\Adjust;
use App\MenuAccess;
use App\ReceiveTransfer;
use App\Requisition;
use App\Transfer;
use Carbon\Carbon;

function menuAccesses($menu_id)
{
    $data = MenuAccess::whereMenuId($menu_id);
    if (auth()->user()->id != 1) {
        $data = $data->whereUserId(auth()->user()->id);
    }
    $data = $data->get();
    return $data;
}

function countRequisitionApprove()
{
    $menuAccesses = menuAccesses(2);

    $department_ids = $menuAccesses->count() ? $menuAccesses->lists('department_id')->toArray() : [0];
    $requisitions = Requisition::whereIn('department_id', $department_ids)->where('approve_user_id', 0)->where('edit_user_id', 0)->count();

    return $requisitions;
}

function countTransferApprove()
{
    $menuAccesses = menuAccesses(26);

    $department_ids = $menuAccesses->count() ? $menuAccesses->lists('department_id')->toArray() : [0];
    $transfers = Transfer::whereIn('from_department_id', $department_ids)->where('approve_user_id', 0)->where('none_approve_user_id', 0)->orWhere('lead_status', 1)->whereIn('from_department_id', $department_ids)->where('none_approve_user_id', 0)->count();

    return $transfers;
}

function countLeadProblem()
{
    $menuAccesses = menuAccesses(4);

    $department_ids = $menuAccesses->count() ? $menuAccesses->lists('department_id')->toArray() : [0];
    $transfers = Transfer::whereIn('from_department_id', $department_ids)->where('lead_status', 9)->where('source_user_id', 0)->count();

    return $transfers;
}

function countLead()
{
    $menuAccesses = menuAccesses(5);

    $department_ids = $menuAccesses->count() ? $menuAccesses->lists('department_id')->toArray() : [0];
    $transfers = Transfer::with('transferReceiveViews')->whereIn('to_department_id', $department_ids)->where('approve_user_id', '>', 0)->where('lead_status', 0)->whereHas('transferReceiveViews', function ($query) {
        $query->whereRaw('amount > receive_amount');
    })->count();

    return $transfers;
}

function countLeadProblemFix()
{
    $menuAccesses = menuAccesses(5);

    $department_ids = $menuAccesses->count() ? $menuAccesses->lists('department_id')->toArray() : [0];
    $transfers = Transfer::whereIn('to_department_id', $department_ids)->where('lead_status', 9)->where('source_user_id', '<>', 0)->count();

    return $transfers;
}

function countAdjust()
{
    $menuAccesses = menuAccesses(6);

    $department_ids = $menuAccesses->count() ? $menuAccesses->lists('department_id')->toArray() : [0];
    $adjusts = Adjust::whereIn('department_id', $department_ids)->where('edit_user_id', 0)->where('approve_user_id', 0)->where('none_approve_user_id', 0)->count();

    return $adjusts;
}

function countAdjustApprove()
{
    $menuAccesses = menuAccesses(7);

    $department_ids = $menuAccesses->count() ? $menuAccesses->lists('department_id')->toArray() : [0];
    $adjusts = Adjust::whereIn('department_id', $department_ids)->where('edit_user_id', 0)->where('approve_user_id', 0)->where('none_approve_user_id', 0)->count();
    return $adjusts;
}

function runCode($warehouseCode, $text)
{
    $now_at = Carbon::now();

    $month = $now_at->month;

    if (strlen($month) == 1) {
        $month = '0' . $month;
    }

    $year = substr($now_at->year + 543, -2);

    $search_code = $warehouseCode . $text . $year . $month;

    if ($text == 'TO') {
        $lastest_code = Transfer::where('code', 'LIKE', $search_code . '%')->orderBy('code', 'desc')->first();
    } elseif ($text == 'TI') {
        $lastest_code = ReceiveTransfer::where('code', 'LIKE', $search_code . '%')->orderBy('code', 'desc')->first();
    }

    if ($lastest_code == null) {
        $current_code = $search_code . '-001';
        return $current_code;
    }

    $code = $lastest_code->code;

    $num = (integer) substr($code, -3);
    $code = $num + 1;
    $count = 3 - strlen($code);

    for ($i = 0; $i < $count; $i++) {
        $code = '0' . $code;
    }

    $current_code = $search_code . '-' . $code;

    return $current_code;
}
