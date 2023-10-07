<?php

namespace App\Observers\Sales;

use Carbon\Carbon;
use App\Models\Sales\Sales;
use App\Models\Summary\SummarySales;

class SalesObserver
{
    /**
     * Handle the Sales "created" event.
     *
     * @param  \App\Models\Sales\Sales  $sales
     * @return void
     */
    public function created(Sales $sales)
    {
        $now = Carbon::now();
        $summary = SummarySales::whereDate('created_at', $now)->first();
        if ($summary) {
            $summary->total += $sales->total;
            $summary->save();
        } else {
            SummarySales::create(['total' => $sales->total]);
        }
    }

    /**
     * Handle the Sales "updated" event.
     *
     * @param  \App\Models\Sales\Sales  $sales
     * @return void
     */
    public function updated(Sales $sales)
    {
        //
    }

    /**
     * Handle the Sales "deleted" event.
     *
     * @param  \App\Models\Sales\Sales  $sales
     * @return void
     */
    public function deleted(Sales $sales)
    {
        //
    }

    /**
     * Handle the Sales "restored" event.
     *
     * @param  \App\Models\Sales\Sales  $sales
     * @return void
     */
    public function restored(Sales $sales)
    {
        //
    }

    /**
     * Handle the Sales "force deleted" event.
     *
     * @param  \App\Models\Sales\Sales  $sales
     * @return void
     */
    public function forceDeleted(Sales $sales)
    {
        //
    }
}
