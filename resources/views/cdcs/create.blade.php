@extends('layouts.app')

@section('headers')
    <h1>Area inserimento di un nuovo centro di costo</h1>
    <div title="lista centri di costo">
        <a href="{{ route('cdcs.index') }}" class="btn btn-primary" role="button">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
@stop    

@section('content')
    <div class="mx-auto p-5">
        <!--parte di codice che mostra eventuali errori quando si compila il form di creazione del centro di costo-->
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('cdcs.store') }}" method="POST">
            @csrf
            <div class="form-row">    
                <div class="form-group col-4">
                    <label for="businessName">Ragione Sociale</label>
                    <input type="text" name="businessName" value="{{ old('businessName') }}" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary mt-2">INVIA</button>
            </div>
        </form>
    </div>    
@stop
