@extends('layouts.app')

@section('headers')
    <h1>Modifica contratto</h1>
    <div title="lista contratti">
        <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
@stop

@section('content')
    <!--parte di codice che mostra eventuali errori quando si compila il form di modifica del contratto-->
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contracts.update', $contract->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="form-row mt-5">    
            <div class="form-group col-4">
                <label for="name">Nome contratto</label>
                <input type="text" name="name" value="{{ $contract->name }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="uniCode">Codice contratto</label>
                <input type="text" name="uniCode" value="{{ $contract->uniCode }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="client_id">Azienda Cliente</label>
                <input type="hidden" name="client_id" value="{{ $contract->client_id }}">
                <input type="text" readonly class="form-control" value="{{ $contract->client->businessName }}">
            </div> 
        </div>
        <div class="form-row">
            <div class="form-group col-4">
                <label for="start_date">Data d'inizio</label>
                <input type="date" name="start_date" value="{{ $contract->start_date }}" class="form-control">
            </div> 
            <div class="form-group col-4">
                <label for="end_date">Data fine</label>
                <input type="date" name="end_date" value="{{ $contract->end_date }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="active">Stato</label>
                <select name="active" id="active" class="form-control custom-select">
                    @if ($contract->active == 'Y')
                    <option value="Y" selected>Attivo</option>
                    <option value="N">Non attivo</option>
                    @elseif ($contract->active == 'N')
                    <option value="Y">Attivo</option>
                    <option value="N" selected>Non attivo</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="form-row">
            <!--CONFIGURAZIONE con campo tipologia contratto non modificabile-->
            <div class="form-group col-4">
                <label for="type">Tipologia contratto</label>
                @if ($contract->type == 'increase')
                    <input type="hidden" name="type" value="{{ $contract->type }}">
                    <input type="text" value="Accumulo monte ore" readonly class="form-control">
                @elseif ($contract->type == 'decrease')
                    <input type="hidden" name="type" value="{{ $contract->type }}">
                    <input type="text" value="Decremento monte ore" readonly class="form-control">
                @endif    
            </div>
            <!-- <div class="form-group col-4">
                <label for="type">Tipologia contratto</label>
                <select name="type" id="type" class="form-control custom-select">
                    @if ($contract->type == 'increase')
                    <option value="increase" selected>Accumulo monte ore</option>
                    <option value="decrease">Decremento monte ore</option>
                    @elseif ($contract->type == 'decrease')
                    <option value="increase">Accumulo monte ore</option>
                    <option value="decrease" selected>Decremento monte ore</option>
                    @endif
                </select>
            </div> -->
            <div class="form-group col-4">
                <label for="totHours">Totale ore contratto</label>
                <input type="text" name="totHours" value="{{ $contract->totHours }}" placeholder="opzionale" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="description">Descrizione</label>
                <input type="text" name="description" value="{{ $contract->description }}" placeholder="opzionale" class="form-control">
            </div>
        </div>
        <input type="hidden" name="company_id" value="{{ $contract->company_id }}">
        <div class="form-group">
            <button type="submit" class="btn btn-primary mt-2">AGGIORNA</button>
        </div>
    </form>   
@stop