<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'client_id');
    }

    protected $fillable = [
        'businessName',
        'email',
        'pIva',
        'address',
        'buldingNum',
        'city',
        'province',
        'country',
        'postalCode',
        'phone',
    ];
}
