<?php

namespace App\Observers\Purchase;

use Illuminate\Support\Facades\DB;

use App\Models\MasterItem\Item;
use App\Models\MasterItem\Stock;
use App\Models\MasterItem\StockCard;
use App\Models\Purchase\GoodReceivedDetail;

class GoodReceivedDetailObserver
{
    /**
     * Handle the Item "created" event.
     *
     * @param  \App\Models\Purchase\GoodReceivedDetail  $goodReceivedDetail
     * @return void
     */
    public function created(GoodReceivedDetail $goodReceivedDetail)
    {
        try {
            DB::beginTransaction();

            $item = Item::where('name', $goodReceivedDetail->item_name)->first();
            if ($item) {
                $stock = Stock::firstOrNew([
                    'item_id' => $item->id
                ], [
                    'total' => 0
                ]);

                $stock->total += $goodReceivedDetail->qty;
                $stock->save();

                StockCard::create([
                    'stock_id'   => $stock->id,
                    'type'       => 'IN',
                    'qty'        => $goodReceivedDetail->qty,
                    'qty_remain' => $stock->total,
                    'note'       => 'Good received from ' . $goodReceivedDetail->good_received_id
                ]);
            }

            DB::commit();

        } catch(\Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Handle the Item "updated" event.
     *
     * @param  \App\Models\Purchase\GoodReceivedDetail  $goodReceivedDetail
     * @return void
     */
    public function updated(GoodReceivedDetail $goodReceivedDetail)
    {
        //
    }

    /**
     * Handle the Item "deleted" event.
     *
     * @param  \App\Models\Purchase\GoodReceivedDetail  $goodReceivedDetail
     * @return void
     */
    public function deleted(GoodReceivedDetail $goodReceivedDetail)
    {
        //
    }

    /**
     * Handle the Item "restored" event.
     *
     * @param  \App\Models\Purchase\GoodReceivedDetail  $goodReceivedDetail
     * @return void
     */
    public function restored(GoodReceivedDetail $goodReceivedDetail)
    {
        //
    }

    /**
     * Handle the Item "force deleted" event.
     *
     * @param  \App\Models\Purchase\GoodReceivedDetail  $goodReceivedDetail
     * @return void
     */
    public function forceDeleted(GoodReceivedDetail $goodReceivedDetail)
    {
        //
    }
}
