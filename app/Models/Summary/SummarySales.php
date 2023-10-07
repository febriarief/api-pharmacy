<?php

namespace App\Models\Summary;

use App\Models\BaseModel;

class SummarySales extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total'
    ];
}
