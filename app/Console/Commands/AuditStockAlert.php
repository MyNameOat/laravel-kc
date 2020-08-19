<?php

namespace App\Console\Commands;

use App\Good;
use App\GoodAudit;
use App\GoodView;
use Illuminate\Console\Command;

class AuditStockAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:audit-stock-alert';

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

    public function handle()
    {
        $goodAudits = GoodAudit::all();

        foreach ($goodAudits as $goodAudit) {
            $goodViews = GoodView::where('good_id', $goodAudit->good_id)->where('warehouse_id', $goodAudit->warehouse_id)->get();
            if ($goodViews->count() == 0) {
                $currentAmount = 0;
            } else {
                $currentAmount = $goodViews->sum('balance_amount');
            }

            if ($goodAudit->min > 0 && $currentAmount > $goodAudit->min && $currentAmount < $goodAudit->max) {
                $goodAudit->delete();
            }
        }
    }
}
