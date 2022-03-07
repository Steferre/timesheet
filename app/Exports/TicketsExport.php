<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromCollection, WithHeadings
{
    protected $tickets;
    public function headings(): array
    {
        return [
            'Azienda Cliente',
            'Contratto',
            'Eseguito da',
            'Commento intervento',
            'Centro di costo',
            'Data chiusura intervento', 
            'Ore totali intervento'
        ];
    }

    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    public function collection()
    {
        return $this->tickets;
    }
}
