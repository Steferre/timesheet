@extends('layouts.app')

@section('headers')
    <div class="mb-5">
        <h1 class="">Modifica Ticket</h1>
        <div title="torna indietro">
            <a href="{{ url()->previous() }}" class="btn btn-info" role="button">
                <i class="bi bi-box-arrow-left"></i> 
            </a>
        </div>
    </div>
@stop

@section('content')
    <!--parte di codice che mostra eventuali errori quando si compila il form di modifica di un ticket-->
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="form-row">
            <div class="form-group col-4">
                <label for="contract_id">Nome Contratto</label>
                <input type="hidden" name="contract_id" value="{{ $ticket->contract->id }}">
                <input type="text" value="{{ $ticket->contract->name }}" readonly class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="workTime">ore di lavoro</label>
                <input type="text" name="workTime" value="{{ $ticket->workTime }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="extraTime">ore extra admin</label>
                @if (Auth::user()['role'] == 'admin')
                    <input type="text" name="extraTime" value="{{ $ticket->extraTime }}" class="form-control">
                @else
                    <input type="text" name="extraTime" value="{{ $ticket->extraTime }}" readonly class="form-control">
                @endif    
            </div>
        </div>
        <div class="form-row">    
            <div class="form-group col-4">
                <label for="start_date">Data inizio intervento</label>
                <input type="date" name="start_date" value="{{ $ticket->start_date }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="end_date">Data fine intervento</label>
                <input type="date" name="end_date" value="{{ $ticket->end_date }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="comments">Commento intervento</label>
                <input type="text" name="comments" value="{{ $ticket->comments }}" class="form-control">
            </div> 
        </div>
        @if(count($cdcs) > 0)
        <div class="form-row">
            <div class="mr-3">Centro di costo associato: </div>
            @foreach($cdcs as $cdc)
                <div class="custom-control custom-radio custom-control-inline">
                    <input <?php if ($ticket->cdc_id == $cdc->id) echo "checked";?> type="radio" class="custom-control-input" value="{{ $cdc->id }}" name="cdc_id" id="{{ $cdc->id }}">
                    <label class="custom-control-label" for="{{ $cdc->id }}">{{ $cdc->businessName }}</label>
                </div>
            @endforeach
        </div>
        @endif
        <div class="form-group">
            <button type="submit" class="btn btn-primary mt-2">MODIFICA</button>
        </div>
    </form>   
@stop
