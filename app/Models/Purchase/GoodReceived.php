<?php

namespace App\Models\Purchase;

use App\Models\BaseModel;
use Carbon\Carbon;

class GoodReceived extends BaseModel
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'note',
        'created_by'
    ];

    /**
     * Generate a new unique ID for Purchase Request.
     *
     * @return string
     */
    public static function generateId()
    {
        $currentDate = Carbon::now();
        $currentYearMonth = $currentDate->format('Ym');
        
        $lastRequest = self::where('id', 'like', "GR{$currentYearMonth}%")
            ->orderBy('id', 'desc')
            ->first();
    
        $increment = $lastRequest ? intval(substr($lastRequest->id, -5)) + 1 : 1;
        
        $incrementFormatted = str_pad($increment, 5, '0', STR_PAD_LEFT);
        $purchaseRequestId = "GR{$currentYearMonth}{$incrementFormatted}";
    
        return $purchaseRequestId;
    }

    /**
     * Get data detail relation of purchase order detail
     * 
     * @return \App\Models\Utils\HasManySyncable
     */
    public function goodReceivedDetail()
    {
        return $this->hasMany(\App\Models\Purchase\GoodReceivedDetail::class, 'good_received_id', 'id');
    }
}
