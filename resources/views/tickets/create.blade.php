@extends('layouts.app')

@section('scripts')
    <script>
        function showHideBox() {
            const infoBox = document.getElementById("infoBox");
            const openBtn = document.getElementById("openBtn");
            const closeBtn = document.getElementById("closeBtn");

            //console.log(infoBox);

            if (infoBox.style.display === "none") {
                infoBox.style.display = "block";
                openBtn.style.display = "none";
                closeBtn.style.display = "block";
            } else {
                infoBox.style.display = "none";
                openBtn.style.display = "block";
                closeBtn.style.display = "none";
            }
        }
    </script>
    <script>
        window.addEventListener('load', (event) => {
            axios({
                method: "get",
                url: "/api/tickets",
            }).then((resp) => {
                // lista contratti
                const contracts = resp.data.contracts;
                // lista clienti
                const clients = resp.data.clients;
                // lista aziende del gruppo
                const companies = resp.data.companies;
                // lista utenti
                const users = resp.data.users;
                /* // lista dei legami tra clienti e cdc 
                const clientCDCs = resp.data.;  */

                // seleziono i campi input con il quale devo interagire
                const selectedContract = document.getElementById("contract_id");
                const cliente = document.getElementById("cliente");
                const selectUser = document.querySelector("#userOption");
                
                if(selectedContract) {
                    // mi metto in ascolto dell'evento change
                    selectedContract.addEventListener('change', (event) => {
                        // trovo l'id del contratto selezionato
                        contractID = event.target.value;
                        console.log('contractID: ' + contractID);
                        // N.B: gli array sono 0 based quindi per trovare il contratto 
                        // devo togliere 1 all'id trovato in precedenza
                        // trovo il contratto
                        const contract = contracts.find(function(contract, index) {
                            if(contract.id == contractID) {
                                return contract;
                            }
                        });
                        // trovo il cliente
                        const clientID = contract.client_id;
                        const client = clients.find(function(client, index) {
                            if(client.id == clientID) {
                                return client;
                            }
                        });
                        console.log(client);
                        cliente.value =  client.businessName;
                        // QUI CHIAMATA PER OTTENERE I CDC LEGATI AL CLIENTE
                        axios({
                            method: "get",
                            url: "/api/tickets/getCDCs",
                            params: {
                                'id' : client.id,
                            },
                        }).then((resp) => {
                            const cdcs = resp.data.cdcs;
                            console.log(cdcs);
                            const infoBox = document.getElementById("infoBox");

                            if (document.querySelectorAll("#myCdc").length > 0){
                                console.log(document.querySelectorAll("#myCdc"));
                                const nodeList = document.querySelectorAll("#myCdc");

                                for (let i = 0; i < nodeList.length; i++) {
                                    const option = nodeList[i];

                                    infoBox.removeChild(option);    
                                }
                            };

                            for (let i = 0; i < cdcs.length; i++) {
                                const cdc = cdcs[i];

                                infoBox.innerHTML += `<option id="myCdc" value="${cdc['id']}">${cdc['businessName']}</option>`;
                            }


                        });

                        // adesso ho necessità di sapere l'azienda del gruppo
                        // in quanto devo permettere di scegliere solo tra gli appartenenti a quella azienda
                        const companyID = contract.company_id;
                        const company = companies.find(function(company, index) {
                            if(company.id == companyID) {
                                return company;
                            }
                        });
                        const companyNameArray = company.businessName.split(' ');
                        const companyName = companyNameArray[0].toLowerCase();
                        
                        const usersName = [];
                        
                        for (let i = 0; i < users.length; i++) {
                            const element = users[i];

                            if (element.email.includes(companyName)) {

                                usersName.push(element.name);
                            }
                            
                        }
                        
                        if (document.querySelectorAll("#myOption").length > 0){
                            console.log(document.querySelectorAll("#myOption"));
                            const nodeList = document.querySelectorAll("#myOption");

                            for (let i = 0; i < nodeList.length; i++) {
                                const option = nodeList[i];

                                selectUser.removeChild(option);    
                            }
                        };

                        if (selectUser) {
                            for (let i = 0; i < usersName.length; i++) {
                                const userName = usersName[i];

                                selectUser.innerHTML +=  `<option id="myOption" value="${userName}">${userName}</option>`;
                            
                            }
                        };
                        
                    });
                } else {
                    console.log('contratto già selezionato');
                }
                
                
            });
        });
    </script>
@stop

