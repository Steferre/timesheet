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

        response = confirm("Vuoi eliminare definitivamente l'azienda?");

        if (!response) {
            event.preventDefault();
        }
    }
</script>
@stop

@section('headers')
    <h1 class="text-center mb-3">Dettaglio Azienda Cliente</h1>
    <div class="d-flex justify-content-around">
        <div title="torna alla lista delle aziende">
            <a href="{{ route('clients.index') }}" class="btn btn-info" role="button">
                <i class="bi bi-box-arrow-left"></i>
            </a>
        </div>
        <div title="modifica azienda cliente">
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-info" role="button">
                <i class="bi bi-pencil-fill"></i>
            </a>
        </div>
        <div title="elimina cliente">
            <form method="POST" action="{{ route('clients.destroy', $client->id) }}">
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
        <div class="col-3 text-center">Ragione Sociale: <strong>{{ $client->businessName }}</strong></div>
        <div class="col-3 text-center">Email: <strong>{{ $client->email }}</strong></div>
        <div class="col-3 text-center">Partita IVA: <strong>{{ $client->pIva }}</strong></div>
        <div class="col-3 text-center">Telefono: <strong>{{ $client->phone }}</strong></div>
    </div>
    <div class="row mt-3">
        <div class="col-4 text-center">Indirizzo: <strong>{{ $client->address }}</strong></div>
        <div class="col-4 text-center">Numero civico: <strong>{{ $client->buldingNum }}</strong></div>
        <div class="col-4 text-center">Città: <strong>{{ $client->city }}</strong></div>
    </div>
    <div class="row mt-3">
        <div class="col-4 text-center">Provincia: <strong>{{ $client->province }}</strong></div>
        <div class="col-4 text-center">Paese: <strong>{{ $client->country }}</strong></div>
        <div class="col-4 text-center">Codice postale: <strong>{{ $client->postalCode }}</strong></div>
    </div>
    @if(count($cdcs) > 0)
    <div class="row mt-3 mb-5">
        <div class="mr-3">Centri di costo dell'azienda cliente:</div>
        <ul class="list-inline">
            @foreach($cdcs as $cdc)
                <li class="list-inline-item btn-success px-2">{{ $cdc->businessName }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    @if(count($client->contracts) > 0)
    <button onclick="showHideBox()" id="openBtn" style="display: none;" class="btn btn-info">Mostra Contratti</button>
    <button onclick="showHideBox()" id="closeBtn" style="display: block;" class="btn btn-info">Nascondi Contratti</button>
    <div id="infoBox" style="display: block;">
    <table class="table table-sm table-hover table-borderless">
        <caption style="caption-side: top;">Lista Contratti</caption>
        <thead class="thead-dark">
            <tr>
                <th>Contratto</th>
                <th>Codice</th>
                <th>Descrizione</th>
                <th>Ore pacchetto</th>
                <th>Stato</th>
                <th>Azienda del gruppo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($client->contracts as $contract)
                <tr>
                    <td>{{ $contract->name }}</td>
                    <td>{{ $contract->uniCode }}</td>
                    <td>{{ $contract->description }}</td>
                    <td>{{ $contract->totHours }}</td>
                    <td>{{ $contract->active }}</td>
                    <td>{{ $contract->tycoonGroupCompany->businessName }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @endif
@stop