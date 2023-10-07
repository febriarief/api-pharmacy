<?php

namespace App\Models\MasterItem;

use App\Models\BaseModel;

class Unit extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'short'
    ];
}
