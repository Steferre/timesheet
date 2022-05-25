@extends('layouts.app')
@php
$query = null;
if (isset($data['getParams'])){
    $token = isset($data['getParams']['_token']) ? $data['getParams']['_token'] : null;
    $searchedC = $data['getParams']['searchedC'];
    $contractT = $data['getParams']['contractT'];
    $page = isset($data['getParams']['page']) ? $data['getParams']['page'] : null;
    $query= '?_token='. $token .'&searchedC='.$searchedC.'&contractT='.$contractT.'&page='.$page;
} else {
    $query;
}
@endphp

@section('scripts')
<script>
    function showHideBox() {
        const infoBox = document.getElementById("infoBox");
        const openBtn = document.getElementById("openBtn");
        const closeBtn = document.getElementById("closeBtn");

        //console.log(infoBox);

        if (infoBox.style.display === "none") {
            infoBox.style.display = "block";
            openBtn.style.display = "none";
            closeBtn.style.display = "block";
        } else {
            infoBox.style.display = "none";
            openBtn.style.display = "block";
            closeBtn.style.display = "none";
        }
    }
    function confirmDelete() {
        const deleteBtn = document.getElementById("deleteBtn");

        let response = null;

        response = confirm("Vuoi eliminare definitivamente il contratto?");

        if (!response) {
            event.preventDefault();
        }
    }
</script>
@stop

@section('headers')
    <h1 class="text-center">Dettaglio Contratto</h1>
    <div class="d-flex justify-content-around py-2">
        @if($contract->active === 'Y')
            <div title="lista contratti">
                <a href="{{ route('contracts.index').$query }}" class="btn btn-primary" role="button">
                    <i class="bi bi-box-arrow-left"></i>
                </a>
            </div>
        @else
            <div title="lista contratti">
                <a href="{{ route('contracts.indexConClose').$query }}" class="btn btn-primary" role="button">
                    <i class="bi bi-box-arrow-left"></i>
                </a>
            </div>
        @endif
        @if (Auth::user()['role'] == 'admin')
            <div title="modifica">
                <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-primary" role="button">
                    <i class="bi bi-pencil-fill"></i>
                </a>
            </div>
            @if (count($contract->tickets) == 0)
                <div title="elimina contratto">
                    <form method="POST" action="{{ route('contracts.destroy', $contract->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="confirmDelete()" class="btn btn-danger" id="deleteBtn">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                </div>
            @endif
        @else
            <div title="modifica">
                <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-primary disabled" role="button">
                    <i class="bi bi-pencil-fill"></i>
                </a>
            </div>
        @endif
    </div>  
@stop

@section('content')
    <div class="row mt-3">
        <div class="col-6 text-center">Nome contratto: <strong>{{ $contract->name }}</strong></div>
        @if($contract->uniCode)
            <div class="col-6 text-center">Codice contratto: <strong>{{ $contract->uniCode }}</strong></div>
        @else
            <div class="col-6 text-center">Codice contratto: <strong> --- </strong></div>
        @endif
    </div>
    <div class="row mt-3">
        <div class="col-4 text-center">Data apertura: <strong>{{ date('d-m-Y', strtotime($contract->start_date)) }}</strong></div>
        @if($contract->end_date)
        <div class="col-4 text-center">Data chiusura: <strong>{{ date('d-m-Y', strtotime($contract->end_date)) }}</strong></div>
        @else
        <div class="col-4 text-center">Data chiusura: <strong>Fine ore pacchetto</strong></div>
        @endif
        <div class="col-4 text-center">Azienda cliente: <strong>{{ $contract->client->businessName }}</strong></div>
    </div>
    <div class="row mt-3">
        @if($contract->active === 'Y')
        <div class="col-4 text-center">Stato: <strong>Attivo</strong></div>
        @elseif($contract->active === 'N')
        <div class="col-4 text-center">Stato: <strong>Chiuso</strong></div>
        @endif
        @if($contract->type === 'increase')
        <div class="col-4 text-center">Tipologia contratto: <strong>Accumulo ore</strong></div>
        @elseif($contract->type === 'decrease')
        <div class="col-4 text-center">Tipologia contratto: <strong>Decremento ore</strong></div>
        @endif
        @if($contract->totHours)
        <div class="col-4 text-center">Ore pacchetto: <strong>{{ $contract->totHours }}</strong></div>
        @else
        <div class="col-4 text-center">Ore pacchetto: <strong>Non definite</strong></div>
        @endif
    </div>

    <p class="text-center mt-3">Descrizione: <strong>{{ $contract->description }}</strong></p>
    
    @if(count($contract->tickets) > 0)
        <button onclick="showHideBox()" id="openBtn" style="display: none;" class="btn btn-info">Mostra Ticket</button>
        <button onclick="showHideBox()" id="closeBtn" style="display: block;" class="btn btn-info">Nascondi Ticket</button>
        <div id="infoBox" style="display: block;">
            <table class="table table-sm table-hover table-borderless">
                <caption style="caption-side: top;">Lista Ticket</caption>
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Data Inizio</th>
                        <th>Data Fine</th>
                        <th>Tempo int.</th>
                        <th>Ore extra</th>
                        <th>Commenti</th>
                        <th>Owner</th>
                        <th>Centro di costo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->tickets->sortByDesc('end_date') as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ date('d-m-Y', strtotime($ticket->start_date)) }}</td>
                            <td>{{ date('d-m-Y', strtotime($ticket->end_date)) }}</td>
                            <td>{{ $ticket->workTime }}</td>
                            <td>{{ $ticket->extraTime }}</td>
                            <td style="width: 30%;">{{ $ticket->comments }}</td>
                            <td>{{ $ticket->performedBy }}</td>
                            <td>{{ $ticket->cdc->businessName }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@stop