@extends('layouts.app')

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
        <div title="lista contratti">
            <a href="{{ route('contracts.index') }}" class="btn btn-primary" role="button">
                <i class="bi bi-box-arrow-left"></i>
            </a>
        </div>
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
        <div class="col-6 text-center">Codice contratto: <strong>{{ $contract->uniCode }}</strong></div>
    </div>
    <div class="row mt-3">
        <div class="col-4 text-center">Data apertura: <strong>{{ date('d-m-Y', strtotime($contract->start_date)) }}</strong></div>
        <div class="col-4 text-center">Data chiusura: <strong>{{ date('d-m-Y', strtotime($contract->end_date)) }}</strong></div>
        <div class="col-4 text-center">Azienda cliente: <strong>{{ $contract->client->businessName }}</strong></div>
    </div>
    <div class="row mt-3">
        <div class="col-4 text-center">Stato: <strong>{{ $contract->active }}</strong></div>
        <div class="col-4 text-center">Tipologia contratto: <strong>{{ $contract->type }}</strong></div>
        <div class="col-4 text-center">Ore pacchetto: <strong>{{ $contract->totHours }}</strong></div>
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
                        <th>Tempo intervento</th>
                        <th>Ore extra admin</th>
                        <th>Commenti</th>
                        <th>Owner</th>
                        <th>Centro di costo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->start_date }}</td>
                            <td>{{ $ticket->end_date }}</td>
                            <td>{{ $ticket->workTime }}</td>
                            <td>{{ $ticket->extraTime }}</td>
                            <td>{{ $ticket->comments }}</td>
                            <td>{{ $ticket->performedBy }}</td>
                            <td>{{ $ticket->cdc->businessName }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@stop