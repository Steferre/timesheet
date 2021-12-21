<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cdc extends Model
{
    use HasFactory;

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'cdc_id');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'cdcs_clients', 'cdcID', 'clientID');
    }

    protected $fillable = [
        'businessName',
    ];
}
