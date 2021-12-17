@extends('layouts.app')

@section('headers')
    <h1>Area modifica azienda cliente</h1>
    <div title="lista clienti">
        <a href="{{ route('clients.index') }}" class="btn btn-primary" role="button">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
@stop 

@section('content')
    <div class="mx-auto p-5">
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
            <div class="form-row">    
                <div class="form-group col-4">
                    <label for="businessName">Ragione Sociale</label>
                    <input type="text" name="businessName" value="{{ $client->businessName }}" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="email">Email</label>
                    <input type="text" name="email" value="{{ $client->email }}" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="pIva">Partita IVA</label>
                    <input type="text" name="pIva" value="{{ $client->pIva }}" class="form-control">
                </div> 
            </div>
            <div class="form-row">
                <div class="form-group col-4">
                    <label for="address">Indirizzo</label>
                    <input type="text" name="address" value="{{ $client->address }}" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="buldingNum">Numero Civico</label>
                    <input type="text" name="buldingNum" value="{{ $client->buldingNum }}" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="city">Città</label>
                    <input type="text" name="city" value="{{ $client->city }}" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="province">Provincia</label>
                    <input type="text" name="province" value="{{ $client->province }}" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="country">Stato</label>
                    <input type="text" name="country" value="{{ $client->country }}" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="postalCode">Codice Postale</label>
                    <input type="text" name="postalCode" value="{{ $client->postalCode }}" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="phone">Telefono</label>
                    <input type="text" name="phone" value="{{ $client->phone }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary mt-2">AGGIORNA</button>
            </div>
        </form>
    </div>    
@stop
