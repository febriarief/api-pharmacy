<?php

namespace App\Models\Purchase;

use App\Models\BaseModel;

class PurchaseOrderDetail extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_order_id',
        'purchase_request_id',
        'item_name',
        'item_unit',
        'supplier_name',
        'qty',
        'price'
    ];

    /**
     * Get detail relation of item
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(\App\Models\Purchase\PurchaseRequest::class, 'purchase_request_id', 'id');
    }
}
