<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TycoonGroupCompany extends Model
{
    use HasFactory;

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'company_id');
    }

    protected $fillable = [
        'businessName',
        'email',
        'emailPEC',
        'pIva',
        'address',
        'buldingNum',
        'city',
        'province',
        'country',
        'postalCode',
        'phone',
        'fax',
        'website',
    ];
}
