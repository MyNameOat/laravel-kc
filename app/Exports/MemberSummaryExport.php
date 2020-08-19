<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class MemberSummaryExport implements FromView
{

    use Exportable;


    public function __construct($bigDatas)
    {

        $this->bigDatas = $bigDatas;

    }

    public function view(): View
    {
        return view('whs-center.members.set-members.export-excel', ['bigDatas' => $this->bigDatas ]);
    }
}
