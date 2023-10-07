<?php

namespace App\Observers\MasterItem;

use App\Models\MasterItem\Item;
use App\Models\MasterItem\Stock;
use App\Models\MasterItem\StockCard;

class ItemObserver
{
    /**
     * Handle the Item "created" event.
     *
     * @param  \App\Models\MasterItem\Item  $item
     * @return void
     */
    public function created(Item $item)
    {
        $stock = Stock::create([
            'item_id' => $item->id,
            'total'   => 0
        ]);

        StockCard::create([
            'stock_id'   => $stock->id,
            'type'       => 'IN',
            'qty'        => 0,
            'qty_remain' => 0,
            'note'       => 'Auto generate from add item.'
        ]);
    }

    /**
     * Handle the Item "updated" event.
     *
     * @param  \App\Models\MasterItem\Item  $item
     * @return void
     */
    public function updated(Item $item)
    {
        //
    }

    /**
     * Handle the Item "deleted" event.
     *
     * @param  \App\Models\MasterItem\Item  $item
     * @return void
     */
    public function deleted(Item $item)
    {
        //
    }

    /**
     * Handle the Item "restored" event.
     *
     * @param  \App\Models\MasterItem\Item  $item
     * @return void
     */
    public function restored(Item $item)
    {
        //
    }

    /**
     * Handle the Item "force deleted" event.
     *
     * @param  \App\Models\MasterItem\Item  $item
     * @return void
     */
    public function forceDeleted(Item $item)
    {
        //
    }
}
