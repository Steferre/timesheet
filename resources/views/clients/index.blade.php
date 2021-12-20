@extends('layouts.app')

@section('scripts')
<script>
    function confirmDelete() {
        const deleteBtn = document.getElementById("deleteBtn");

        let response = null;

        response = confirm("Vuoi proseguire con l'eliminazione?");

        if (!response) {
            event.preventDefault();
        }
    }
</script>
@stop

@section('headers')
    <h1>Aziende Clienti</h1>
    <div class="mb-5">
        <a href="{{ route('clients.create') }}" class="btn btn-primary" role="button">
            <i class="bi bi-plus-square mr-2 align-middle" style="font-size: 20px;"></i>
            Aggiungi nuova azienda
        </a>
    </div>
@stop

@section('filters')
    <div>
        <form action="{{ route('clients.index') }}" method="GET">
            @csrf

            <div class="form-row mt-5">    
                <div class="form-group col-3">
                    @if (old('searchedC'))
                        <input type="text" name="searchedC" value="{{ old('searchedC') }}" class="form-control" placeholder="ricerca per ragione sociale">
                    @else 
                        <input type="text" name="searchedC" value="{{ $client ?? '' }}" class="form-control" placeholder="ricerca per ragione sociale">
                    @endif
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary ml-3">FILTRA</button>
                </div>
                <div class="form-group">
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary ml-3" role="button">ANNULLA FILTRO</a>
                </div>
            </div>
        </form>
    </div>
@stop

@section('content')
    <table class="table table-sm table-hover table-borderless">
        <caption style="caption-side: top;">Lista Aziende Clienti</caption>
        <thead class="thead-dark">
            <tr>
                <th>Ragione Sociale</th>
                <th>Email</th>
                <th>pIva</th>
                <th>Telefono</th>
                <th>Contratti attivi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
                <tr>
                    <td>{{ $client->businessName }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->pIva }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>{{ count($client->contracts) }}</td>
                    <td title="Modifica dettagli azienda cliente">
                        <a href="{{ route('clients.edit', $client->id) }}">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                    </td>
                    <td title="vai al dettaglio del cliente">
                        <a href="{{ route('clients.show', $client->id) }}">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </td>
                    @if(count($client->contracts) == 0)
                    <td title="elimina cliente">
                        <form method="POST" action="{{ route('clients.destroy', $client->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="confirmDelete()" class="border-0 bg-transparent text-danger" id="deleteBtn">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $clients->links() }}
@stop