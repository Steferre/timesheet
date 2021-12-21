@extends('layouts.app')

@section('scripts')
    <script>
        window.addEventListener('load', (event) => {
            const d = new Date();
            const year = d.getFullYear().toString();
            //è necessario sommare 1, perchè è un array e parte da 0 = gennaio!!!
            let month = (d.getMonth() + 1).toString();  
            if (month.length == 1) {
                month = '0' + month;
            }
            let day = d.getDate().toString();
            if (day.length == 1) {
                day = '0' + day;
            }

            const today = `${year}-${month}-${day}`;

            //console.log(today);

            // intercetto il campo start date
            const startDate = document.getElementById("startDate");
            const endDate = document.getElementById("endDate");
            let selectedStartDate = null;

            startDate.addEventListener('change', (e) => {
                
                if (e.target.value <= today) {
                    alert("Attenzione, non puoi inserire come data d'inizio, una data già passata!!!");
                    e.target.value = null;
                } else {
                    return selectedStartDate = e.target.value;
                }

            });

            endDate.addEventListener('change', (e) => {

                if (e.target.value <= selectedStartDate) {
                    alert("Attenzione, non puoi inserire una data di fine, precedente o uguale alla data di apertura!!!");
                    e.target.value = null;
                }

            });

        });
    </script>
@stop

@section('headers')
    <h1>Nuovo contratto</h1>
    <div title="lista contratti">
        <a href="{{ route('contracts.index') }}" class="btn btn-primary" role="button">
            <i class="bi bi-box-arrow-left"></i>
        </a>
    </div>
@stop

@section('content')
    <!--parte di codice che mostra eventuali errori quando si compila il form di creazione del nuovo contratto-->
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contracts.store') }}" method="POST">
        @csrf
        <div class="form-row mt-5">    
            <div class="form-group col-4">
                <label for="name">Nome contratto</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="uniCode">Codice contratto</label>
                <input type="text" name="uniCode" value="{{ old('uniCode') }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="start_date">Data d'inizio</label>
                <input type="date" id="startDate" name="start_date" value="{{ old('start_date') }}" class="form-control">
            </div> 
        </div>
        <div class="form-row">
            <div class="form-group col-4">
                <label for="end_date">Data fine</label>
                <input type="date" id="endDate" name="end_date" value="{{ old('end_date') }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="description">Descrizione</label>
                <input type="text" name="description" value="{{ old('description') }}" placeholder="opzionale" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="totHours">Totale ore contratto</label>
                <input type="text" name="totHours" value="{{ old('totHours') }}" placeholder="opzionale" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-4">
                <label for="active">Stato</label>
                <select name="active" id="active" class="form-control custom-select">
                    <option value="">Scegli lo stato del contratto...</option>
                    <option value="Y">Attivo</option>
                    <option value="N">Non attivo</option>
                </select>
            </div>
            <div class="form-group col-4">
                <label for="company_id">Società del gruppo</label>
                <input type="hidden" name="company_id" value=6 readonly class="form-control">
                <input type="text" value="KeyOS srl" readonly class="form-control">
                <!-- <select name="company_id" id="company_id" class="form-control custom-select">
                    <option value="">Scegli la società del gruppo</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->businessName }}</option>
                    @endforeach
                </select> -->
            </div>
            <div class="form-group col-4">
                <label for="client_id">Azienda Cliente</label>
                <select name="client_id" id="client_id" class="form-control custom-select">
                    <option value="">Scegli l'azienda cliente</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->businessName }}</option>
                    @endforeach
                </select>
            </div> 
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary mt-2">INVIA</button>
        </div>
    </form>
@stop