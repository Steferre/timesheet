@extends('layouts.app')
@php
$getParams = $_GET;
@endphp
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
    <h1>Ticket</h1>
    <a href="{{ route('tickets.create') }}" role="button" class="btn btn-info">
        <i class="bi bi-plus-square align-middle mr-2" style="font-size: 20px;"></i>
        Nuovo Ticket
    </a>
    <!-- <div>
        <form method="POST" action="{{ route('tickets.create') }}">
            @csrf
            <button type="submit" class="btn btn-info">
                <i class="bi bi-plus-square align-middle mr-2" style="font-size: 20px;"></i>
                Nuovo Ticket
            </button>
        </form>
    </div> -->
@stop

@section('filters')
    <div>
        <form action="{{ route('tickets.index') }}" method="GET">
            @csrf

            <div class="form-row mt-5">    
                <div class="form-group col-2">
                    <label for="contractN">Nome Contratto</label>
                    @if (old('contractN'))
                        <input type="text" name="contractN" value="{{ old('contractN') }}" class="form-control" placeholder="nome contratto">
                    @else 
                        <input type="text" name="contractN" value="{{ $contractN ?? '' }}" class="form-control" placeholder="nome contratto">
                    @endif
                </div>
                <div class="form-group col-2">
                    <label for="startingDR">Ticket da ...</label>
                    @if (old('startingDR'))
                        <input type="date" name="startingDR" value="{{ old('startingDR') }}" class="form-control">
                    @else 
                        <input type="date" name="startingDR" value="{{ $startingDR ?? '' }}" class="form-control">
                    @endif
                </div>
                <div class="form-group col-2">
                    <label for="endingDR">Ticket a ...</label>
                    @if (old('endingDR'))
                        <input type="date" name="endingDR" value="{{ old('endingDR') }}" class="form-control">
                    @else 
                        <input type="date" name="endingDR" value="{{ $endingDR ?? '' }}" class="form-control">
                    @endif
                </div>
                <div class="form-group col-2">
                    <label for="searchedC">Azienda Cliente</label>
                    <select name="searchedC" class="form-control">
                        <option value="">Seleziona...</option>
                        @foreach($clients as $client)
                            @if($client->id == old('searchedC'))
                                <option value="{{ old('searchedC') }}" selected>{{ $client->businessName }}</option>
                            @else
                                <option value="{{ $client->id }}">{{ $client->businessName }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-2">
                    <label for="searchedCDC">Centro di costo</label>
                    <select name="searchedCDC" class="form-control">
                        <option value="">Seleziona...</option>
                        @foreach($cdcs as $cdc)
                            @if($cdc->id == old('searchedCDC'))
                                <option value="{{ old('searchedCDC') }}" selected>{{ $cdc->businessName }}</option>
                            @else
                                <option value="{{ $cdc->id }}">{{ $cdc->businessName }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-3">
                    <button type="submit" class="btn btn-primary">FILTRA</button>
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary ml-3" role="button">ANNULLA FILTRO</a>
                </div>
                <div class="offset-6"></div>
            </div>   
        </form>
        <div class="form-group">
            <!--FORM PER L'EXPORT-->
            <form action="{{ route('tickets.export') }}" method="GET">
                @csrf
                <input type="hidden" name="contractN" value="{{ $contractN ?? '' }}">
                <input type="hidden" name="startingDR" value="{{ $startingDR ?? '' }}">
                <input type="hidden" name="endingDR" value="{{ $endingDR ?? '' }}">
                <input type="hidden" name="searchedC" value="{{ $searchedC ?? '' }}">
                <input type="hidden" name="searchedCDC" value="{{ $searchedCDC ?? '' }}">
                <button type="submit" class="btn btn-info">Scarica dati</button>
            </form>
        </div>
    </div>
@stop

@section('content')
    <table class="table table-sm table-hover table-borderless mt-3">
        <caption style="caption-side: top;">Lista Ticket</caption>
        <thead class="thead-dark">
            <tr>
                <th>Contratto</th>
                <!-- <th>Aperto</th> -->
                <th>Eseguito</th>
                <th>Durata intervento</th>
                <th>Ore Extra Admin</th>
                <!-- <th>Data Inizio</th> -->
                <th>Data</th>
                <th>Commenti</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        @if (Session::get('warning') == null)
        <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->contract->name }}</td>
                    <!-- <td>{{ $ticket->openBy }}</td> -->
                    <td>{{ $ticket->performedBy }}</td>
                    <td>{{ $ticket->workTime }}</td>
                    <td>{{ $ticket->extraTime }}</td>
                    <!-- <td>{{ date('d-m-Y', strtotime($ticket->start_date)) }}</td> -->
                    <td>{{ date('d-m-Y', strtotime($ticket->end_date)) }}</td>
                    <!-- @if(strlen($ticket->comments) > 11)
                        <td>{{ substr($ticket->comments, 0, 11). ' ' . '...' }}</td>
                    @else
                        <td>{{ $ticket->comments }}</td>
                    @endif -->
                    <td>{{ $ticket->comments }}</td>
                    @if(Auth::user()['name'] == $ticket->performedBy || Auth::user()['role'] == 'admin')
                        <td title="Modifica">
                            <a href="{{ route('tickets.edit', $ticket->id) }}">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </td>
                        <td title="Dettaglio">
                            <a href="{{ route('tickets.show', ['id' => $ticket->id, 'getParams' => $getParams]) }}">
                                <i class="bi bi-box-arrow-right"></i>
                            </a>
                        </td>
                        <td title="Elimina">
                            <form method="POST" action="{{ route('tickets.destroy', $ticket->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="confirmDelete()" id="deleteBtn" class="border-0 bg-transparent text-danger">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    @else
                        <td title="Modifica">
                            <a href="{{ route('tickets.edit', $ticket->id) }}">
                                <i class="bi bi-pencil" disabled></i>
                            </a>
                        </td>
                        <td title="Dettaglio">
                            <a href="{{ route('tickets.show', $ticket->id) }}">
                                <i class="bi bi-box-arrow-right"></i>
                            </a>
                        </td>
                        <td title="Elimina">
                            <form method="POST" action="{{ route('tickets.destroy', $ticket->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="confirmDelete()" id="deleteBtn" class="border-0 bg-transparent text-danger">
                                    <i class="bi bi-trash" disabled></i>
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        @endif
    </table>
    {{ $tickets->appends(['contractN' => $contractN ?? "",
        'startingDR' => $startingDR ?? "",
        'endingDR' => $endingDR ?? "",
        'searchedC' => $searchedC ?? "",
        'searchedCDC' => $searchedCDC ?? ""])->links() }}
@stop