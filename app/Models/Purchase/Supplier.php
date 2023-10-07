<?php

namespace App\Models\Purchase;

use App\Models\BaseModel;

class Supplier extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email'
    ];
}
