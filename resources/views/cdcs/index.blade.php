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
    <h1>Centri di costo</h1>
    <div class="mb-5">
        <a href="{{ route('cdcs.create') }}" class="btn btn-primary" role="button">
            <i class="bi bi-plus-square mr-2 align-middle" style="font-size: 20px;"></i>
            Aggiungi nuovo centro di costo
        </a>
    </div>
@stop

@section('filters')
    <div>
        <form action="{{ route('cdcs.index') }}" method="GET">
            @csrf

            <div class="form-row mt-5">    
                <div class="form-group col-3">
                    @if (old('searchedC'))
                        <input type="text" name="searchedC" value="{{ old('searchedC') }}" class="form-control" placeholder="ricerca per ragione sociale">
                    @else 
                        <input type="text" name="searchedC" value="{{ $cdc ?? '' }}" class="form-control" placeholder="ricerca per ragione sociale">
                    @endif
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary ml-3">FILTRA</button>
                </div>
                <div class="form-group">
                    <a href="{{ route('cdcs.index') }}" class="btn btn-secondary ml-3" role="button">ANNULLA FILTRO</a>
                </div>
            </div>
        </form>
    </div>
@stop

@section('content')
    <table class="table table-sm table-hover table-borderless">
        <caption style="caption-side: top;">Lista Centri di costo</caption>
        <thead class="thead-dark">
            <tr>
                <th>Ragione Sociale</th>
                <th>Num aziende clienti</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($cdcs as $cdc)
                <tr>
                    <td>{{ $cdc->businessName }}</td>
                    <td>{{ count($cdc->clients) }}</td>
                    @if(count($cdc->clients) > 0)
                        <td title="info">
                            <form method="POST" action="{{ route('cdcs.destroy', $cdc->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="deleteBtn" class="border-0 bg-transparent">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                            </form>
                        </td>
                    @else
                        <td title="elimina contratto">
                            <form method="POST" action="{{ route('cdcs.destroy', $cdc->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="confirmDelete()" id="deleteBtn" class="border-0 bg-transparent text-danger">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $cdcs->links() }}
@stop