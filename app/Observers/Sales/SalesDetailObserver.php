<?php

namespace App\Observers\Sales;

use Carbon\Carbon;

use App\Models\MasterItem\Stock;
use App\Models\MasterItem\StockCard;
use App\Models\Sales\SalesDetail;

class SalesDetailObserver
{
    /**
     * Handle the SalesDetail "created" event.
     *
     * @param  \App\Models\Sales\SalesDetail  $salesDetail
     * @return void
     */
    public function created(SalesDetail $salesDetail)
    {
        $stock = Stock::where('item_id', $salesDetail->item_id)->first();
        $stock->total = $stock->total - $salesDetail->qty;
        $stock->save();

        $salesDateTime = Carbon::parse($salesDetail->sales->created_at)->format('Y-m-d H:i:s');
        StockCard::create([
            'stock_id'   => $stock->id,
            'type'       => 'OUT',
            'qty'        => $salesDetail->qty,
            'qty_remain' => $stock->total,
            'note'       => 'Sales on ' . $salesDateTime . ' by ' . $salesDetail->sales->cashier_name
        ]);
    }

    /**
     * Handle the SalesDetail "updated" event.
     *
     * @param  \App\Models\Sales\SalesDetail  $salesDetail
     * @return void
     */
    public function updated(SalesDetail $salesDetail)
    {
        //
    }

    /**
     * Handle the SalesDetail "deleted" event.
     *
     * @param  \App\Models\Sales\SalesDetail  $salesDetail
     * @return void
     */
    public function deleted(SalesDetail $salesDetail)
    {
        //
    }

    /**
     * Handle the SalesDetail "restored" event.
     *
     * @param  \App\Models\Sales\SalesDetail  $salesDetail
     * @return void
     */
    public function restored(SalesDetail $salesDetail)
    {
        //
    }

    /**
     * Handle the SalesDetail "force deleted" event.
     *
     * @param  \App\Models\Sales\SalesDetail  $salesDetail
     * @return void
     */
    public function forceDeleted(SalesDetail $salesDetail)
    {
        //
    }
}
