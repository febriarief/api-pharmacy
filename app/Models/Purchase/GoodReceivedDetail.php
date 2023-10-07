<?php

namespace App\Models\Purchase;

use App\Models\BaseModel;

class GoodReceivedDetail extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'good_received_id',
        'purchase_order_id',
        'item_name',
        'item_unit',
        'supplier_name',
        'qty',
        'qty_order'
    ];

    /**
     * Get detail relation of item
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(\App\Models\Purchase\PurchaseOrder::class, 'purchase_order_id', 'id');
    }
}