@section('headers')
    <div class="mb-5">
        <h1 class="">Nuovo Ticket</h1>
        <div title="torna indietro">
            <a href="{{ url()->previous() }}" class="btn btn-info" role="button">
                <i class="bi bi-box-arrow-left"></i> 
            </a>
        </div>
    </div>
@stop

@php 
    $result = isset($contract);
    var_dump($result);
@endphp

@section('content')
    <!--parte di codice che mostra eventuali errori quando si compila il form di creazione del nuovo ticket-->
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tickets.store') }}" method="POST">
        @csrf
        <div class="form-row">
            @if(isset($contract))
                <div class="form-group col-4">
                    <label for="contract_id">Contratto</label>
                    <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                    <input type="text" value="{{ $contract->name }}" readonly class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="cliente">Cliente</label>
                    <input type="text" name="cliente" value="{{ $contract->client->businessName }}" readonly class="form-control">
                </div>
                <div class="form-group col-4">
                    <label for="openBy">Aperto</label>
                    <input type="text" name="openBy" value="{{ $activeUser->name }}" readonly class="form-control">
                </div>
            @else
                <div class="form-group col-4">
                    <label for="contract_id">Contratto</label>
                    <select name="contract_id" id="contract_id" class="form-control custom-select">
                        <option value="">Scegli il contratto su cui attivare il ticket</option>
                        @foreach($contracts as $contract)
                            <option <?php if ($contract->id == old('contract_id')) echo "selected";?> value="{{ $contract->id }}">{{ $contract->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label for="cliente">Cliente</label>
                    @if(!old('cliente'))
                    <input type="text" id="cliente" name="cliente"  readonly class="form-control">
                    @else
                    <input type="text" id="cliente" name="cliente"  value="{{ old('cliente') }}"readonly class="form-control">
                    @endif
                </div>
                <div class="form-group col-4">
                    <label for="openBy">Aperto</label>
                    <input type="text" name="openBy" value="{{ $activeUser->name }}" readonly class="form-control">
                </div>
            @endif
        </div>
        <div class="form-row">
            <div class="form-group col-4">
                <label for="performedBy">Eseguito</label>
                <select name="performedBy" class="form-control custom-select">
                    <option value="">Scegli chi ha eseguito l'intervento</option>
                    @if ($activeUser['role'] == 'admin')
                        <option value="{{ $activeUser['name'] }}">{{ $activeUser['name'] }}</option>
                    @endif
                    @foreach($users as $user)
                        <option <?php if ($user->name == old('performedBy')) echo "selected";?> value="{{ $user->name }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-4">
                <label for="workTime">Durata intervento</label>
                <input type="text" name="workTime" value="{{ old('workTime') }}" placeholder="Esempio: 1.5 (un ora e mezza) " class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="extraTime">Ore extra</label>
                @if (Auth::user()['role'] == 'admin')
                    <input type="text" name="extraTime" value="{{ old('extraTime') }}" placeholder="solo amministratori" class="form-control">
                @else
                    <input type="text" name="extraTime" value="{{ old('extraTime') }}" placeholder="0.00" readonly class="form-control">
                @endif
            </div> 
        </div>
        <div class="form-row">  
            <div class="form-group col-4">
                <label for="start_date">Data inizio intervento</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="end_date">Data fine intervento</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
            </div>
            <div class="form-group col-4">
                <label for="comments">Commento intervento</label>
                <input type="text" name="comments" value="{{ old('comments') }}" placeholder="riassumi l'intervento" class="form-control">
            </div>  
        </div>
        <div class="form-row mt-2">
            <div onclick="showHideBox()" id="openBtn" style="display: block;" class="btn btn-success form-group col-3">
                Aggiungi Centro di costo
                <i class="bi bi-arrow-bar-right"></i>
            </div>
            <div onclick="showHideBox()" id="closeBtn" style="display: none;" class="btn btn-success form-group col-3">
                <i class="bi bi-arrow-bar-left"></i>
                Chiudi
            </div>
            <div class="form-group col-3">
                <select name="cdc_id" id="infoBox" style="display: none;" class="form-control custom-select">
                    <option value="">opzionale</option>
                    @if ($result)
                        @foreach ($cdcs as $cdc)
                            <option value="{{ $cdc->id }}">{{ $cdc->businessName }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
       
        <div class="form-group">
            <button type="submit" class="btn btn-primary mt-2">INVIA</button>
        </div>
    </form>    
@stop
