@extends('layouts.app')

@section('headers')
    <h1 class="mb-3">Area modifica azienda cliente</h1>
    <div title="lista clienti">
        <a href="{{ route('clients.index') }}" class="btn btn-primary" role="button">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
@stop 

@section('content')
    <div class="mx-auto">
        <!--parte di codice che mostra eventuali errori quando si compila il form di modifica del cliente-->
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    
        <form action="{{ route('clients.update', $client->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-row mt-5">    
                <div class="form-group col-4">
                    <label for="businessName">Ragione Sociale</label>
                    <input type="text" name="businessName" value="{{ $client->businessName }}" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="email">Email</label>
                    <input type="text" name="email" value="{{ $client->email }}" class="form-control" placeholder="opzionale">
                </div>
                <div class="form-group col-4">
                    <label for="pIva">Partita IVA</label>
                    <input type="text" name="pIva" value="{{ $client->pIva }}" class="form-control" placeholder="opzionale">
                </div> 
            </div>
            <div class="form-row">
                <div class="form-group col-4">
                    <label for="address">Indirizzo</label>
                    <input type="text" name="address" value="{{ $client->address }}" class="form-control" placeholder="opzionale">
                </div>
                <div class="form-group col-4">
                    <label for="buldingNum">Numero Civico</label>
                    <input type="text" name="buldingNum" value="{{ $client->buldingNum }}" class="form-control" placeholder="opzionale">
                </div>
                <div class="form-group col-4">
                    <label for="city">Città</label>
                    <input type="text" name="city" value="{{ $client->city }}" class="form-control" placeholder="opzionale">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="province">Provincia</label>
                    <input type="text" name="province" value="{{ $client->province }}" class="form-control" placeholder="opzionale">
                </div>
                <div class="form-group col-3">
                    <label for="country">Stato</label>
                    <input type="text" name="country" value="{{ $client->country }}" class="form-control" placeholder="opzionale">
                </div>
                <div class="form-group col-3">
                    <label for="postalCode">Codice Postale</label>
                    <input type="text" name="postalCode" value="{{ $client->postalCode }}" class="form-control" placeholder="opzionale">
                </div>
                <div class="form-group col-3">
                    <label for="phone">Telefono</label>
                    <input type="text" name="phone" value="{{ $client->phone }}" class="form-control" placeholder="opzionale">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <strong>Scegli i Centri di Costo associati all'Azienda: </strong>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    @foreach($onlyCdcs as $singleCdc)
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input <?php foreach($cdcs as $cdc) if ($singleCdc->id == $cdc->id) echo "checked";?> type="checkbox" class="custom-control-input" value="{{ $singleCdc->id }}" name="cdc_id[]" id="{{ $singleCdc->id }}">
                            <label class="custom-control-label" for="{{ $singleCdc->id }}">{{ $singleCdc->businessName }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary mt-2">AGGIORNA</button>
            </div>
        </form>
    </div>    
@stop
