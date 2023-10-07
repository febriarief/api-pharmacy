<?php

namespace App\Models\MasterItem;

use App\Models\BaseModel;

class StockCard extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock_id',
        'type',
        'qty',
        'qty_remain',
        'note'
    ];

    /**
     * Relation detail to table stocks
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo(\App\Models\MasterItem\Stock::class, 'stock_id', 'id');
    }
}
