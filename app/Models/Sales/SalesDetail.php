<?php

namespace App\Models\Sales;

use App\Models\BaseModel;

class SalesDetail extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_id',
        'item_id',
        'item_name',
        'qty',
        'price'
    ];

    public function sales()
    {
        return $this->belongsTo(\App\Models\Sales\Sales::class, 'sales_id', 'id');
    }
}
