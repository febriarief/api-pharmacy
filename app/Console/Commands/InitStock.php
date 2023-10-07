<?php

namespace App\Console\Commands;

use App\Models\MasterItem\Stock;
use App\Models\MasterItem\StockCard;
use App\Models\MasterItem\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to init stock';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::beginTransaction();

            Stock::truncate();
            
            $items = Item::get();
            foreach($items as $item) {
                $stock = Stock::create([
                    'item_id' => $item->id,
                    'total' => 0
                ]);

                StockCard::create([
                    'stock_id'      => $stock->id,
                    'type'          => 'IN',
                    'qty'           => 0,
                    'qty_remain'    => 0,
                    'note'          => 'Auto generate from init stock'
                ]);
            }

            DB::commit();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return Command::SUCCESS;

        } catch(\Exception $e) {
            DB::rollBack();
            return $this->info($e->getMessage());
        }
    }
}
