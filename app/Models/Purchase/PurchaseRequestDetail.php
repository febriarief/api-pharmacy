<?php

namespace App\Models\Purchase;

use App\Models\BaseModel;

class PurchaseRequestDetail extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_request_id',
        'item_name',
        'item_unit',
        'supplier_name',
        'qty'
    ];
}
