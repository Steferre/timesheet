<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContractsExport implements FromCollection, WithHeadings
{
    protected $contracts;

    public function headings(): array
    {
        return [
            'nome contratto',
            'codice contratto',
            'data apertura contratto',
            'data fine contratto',
            'ore totali pacchetto',
            'ore utilizzate',
            'valore % ore utilizzate',
            'Società del gruppo',
            'Azienda Cliente',
            'descrizione',
        ];
    }

    public function __construct($contracts)
    {
        $this->contracts = $contracts;
    }

    public function collection()
    {
        return $this->contracts;
    }
}
