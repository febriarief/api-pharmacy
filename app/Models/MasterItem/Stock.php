<?php

namespace App\Models\MasterItem;

use App\Models\BaseModel;

class Stock extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'total'
    ];

    /**
     * Relation detail to table items
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(\App\Models\MasterItem\Item::class, 'item_id', 'id');
    }
}
