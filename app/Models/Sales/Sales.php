<?php

namespace App\Models\Sales;

use App\Models\BaseModel;

class Sales extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total',
        'money_received',
        'money_change',
        'cashier_name'
    ];

    /**
     * Get data detail relation of purchase order detail
     * 
     * @return \App\Models\Utils\HasManySyncable
     */
    public function salesDetail()
    {
        return $this->hasMany(\App\Models\Sales\SalesDetail::class, 'sales_id', 'id');
    }
}
