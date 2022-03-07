<?php

namespace App\Http\Controllers\Auth;

use App\Models\Contract;
use App\Models\Cdc;
use App\Models\Client;
use App\Models\TycoonGroupCompany;
use App\Models\Ticket;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedUser = Auth::user();
        $users = User::all();

        $dff = $request->only([
            'contractN',
            'startingDR',
            'endingDR',
            'searchedC',
            'searchedCDC',
            'contractStatus'
        ]);

        if ($loggedUser['role'] == 'admin'){

            $clients = Client::all();
            $cdcs = Cdc::all();

            if (count($dff) == 0) {
                // filtri non settati
                $tickets = Ticket::join('contracts', 'tickets.contract_id', '=', 'contracts.id')
                            ->select('tickets.id','tickets.start_date as tOpenDate','tickets.end_date as tCloseDate','tickets.workTime','tickets.extraTime',
                            'tickets.comments','tickets.performedBy','tickets.openBy','tickets.contract_id','tickets.cdc_id',
                            'contracts.name','contracts.start_date as cStartDate','contracts.end_date as cEndDate','contracts.description',
                            'contracts.totHours','contracts.active','contracts.client_id','contracts.type')
                            ->orderBy('tickets.end_date', 'desc')
                            ->paginate(30);

                /* foreach ($tickets as $ticket) {
                    echo '<pre>';
                    var_dump($ticket->);
                    echo '</pre>';
                }*/
                /* echo '<pre>';
                var_dump($tickets);
                echo '</pre>';
                die();  */
                return view('tickets.index', [
                    'tickets' => $tickets,
                    'users' => $users,
                    'clients' => $clients,
                    'cdcs' => $cdcs,
                ]);

            } else if (count($dff) > 0) {

                /* echo '<pre>';
                var_dump($dff);
                echo '</pre>';
                die(); */

                // filtri attivati endingDR
                $contractN = isset($dff['contractN']) ? $dff['contractN'] : null;
                $startingDR = isset($dff['startingDR']) ? $dff['startingDR'] : null;
                $endingDR = isset($dff['endingDR']) ? $dff['endingDR'] : null;
                $searchedC = isset($dff['searchedC']) ? $dff['searchedC'] : null;
                $searchedCDC = isset($dff['searchedCDC']) ? $dff['searchedCDC'] : null;
                $contractStatus = isset($dff['contractStatus']) ? $dff['contractStatus'] : null;
                
                $tickets = Ticket::join('contracts', 'tickets.contract_id', '=', 'contracts.id')
                            ->select('tickets.id','tickets.start_date as tOpenDate','tickets.end_date as tCloseDate','tickets.workTime','tickets.extraTime',
                            'tickets.comments','tickets.performedBy','tickets.openBy','tickets.contract_id','tickets.cdc_id',
                            'contracts.name','contracts.start_date as cStartDate','contracts.end_date as cEndDate','contracts.description',
                            'contracts.totHours','contracts.active','contracts.client_id','contracts.type');

                if ($startingDR) {
                    $tickets = $tickets->where('tickets.end_date', '>=', $startingDR);
                }
                if ($endingDR) {
                    $tickets = $tickets->where('tickets.end_date', '<=', $endingDR);
                }
                if ($contractN) {
                    $tickets = $tickets->where('contracts.name', 'like', '%'.$contractN.'%');
                }
                if ($searchedC) {
                    $tickets = $tickets->where('contracts.client_id', $searchedC);
                }
                if ($searchedCDC) {
                    $tickets = $tickets->where('tickets.cdc_id', $searchedCDC);
                }
                if ($contractStatus) {
                    $tickets = $tickets->where('contracts.active', $contractStatus);
                }

                $tickets = $tickets->orderBy('tickets.end_date', 'desc');
                $tickets = $tickets->paginate(30);

                /* echo '<pre>';
                var_dump($tickets);
                echo '</pre>';
                die();  */

                if (count($tickets) > 0) {

                    return view('tickets.index', [
                        'tickets' => $tickets,
                        'users' => $users,
                        'clients' => $clients,
                        'contractN' => $contractN,
                        'startingDR' => $startingDR,
                        'endingDR' => $endingDR,
                        'searchedC' => $searchedC,
                        'searchedCDC' => $searchedCDC,
                        'contractStatus' => $contractStatus,
                        'cdcs' => $cdcs,
                    ]);

                } else {
                    // la ricerca non ha fornito risultati, perchè uno o più campi non sono corretti
                    // quindi nella view non dovrò mostrare risultati
                    /* echo '<pre>';
                    var_dump($request->input());
                    echo '</pre>';
                    die();  */
                    return back()->with("warning", "La ricerca NON ha prodotto risultati, provare a cambiare i paramentri inseriti!")->withInput($request->input());
                }

            }

        } else { // sono un user normale
            // posso vedere solo i ticket che sono aperti dalla mia azienda
            $emailPath = explode('@', $loggedUser['email']);
            $company = explode('.', $emailPath[1]);
            $companyName = $company[0]; // esempio: keyos

            // preparo la query per ottenere la lista delle aziende clienti della società del gruppo
            $clients = Client::join('contracts', 'contracts.client_id', '=', 'clients.id')
                            ->join('tycoon_group_companies', 'contracts.company_id', '=', 'tycoon_group_companies.id')
                            ->select('clients.id', 'clients.businessName')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%')
                            ->get();

            // preparo la query per ottenere la lista dei centri di costo legati a contratti della società del gruppo
            $cdcs = Ticket::join('contracts', 'tickets.contract_id', '=', 'contracts.id')
                            ->join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                            ->join('cdcs', 'cdcs.id', '=', 'tickets.cdc_id')
                            ->select('cdcs.id', 'cdcs.businessName')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%')
                            ->groupBy('cdcs.id', 'cdcs.businessName')
                            ->get();

            // preparo la query di base per ottenere i ticket
            $tickets = Ticket::join('contracts', 'tickets.contract_id', '=', 'contracts.id')
                            ->join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                            ->select('tickets.id','tickets.start_date as tOpenDate','tickets.end_date as tCloseDate','tickets.workTime','tickets.extraTime',
                            'tickets.comments','tickets.performedBy','tickets.openBy','tickets.contract_id','tickets.cdc_id',
                            'contracts.name','contracts.start_date as cStartDate','contracts.end_date as cEndDate','contracts.description',
                            'contracts.totHours','contracts.active','contracts.client_id','contracts.type')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%');
                
            if (count($dff) == 0) {
                // ritorno tutti i ticket perchè nn ho filtri attivi
                $tickets = $tickets->orderBy('tickets.end_date', 'desc');
                $tickets = $tickets->paginate(30);

                /* echo '<pre>';
                var_dump($clients);
                echo '</pre>';
                die(); */


                return view('tickets.index', [
                    'tickets' => $tickets,
                    'users' => $users,
                    'clients' => $clients,
                    'cdcs' => $cdcs,
                ]);

            } else {
                // ci sono i filtri
                $contractN = isset($dff['contractN']) ? $dff['contractN'] : null;
                $startingDR = isset($dff['startingDR']) ? $dff['startingDR'] : null;
                $endingDR = isset($dff['endingDR']) ? $dff['endingDR'] : null;
                $searchedC = isset($dff['searchedC']) ? $dff['searchedC'] : null;
                $searchedCDC = isset($dff['searchedCDC']) ? $dff['searchedCDC'] : null;
                $contractStatus = isset($dff['contractStatus']) ? $dff['contractStatus'] : null;
               
                if ($startingDR) {
                    $tickets = $tickets->where('tickets.end_date', '>=', $startingDR);
                }
                if ($endingDR) {
                    $tickets = $tickets->where('tickets.end_date', '<=', $endingDR);
                }
                if ($contractN) {
                    $tickets = $tickets->where('contracts.name', 'like', '%'.$contractN.'%');
                }
                if ($searchedC) {
                    $tickets = $tickets->where('contracts.client_id', $searchedC);
                }
                if ($searchedCDC) {
                    $tickets = $tickets->where('tickets.cdc_id', $searchedCDC);
                }
                if ($contractStatus) {
                    $tickets = $tickets->where('contracts.active', $contractStatus);
                }

                $tickets = $tickets->orderBy('tickets.end_date', 'desc');
                $tickets = $tickets->paginate(30);

                if (count($tickets) > 0) {

                    return view('tickets.index', [
                        'tickets' => $tickets,
                        'users' => $users,
                        'clients' => $clients,
                        'contractN' => $contractN,
                        'startingDR' => $startingDR,
                        'endingDR' => $endingDR,
                        'searchedC' => $searchedC,
                        'searchedCDC' => $searchedCDC,
                        'contractStatus' => $contractStatus,
                        'cdcs' => $cdcs,
                    ]);

                } else {
                    // la ricerca non ha fornito risultati, perchè uno o più campi non sono corretti
                    return back()->with("warning", "La ricerca NON ha prodotto risultati, provare a cambiare i paramentri inseriti!")->withInput($request->input());
                }

            }

        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();

        $activeUser = Auth::user();

        $emailArray = explode('@', $activeUser['email']);
        $emailPathArray = explode('.', $emailArray[1]);
        $activeUserCompanyName = $emailPathArray[0];

        // trovo la data di odierna, per passarla alla view ed impostarla come default
        $today = date('Y-m-d');

        // recupero gli utenti da passare alla view
        $users = User::all();
        
        // posso arrivare alla pagina di creazione di un ticket in due modi
        // direttamente dal contratto verificando se sia presente lo slug (codice univoco)
        if (isset($data['slug'])) {
            // arrivo direttamente (il contratto è già selezionato)
            $contract = Contract::where('slug', $data['slug'])->first();

            //trovo anche il cliente per risalire ai cdc correlati
            $client = $contract->client;
            $cdcs = $client->cdcs()->where('clientID', $client['id'])->get();
            
            // prima di dispensare la view che permette la creazione del ticket
            // verificare che il contratto sia attivo
            if ($contract['active'] == 'N') {

                return back()->with("warning", "IMPOSSIBILE aggiungere ticket, in quanto il contratto NON è attivo!!!");

            }

            // potrebbe non servire più
            $companyName = strtolower(explode(' ', $contract->tycoonGroupCompany->businessName)[0]);

            // se esiste il contratto
            if(isset($contract)) {
                return view('tickets.create', [
                    'data' => $data,
                    'contract' => $contract,
                    'activeUser' => $activeUser,
                    'users' => $users,
                    'cdcs' => $cdcs,
                    'today' => $today
                ]);
            } else {
                return abort(404);
            }
            

        } else { // caso in cui arrivo alla creazione del ticket dal pulsante apposito
            // in questo caso devo scegliere io su che contratto aprire il ticket
            if (count($_GET) == 0 && Auth::user()['role'] == 'admin') {
                // passo la lista di tutti i contratti attivi
                $contracts = Contract::where('active', 'Y')->get();

                return view('tickets.create', [
                    'contracts' => $contracts,
                    'activeUser' => $activeUser,
                    'users' => $users,
                    'today' => $today
                    //'cdcs' => $cdcs,
                ]);
            } else if (count($_GET) == 0 && Auth::user()['role'] == 'user') {
                // devo far scegliere solo tra i contratti aperti dall'azienda di appartenenza dell'user e che sono attivi

                $contracts = Contract::join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                                        ->select('contracts.*')
                                        ->where('tycoon_group_companies.businessName', 'like', '%'. $activeUserCompanyName .'%')
                                        ->where('contracts.active', 'Y')
                                        ->get();

                return view('tickets.create', [
                    'contracts' => $contracts,
                    'activeUser' => $activeUser,
                    'users' => $users,
                    'today' => $today
                    //'cdcs' => $cdcs,
                ]);

            } else {

                return abort(404);
            }
        }
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* echo '<pre>';
        var_dump($request->input());
        echo '</pre>'; */
        $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'workTime' => 'nullable|numeric',
            'comments' => 'required',
            'performedBy' => 'required',
            'contract_id' => 'required',
            'openBy' => 'required',
            'cdc_id' => 'nullable',
            'extraTime' => 'nullable|numeric',
        ]);

        $data = $request->all();
        // trovo la data odierna, mi servirà in alcuni controlli
        $today = date('Y-m-d');

        if ($data['workTime'] == null) {
            $data['workTime'] = 0;
        }

        if ($data['extraTime'] == null) {
            $data['extraTime'] = 0;
        }

        $contract = Contract::findOrFail($data['contract_id']);

        $contract->hours = DB::table('tickets')
                            ->where('tickets.contract_id', $contract->id)
                            ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
        // recupero l'azienda cliente
        $client = $contract->client;

        // controlliamo se l'azienda cliente è già presente nella lista dei centri di costo
        $cdcRecordExist = Cdc::where('cdcs.businessName', 'like', '%'. $client->businessName .'%')
                            ->count('cdcs.businessName');
                        
        // se non è presente la inserisco
        if ($cdcRecordExist == 0) {
            // l'azienda cliente non è registrata come centro di costo
            // a questo punto la inserisco
            $cdc = new Cdc();
                        
            $cdc['businessName'] = $client;
                        
            $cdc->save();
        }

        unset($data['cliente']);

        $newCDC = Cdc::where('cdcs.businessName', 'like', '%'. $client->businessName .'%')
                    ->select('cdcs.id')->first();
        // controllo che la data di apertura del ticket sia uguale o successiva a quella di apertura del contratto
        // in caso contrario blocco tutto
        if (($data['start_date'] < $contract['start_date'])) {
            return back()->with('error', 'ERRORE, la data di apertura del ticket non può essere precedente alla data di apertura del contratto!!!')->withInput($request->input());
        }
        // per la data di chiusura devo anche verificare se esiste oppure è nulla
        // in quanto in base alla tipologia di contratto potrebbe non esserci
        // controllo che la data di chiusura del ticket sia al massimo uguale a quella di chiusura del contratto
        // oppure che sia precedente, in caso contrario blocco tutto
        if ($contract['end_date'] !== null) {
            if (($data['end_date'] > $contract['end_date'])) {
                echo 'ERRORE, data di chiusura del ticket successiva alla data di chiusura del contratto!!!';
            }
        }
        
        // preparo il ticket, lo salvo solo se il controllo delle ore residue risulta positivo
        $ticket = new Ticket();

        $ticket['contract_id'] = $data['contract_id'];
        $ticket['workTime'] = $data['workTime'];
        $ticket['extraTime'] = $data['extraTime'];
        $ticket['comments'] = $data['comments'];
        $ticket['start_date'] = $data['start_date'];
        $ticket['end_date'] = $data['end_date'];
        $ticket['performedBy'] = $data['performedBy'];
        $ticket['openBy'] = $data['openBy'];
        if ($data['cdc_id'] == null) {

            $ticket['cdc_id'] = $newCDC['id'];

        } else {

            $ticket['cdc_id'] = $data['cdc_id'];

        }

        if ($contract['type'] == 'decrease') {
            // ho bisogno anche delle ore già utilizzate per questo contratto
            // in modo da poter controllare se con l'aggiunta di questo ticket 
            // il monte ore totale del contratto viene raggiunto o superato

            // devo controllare che le ore che sto aggiungendo non facciano superare il limite del contratto
            $x = $contract->totHours;
            $y = $contract->hours;
            $z = ($data['workTime'] + $data['extraTime']);

            $lastTenPercHours = ($x * 10)/100;

            $availableHours = $x - $y;

            $result = $x - ($y + $z);

            // controlli sulle ore rimanenti di un contratto                    
            if ($result < 0) {
                // significa che le ore del contratto sono finite
                // e che con l'aggiunta di questo ticket si andrebbe oltre
                return back()->with("error", "Attenzione, le ore aggiunte superano quelle rimaste a disposizione, per il seguente contratto! Ore rimaste " . $availableHours)
                            ->withInput($request->input());

            } else if ($lastTenPercHours > $result && $result > 0) {
                // siamo nell'ultimo 10% di ore disponibili
                $ticket->save();

                /* if ($data['cdc_id'] == null) {

                    $client->cdcs()->attach($newCDC['id']);
        
                } else {
        
                    $client->cdcs()->attach($data['cdc_id']);
        
                } */

                return redirect('tickets')->with("warning", "ATTENZIONE!!! " . " " . $contract->name . " " . "Percentuale di ore rimaste disponibili < 10%");

            } else if ($result == 0) {
                // il contratto ha esaurito le ore disponibili
                // dopo la creazione di questo ticket 
                // è necessario chiudere il contratto
                $ticket->save();

                // devo disattivare il contratto
                unset($contract['hours']);
                $contract->active = 'N';
                $contract->save();

                // devo sempre salvare la relazione nella tabella pivot
                /* if ($data['cdc_id'] == null) {

                    $client->cdcs()->attach($newCDC['id']);
        
                } else {
        
                    $client->cdcs()->attach($data['cdc_id']);
        
                } */

                return redirect('tickets')->with("warning", "ATTENZIONE!!! " . "Le ore disponibili per il contratto: " . $contract->name . " " . "sono terminate! Il contratto è quindi stato chiuso");

            } else { // non ci sono particolari messaggi da mostrare, il ticket viene creato regolarmente

                $ticket->save();

                /* if ($data['cdc_id'] == null) {

                    $client->cdcs()->attach($newCDC['id']);
        
                } else {
        
                    $client->cdcs()->attach($data['cdc_id']);
        
                } */

                return redirect()->route('tickets.index');
            }

        } else {// contratto ACCUMULO ORE

            // le ore vengono sommate, tenendo in considerazione sia le ore dell'user sia quelle extra dell'admin
            $inputHours = ($data['workTime'] + $data['extraTime']);
            $usedHours = ($contract->hours + $inputHours);
            // possiamo tenere questo parametro come eventuale verifica per emettere un avviso superate le x ore di tickets caricati
            // echo $usedHours;
            // die();

            // presenta una data di chiusura del contratto
            // trovo quando mancano due settimane alla data di chiusura
            $endDate = $contract['end_date'];
            /* echo 'data fine contratto: ' . $endDate;
            echo '<pre>'; */
            // adesso tolgo 15 giorni alla data di fine, per avere una data di controllo per mandare un messaggio
            // che segnala che ci si stà avvicinando alla fine del contratto stipulato
            $date = date_create($endDate);
            $warningDate = date_format(date_sub($date, date_interval_create_from_date_string('15 days')), 'Y-m-d'); 
            /* echo 'data di allerta: ' . $warningDate;
            echo '<pre>';
            echo 'oggi: ' . $today;
            die(); */

            if ($today > $warningDate && $endDate > $today) {
                // siamo all'interno degli ultimi 15 gg di contratto
                $ticket->save();

                return redirect('tickets')->with("warning", "ATTENZIONE!!! " . " " . $contract->name . " " . "Mancano meno di 10 giorni lavorativi alla data di chiusura del contratto!!");
                
            } else {
                // ancora mancano più di 15 giorni alla fine del contratto salvo solo il ticket senza segnalare nulla
                $ticket->save();

                return redirect()->route('tickets.index');

            }
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data =  $request->all();
        $ticket = Ticket::findOrFail($id);
 
        return view('tickets.show', ['ticket' => $ticket, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);

        $contract = $ticket->contract;

        // trovo l'azienda cliente legata al contratto sul quale è stato aperto i ticket in modifica
        $client = $contract->client;
        
        // devo trovare i cdc associati all'azienda del ticket aperto
        $cdcs = $client->cdcs()->where('clientID', $client->id)->get();

        if ($ticket->performedBy == Auth::user()['name'] || Auth::user()['role'] == 'admin') {

            return view('tickets.edit', ['ticket' => $ticket, 'cdcs' => $cdcs]);

        } else {

            return back()->with("warning", "Il ticket scelto non può essere modificato, in quanto non sei tu l'autore dell'intervento");

        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'workTime' => 'required|numeric',
            'extraTime' => 'required|numeric',
            'comments' => 'required',
            'contract_id' => 'required',
            'cdc_id' => 'nullable'
        ]);

        $data = $request->all();

        $ticket = Ticket::findOrFail($id);

        // ore segnate nel ticket che sto modificando (sia quelle user sia quelle admin)
        $savedTicketHours = ($ticket->workTime) + ($ticket->extraTime);

        $contract = $ticket->contract;

        // trovo l'azienda cliente legata al contratto sul quale è stato aperto i ticket in modifica
        $client = $contract->client;
        
        // devo trovare i cdc associati all'azienda del ticket aperto
        $cdcs = $client->cdcs()->where('clientID', $client->id)->get();

        /* echo '<pre>';
        var_dump($data);
        echo '</pre>';
        die(); */

        // ore totali già usate
        // N.B. in queste ore ci sono anche le savedTicketHours
        $contract->hours = DB::table('tickets')
                                    ->where('tickets.contract_id', $contract->id)
                                    ->sum(DB::raw('tickets.workTime + tickets.extraTime'));

        // controllo che la data di apertura del ticket sia uguale o successiva a quella di apertura del contratto
        // in caso contrario blocco tutto
        if (($data['start_date'] < $contract['start_date'])) {
            return back()->with('error', 'ERRORE, la data di apertura del ticket non può essere precedente alla data di apertura del contratto!!!')->withInput($request->input());
        }
        // per la data di chiusura devo anche verificare se esiste oppure è nulla
        // in quanto in base alla tipologia di contratto potrebbe non esserci
        // controllo che la data di chiusura del ticket sia al massimo uguale a quella di chiusura del contratto
        // oppure che sia precedente, in caso contrario blocco tutto
        if ($contract['end_date'] !== null) {
            if (($data['end_date'] > $contract['end_date'])) {
                return back()->with('error', 'ERRORE, data di chiusura del ticket successiva alla data di chiusura del contratto!!!')->withInput($request->input());
            }
        }
        
        // nuove ore inserite 
        $modifiedTicketHours = ($data['workTime']) + ($data['extraTime']);

        $diffHours = $modifiedTicketHours - $savedTicketHours;
        
        // prima di effettuare l'update, devo accertarmi che le ore aggiunte non sforino il limite delle ore totali del contratto
        // quindi devo trovare la differenza tra le ore già presenti nel ticket e quelle dell'aggiornamento
        // e valutare se sono congrue con quanto detto sopra
        // ho bisogno anche di sapere quante ore mancano alla fine del contratto
    
        //PROBABILE PUNTO DI SEPARAZIONE TRA TIPO DI CONTRATTO
        if ($contract['type'] == 'decrease') {



            /* echo '<pre>';
            var_dump($ticket->contract);
            echo '</pre>';
            die(); */




            $remainingHours = $contract->totHours - $contract->hours;

            if ($remainingHours > $diffHours) {
                $newRemainingHours = $remainingHours - $diffHours;
            } else {
                $newRemainingHours = $remainingHours;
            }

            $lastTenPercHours = ($contract->totHours * 10)/100;

            if ($diffHours < 0) {
                // in questo caso sto togliendo ore
                // perchè ne avevo segnate troppe
                
                $ticket->update($data);
    
                $warningValue = ($contract->totHours * 90) / 100;
    
                if ($contract->hours > $warningValue) {
    
                    return redirect('/tickets/' . $ticket->id)->with("warning", "Manca meno del 10% delle ore del contratto!");
    
                } else {
                    
                    return redirect()->route('tickets.show', ['id' => $ticket->id]);
    
                }
    
    
            } else {
    
                if ($remainingHours < $diffHours) {
                    
                    return back()->with("error", "ATTENZIONE, ore residue contratto: " . $remainingHours . " ore aggiuntive inserite: " . $diffHours)
                                ->withInput($data);
                    
                } else if ($lastTenPercHours >= $newRemainingHours && $diffHours < $remainingHours) {
                    
                    $ticket->update($data);
        
                    return redirect('/tickets/' . $ticket->id)->with("warning", "il contratto " . $contract->name . " ha meno del 10% di ore residue disponibili!!!");
        
                } else if ($remainingHours == $diffHours) {
                    
                    unset($contract['hours']);
        
                    $ticket->update($data);
                    // devo disattivare il contratto
                    $contract->active = 'N';
                    $contract->save();
        
                    return redirect('/tickets')->with("warning", "MAX ore contratto raggiunto, il contratto è stato CHIUSO!!!");
        
        
                } else {
                    
                    $ticket->update($data);
                    return redirect()->route('tickets.show', ['id' => $ticket->id]);
        
                }
    
            }

        } else {// $contract['type'] == 'increase'
            // DA INSERIRE UN RAGIONAMENTO SUL TEMPO RESIDUO
            // se inferiore all 85% avvisare
            // per il momento ritornare solo la view
            $ticket->update($data);
            return redirect()->route('tickets.show', ['id' => $ticket->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);

        $contract = $ticket->contract;

        if ($ticket->performedBy == Auth::user()['name'] || Auth::user()['role'] == 'admin') {
            // controllo se il contratto è attivo o meno
            if ($contract['active'] == 'N') {
                // se non fosse attivo, non appena cancello un ticket
                // libero ore disponibili sul contratto, che tornerà attivo
                $contract['active'] = 'Y';
                $contract->save();

                $ticket->delete();

                return redirect('/tickets')->with("success", "Il ticket è stato eliminato con successo e il contratto è stato riattivato");

            } else {

                $ticket->delete();

                return redirect('/tickets')->with("success", "Il ticket è stato eliminato con successo!");
                //return back()->with("success", "Il ticket è stato eliminato con successo!");
            }



        } else {

            return back()->with("warning", "Non puoi eliminare il ticket selezionato, in quanto non sei tu l'autore dell'intervento!");

        }

    }

    public function export(Request $request) {

        $dff = $request->only([
            'contractN',
            'startingDR',
            'endingDR',
            'searchedC',
            'searchedCDC',
            'contractStatus'
        ]);

        /* echo '<pre>';
        print_r($dff);
        echo '</pre>';
        die(); */

        $params = [];
        $contractN = isset($dff['contractN']) ? $dff['contractN'] : null;
        $startingDR = isset($dff['startingDR']) ? $dff['startingDR'] : null;
        $endingDR = isset($dff['endingDR']) ? $dff['endingDR'] : null;
        $searchedC = isset($dff['searchedC']) ? $dff['searchedC'] : null;
        $searchedCDC = isset($dff['searchedCDC']) ? $dff['searchedCDC'] : null;
        $contractStatus = isset($dff['contractStatus']) ? $dff['contractStatus'] : null;

        if (Auth::user()['role'] == 'admin') {// admin

            $tickets = Ticket::join('contracts', 'contracts.id', '=', 'tickets.contract_id')
                            ->join('cdcs', 'cdcs.id', '=', 'tickets.cdc_id')
                            ->join('clients', 'clients.id', '=', 'contracts.client_id')
                            ->join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                            ->select('clients.businessName as Azienda Cliente',
                                    'contracts.name', 'tickets.workTime', 'tickets.extraTime',
                                    'tickets.performedBy', 'tickets.comments', 'cdcs.businessName', 'tickets.end_date');
            
        } else {// user normale
            $emailPath = explode('@', Auth::user()['email']);
            $company = explode('.', $emailPath[1]);
            $companyName = $company[0]; // esempio: keyos
            
            $tickets = Ticket::join('contracts', 'tickets.contract_id', '=', 'contracts.id')
                            ->join('cdcs', 'cdcs.id', '=', 'tickets.cdc_id')
                            ->join('clients', 'clients.id', '=', 'contracts.client_id')
                            ->join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                            ->select('clients.businessName as Azienda Cliente', 
                                    'contracts.name', 'tickets.workTime', 'tickets.extraTime',
                                    'tickets.performedBy', 'tickets.comments', 'cdcs.businessName', 'tickets.end_date')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%');
            
        }

        if ($startingDR) {
            $tickets = $tickets->where('tickets.end_date', '>=', $startingDR);
        }
        if ($endingDR) {
            $tickets = $tickets->where('tickets.end_date', '<=', $endingDR);
        }
        if ($contractN) {
            $tickets = $tickets->where('contracts.name', 'like', '%'.$contractN.'%');
        }
        if ($searchedC) {
            $tickets = $tickets->where('contracts.client_id', $searchedC);
        }
        if ($searchedCDC) {
            $tickets = $tickets->where('tickets.cdc_id', $searchedCDC);
        }
        if ($contractStatus) {
            $tickets = $tickets->where('contracts.active', $contractStatus);
        }

        $tickets = $tickets->orderBy('tickets.end_date', 'desc');
        $tickets = $tickets->get();

        foreach ($tickets as $ticket) {
            $ticket->totHours = ($ticket->workTime + $ticket->extraTime);
            $ticket->end_date = date('d/m/Y', strtotime($ticket->end_date));
            unset($ticket->workTime);
            unset($ticket->extraTime);
        }

        /* echo '<pre>';
        print_r($tickets);
        echo '</pre>';
        die(); */

        return Excel::download(new TicketsExport($tickets), 'tickets.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }
}
