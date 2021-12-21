<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uniCode',
        'start_date',
        'end_date',
        'description',
        'totHours',
        'active',
        'company_id',
        'client_id',
        'slug',
        'type',
    ];


    public function tycoonGroupCompany()
    {
        return $this->belongsTo(TycoonGroupCompany::class, 'company_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'contract_id');
    }

}
