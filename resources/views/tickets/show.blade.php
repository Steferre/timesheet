@extends('layouts.app')
@php
$query = null;
if (isset($data['getParams'])){
    $token = isset($data['getParams']['_token']) ? $data['getParams']['_token'] : null;
    $contractN = $data['getParams']['contractN'];
    $startingDR = $data['getParams']['startingDR'];
    $endingDR = $data['getParams']['endingDR'];
    $searchedC = $data['getParams']['searchedC'];
    $searchedCDC = $data['getParams']['searchedCDC'];
    $contractStatus = $data['getParams']['contractStatus'];
    $page = isset($data['getParams']['page']) ? $data['getParams']['page'] : null;
    $query= '?_token='. $token .'&contractN='.$contractN.'&startingDR='.$startingDR.'&endingDR='.$endingDR.'&searchedC='.$searchedC.'&searchedCDC='.$searchedCDC.'&contractStatus='.$contractStatus.'&page='.$page;
} else {
    $query;
}
@endphp
@section('scripts')
    <script>
        function confirmDelete() {
            const deleteBtn = document.getElementById("deleteBtn");

            let response = null;

            response = confirm("Vuoi eliminare definitivamente il ticket?");

            if (!response) {
                event.preventDefault();
            }
        }
    </script>
@stop

@section('headers')
    <h1 class="text-center">Dettaglio Ticket</h1>
    <div class="d-flex justify-content-around py-2">
        <div title="lista ticket">
            <a href="{{ route('tickets.index').$query }}" class="btn btn-primary" role="button">
                <i class="bi bi-box-arrow-left"></i>
            </a>
        </div>
        <div title="modifica">
            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-primary" role="button">
                <i class="bi bi-pencil-fill"></i>
            </a>
        </div>
        <div title="elimina ticket">
            <form method="POST" action="{{ route('tickets.destroy', $ticket->id) }}">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="confirmDelete()" class="btn btn-danger" id="deleteBtn">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </form>
        </div>
    </div>  
@stop

@section('content')
    <div class="row mt-5">
        <div class="col-4 text-center">Nome contratto: <strong>{{ $ticket->contract->name }}</strong></div>
        <div class="col-4 text-center">Aperto da: <strong>{{ $ticket->openBy }}</strong></div>
        <div class="col-4 text-center">Eseguito da: <strong>{{ $ticket->performedBy }}</strong></div>
    </div>
    <div class="row mt-3">
        <div class="col-4 text-center">Ore intervento: <strong>{{ ($ticket->workTime + $ticket->extraTime) }}</strong></div>
        <div class="col-4 text-center">Data apertura: <strong>{{ date('d-m-Y', strtotime($ticket->start_date)) }}</strong></div>
        <div class="col-4 text-center">Data chiusura: <strong>{{ date('d-m-Y', strtotime($ticket->end_date)) }}</strong></div>
    </div>

    <p class="text-center mt-3">Commento: <strong>{{ $ticket->comments }}</strong></p>
@stop