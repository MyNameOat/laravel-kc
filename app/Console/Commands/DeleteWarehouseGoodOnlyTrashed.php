<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\WarehouseGood;
use App\WarehouseGoodBalance;

class DeleteWarehouseGoodOnlyTrashed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeleteWarehouseGoodOnlyTrashed';

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
        $warehouseGoodOnlyTrasheds = WarehouseGood::onlyTrashed()->get();
        foreach ($warehouseGoodOnlyTrasheds as $warehouseGoodOnlyTrashed) {
            $warehouseGoodBalances = WarehouseGoodBalance::where('warehouse_good_id', $warehouseGoodOnlyTrashed->id)->get();
            foreach ($warehouseGoodBalances as $warehouseGoodBalance) {
                $warehouseGoodBalance->delete();
            }
        }
    }
}
