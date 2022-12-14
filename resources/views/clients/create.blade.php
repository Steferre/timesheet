@extends('layouts.app')

@section('headers')
    <h1 class="mb-3">Area inserimento di una nuova azienda cliente</h1>
    <div title="lista clienti">
        <a href="{{ route('clients.index') }}" class="btn btn-primary" role="button">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
@stop    

@section('content')
    <div class="mx-auto">
        <!--parte di codice che mostra eventuali errori quando si compila il form di creazione del nuovo promoter-->
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            <div class="form-row mt-5">    
                <div class="form-group col-4">
                    <label for="businessName">Ragione Sociale</label>
                    <input type="text" name="businessName" value="{{ old('businessName') }}" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="email">Email</label>
                    <input type="text" name="email" value="{{ old('email') }}" placeholder="opzionale" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="phone">Telefono</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"  placeholder="opzionale" class="form-control">
                </div> 
            </div>
            <div class="form-row">
                <div class="form-group col-4">
                    <label for="pIva">Partita IVA</label>
                    <input type="text" name="pIva" value="{{ old('pIva') }}" placeholder="opzionale" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="address">Indirizzo</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="opzionale" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="buldingNum">Numero civico</label>
                    <input type="text" name="buldingNum" value="{{ old('buldingNum') }}" placeholder="opzionale" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="city">Citt??</label>
                    <input type="text" name="city" value="{{ old('city') }}" placeholder="opzionale" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="province">Provincia</label>
                    <input type="text" name="province" value="{{ old('province') }}" placeholder="opzionale" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="country">Stato</label>
                    <input type="text" name="country" value="{{ old('country') }}" placeholder="opzionale" class="form-control">
                </div>
                <div class="form-group col-3">
                    <label for="postalCode">Codice postale</label>
                    <input type="text" name="postalCode" value="{{ old('postalCode') }}" placeholder="opzionale" class="form-control">
                </div> 
            </div>
            @if(count($cdcs) > 0)
                <div class="form-row">
                    <div class="form-group">
                        <strong>Scegli i Centri di Costo associati all'Azienda: </strong>
                    </div>
                </div>
                @foreach($cdcs as $cdc)
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" value="{{ $cdc->id }}" name="cdc_id[]" id="{{ $cdc->id }}">
                    <label class="custom-control-label" for="{{ $cdc->id }}">{{ $cdc->businessName }}</label>
                </div>
                @endforeach
            @endif    
            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary mt-2">CREA</button>
            </div>
        </form>
    </div>    
@stop
