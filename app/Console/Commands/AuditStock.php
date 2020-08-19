<?php

namespace App\Console\Commands;

use App\Good;
use App\GoodAudit;
use App\GoodSetting;
use App\GoodView;
use App\Warehouse;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class AuditStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:audit-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $goodSettings = GoodSetting::with('warehouse')->where('min', '>', 0)->orWhere('max', '>', 0)->get();

        foreach ($goodSettings as $goodSetting) {
            $goodViews = GoodView::where('good_id', $goodSetting->good_id)->where('warehouse_id', $goodSetting->warehouse_id)->get();
            if ($goodViews->count() == 0) {
                $currentAmount = 0;
            } else {
                $currentAmount = $goodViews->sum('balance_amount');
            }
            if ($goodSetting->min > 0 && $currentAmount < $goodSetting->min) {
                $goodAudit = GoodAudit::where('good_id', $goodSetting->good_id)->where('warehouse_id', $goodSetting->warehouse_id)->first();
                if ($goodAudit == null) {
                    $goodAudit = new GoodAudit;
                }
                $good = Good::find($goodSetting->good_id);
                $goodAudit->good_id = $goodSetting->good_id;
                $goodAudit->warehouse_id = $goodSetting->warehouse_id;
                $goodAudit->min = $goodSetting->min;
                $goodAudit->max = $goodSetting->max;
                $goodAudit->current_amount = $currentAmount;
                $goodAudit->message = "คลัง ".$goodSetting->warehouse->name." สินค้า ".$good->code." ".$good->name." มีจำนวนน้อยกว่ากำหนด (จำนวนปัจจุบัน ".$currentAmount." จำนวนขั้นต่ำที่กำหนดไว้ ".$goodSetting->min.")";
                $goodAudit->save();
            }

            if ($goodSetting->max > 0 && $currentAmount > $goodSetting->max) {
                $goodAudit = GoodAudit::where('good_id', $goodSetting->good_id)->where('warehouse_id', $goodSetting->warehouse_id)->first();
                if ($goodAudit == null) {
                    $goodAudit = new GoodAudit;
                }
                $good = Good::find($goodSetting->good_id);
                $goodAudit->good_id = $goodSetting->good_id;
                $goodAudit->warehouse_id = $goodSetting->warehouse_id;
                $goodAudit->min = $goodSetting->min;
                $goodAudit->max = $goodSetting->max;
                $goodAudit->current_amount = $currentAmount;
                $goodAudit->message = "คลัง ".$goodSetting->warehouse->name." สินค้า ".$good->code." ".$good->name." มีจำนวนมากกว่ากำหนด (จำนวนปัจจุบัน ".$currentAmount." จำนวนสูงสุดที่กำหนดไว้ ".$goodSetting->max.")";
                $goodAudit->save();
            }
        }
    }
}
