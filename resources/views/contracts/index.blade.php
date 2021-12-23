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
    <h1>Contratti</h1>
    @if ($loggedUser['role'] == 'admin')
    <div class="d-flex">
        <div class="mr-3">
            <a href="{{ route('contracts.create') }}" class="btn btn-primary" role="button">Crea nuovo contratto</a>
        </div>
    </div>
    @endif
@stop

@section('filters')
    <div>
        <form action="{{ route('contracts.index') }}" method="GET">
            @csrf

            <div class="form-row mt-5">
                <div class="form-group col-3">
                    @php
                    $result = isset($searchedC);
                    @endphp
                    <!-- <label for="searchedC"></label> -->
                    <select name="searchedC" class="form-control">
                        <option value="">Seleziona l'Azienda Cliente</option>
                        @foreach($clients as $client)
                            @if(!$result)
                                <option value="{{ $client->id }}">{{ $client->businessName }}</option>
                            @else
                                <option <?php if ($client->id == $searchedC) echo "selected";?> value="{{ $client->id }}">{{ $client->businessName }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-3">
                    @php
                    $result = isset($contractS);
                    @endphp
                    <!-- <label for="searchedC"></label> -->
                    <select name="contractS" class="form-control">
                        @if(!$result)
                            <option value="">Stato contratto</option>
                            <option value="Y">Attivo</option>
                            <option value="N">Non Attivo</option>
                        @else
                            <option <?php if ($contractS == null) echo "selected";?> value="">Stato contratto</option>
                            <option <?php if ($contractS == "y") echo "selected";?> value="Y">Attivo</option>
                            <option <?php if ($contractS == "n") echo "selected";?> value="N">Non Attivo</option>
                        @endif    
                    </select>
                </div>
                <div class="form-group col-3">
                    @php
                        $result = isset($contractT);
                    @endphp
                    <select name="contractT" class="form-control">
                        <option value="">Seleziona il tipo di contratto</option>
                        @foreach($contracts as $contract)
                            @if(!$result)
                                @if($contract->type == 'increase')
                                    <option value="{{ $contract->type }}">Accumulo</option>
                                @else
                                    <option value="{{ $contract->type }}">Decremento</option>
                                @endif
                            @else
                                <option <?php if ($contract->type == $contractT) echo "selected";?> value="{{ $contract->type }}">{{ $contract->type }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-3">
                    <button type="submit" class="btn btn-primary ml-3">FILTRA</button>
                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary ml-3" role="button">ANNULLA FILTRO</a>
                </div>
                <div class="offset-1"></div>
                <div class="form-group col-2">
                </div>
            </div>
        </form>
        <!--FORM PER L'EXPORT-->
        <form action="{{ route('contracts.exportCON') }}" method="GET">
            @csrf
            <input type="hidden" name="searchedC" value="{{ $searchedC ?? '' }}">
            @if (Auth::user()['role'] == 'admin')
                <input type="hidden" name="searchedS" value="{{ $searchedS ?? '' }}">
            @endif
            <button type="submit" class="btn btn-info">Scarica dati</button>
        </form>
    </div>
@stop

@section('content')
    <table class="table table-sm table-hover table-borderless">
        <caption style="caption-side: top;">Lista Contratti</caption>
        <thead class="thead-dark">
            <tr>
                <th>Nome</th>
                <th>Codice</th>
                <th>Data Inizio</th>
                <th>Tipologia contratto</th>
                <th>Ore Totali</th>
                <th>Ore utilizzate (user+admin)</th>
                <th>Avanzamento (%)</th>
                <th>Ticket</th>
                <th>Attivo</th>
                <th>Azienda Cliente</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $contract)
                <tr>
                    <td>{{ $contract->name }}</td>
                    <td>{{ $contract->uniCode }}</td>
                    <td>{{ $contract->start_date }}</td>
                    <td>{{ $contract->type }}</td>
                    @if ($contract->totHours != null)
                    <td>{{ $contract->totHours }}</td>
                    <td>{{ $contract->hours }}</td>
                    @php
                    $advancement = ((intval($contract->hours)/intval($contract->totHours))*100);
                    $result = round($advancement);
                    @endphp
                    <td class="p-2">
                        <div class="py-2" title="{{$result}}">
                            @if ($result <= 50)
                            <div style="width: {{$result}}%; height: 10px; background-color: #00b500;"></div>
                            @elseif ($result >= 51 && $result <= 84)
                            <div style="width: {{$result}}%; height: 10px; background-color: #ffcb00;"></div>
                            @elseif ($result >= 85 && $result <= 99)
                            <div style="width: {{$result}}%; height: 10px; background-color: #ff7500;"></div>
                            @else
                            <div style="width: {{$result}}%; height: 10px; background-color: #c00000;"></div>
                            @endif
                        </div>
                    </td>
                    @else
                    <td>Nessun limite</td>
                    <td>{{ $contract->hours }}</td>
                    <td>---</td>
                    @endif
                    <td>{{ count($contract->tickets) }}</td>
                    <td>{{ $contract->active }}</td>
                    <td>{{ $contract->client->businessName }}</td>
                    @if($loggedUser['role'] == 'admin')
                        <td title="Modifica contratto">
                            <a href="{{ route('contracts.edit', $contract->id) }}">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                        </td>
                    @else
                        <td title="Modifica contratto">
                            <a href="{{ route('contracts.edit', $contract->id) }}" disabled>
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    @endif
                    <td title="Vai al dettaglio del contratto">
                        <a href="{{ route('contracts.show', $contract->id) }}">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </td>
                    @if($loggedUser['role'] == 'admin')
                        @if(count($contract->tickets) > 0)
                        <td title="info">
                            <form method="POST" action="{{ route('contracts.destroy', $contract->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="deleteBtn" class="border-0 bg-transparent">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                            </form>
                        </td>
                        @else
                        <td title="elimina contratto">
                            <form method="POST" action="{{ route('contracts.destroy', $contract->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="confirmDelete()" id="deleteBtn" class="border-0 bg-transparent text-danger">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    @endif    
                    <td title="aggiungi Ticket">
                        <a href="{{ route('tickets.create', ['slug' => $contract->slug]) }}" role="button" class="border-0 bg-transparent">
                            <i class="bi bi-plus-square"></i>
                        </a>
                        <!-- <form method="POST" action="{{ route('tickets.create') }}">
                            @csrf
                            <input type="hidden" name="contractId" value="{{ $contract->id }}">
                            <button type="submit" class="border-0 bg-transparent">
                                <i class="bi bi-plus-square"></i>
                            </button>
                        </form> -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $contracts->links() }}
@stop