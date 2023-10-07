<?php

namespace App\Models\MasterItem;

use App\Models\BaseModel;

class Item extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'unit_id',
        'price'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'unit_id'
    ];

    /**
     * Get relation data of unit
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit() 
    {
        return $this->belongsTo(\App\Models\MasterItem\Unit::class, 'unit_id', 'id')->select(['id', 'name', 'short']);
    }
}
