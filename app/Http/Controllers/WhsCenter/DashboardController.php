<?php

namespace App\Http\Controllers\WhsCenter;

use App\GoodAudit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $goodAudits = GoodAudit::all();
        return view('whs-center.dashboard', compact('goodAudits'));
    }
}
