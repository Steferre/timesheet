<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
    
    public function cdc()
    {
        return $this->belongsTo(Cdcs::class, 'cdc_id');
    }

    protected $fillable = [
        'start_date',
        'end_date',
        'workTime',
        'comments',
        'performedBy',
        'contract_id',
        'openBy',
        'cdc_id',
        'extraTime',
    ];
}
