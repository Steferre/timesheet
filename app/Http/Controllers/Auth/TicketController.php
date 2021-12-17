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
        ]);

        /* echo '<pre>';
        var_dump($dff);
        echo '</pre>'; */
        

        if ($loggedUser['role'] == 'admin'){

            $clients = Client::all();
            $cdcs = Cdc::all();

            if (count($dff) == 0) {
                // filtri non settati
                $tickets = Ticket::all();
                
                return view('tickets.index', [
                    'tickets' => $tickets,
                    'users' => $users,
                    'clients' => $clients,
                    'cdcs' => $cdcs,
                ]);

            } else if (count($dff) > 0) {

                // filtri attivati endingDR
                $contractN = isset($dff['contractN']) ? $dff['contractN'] : null;
                $startingDR = isset($dff['startingDR']) ? $dff['startingDR'] : null;
                $endingDR = isset($dff['endingDR']) ? $dff['endingDR'] : null;
                $searchedC = isset($dff['searchedC']) ? $dff['searchedC'] : null;
                $searchedCDC = isset($dff['searchedCDC']) ? $dff['searchedCDC'] : null;
                /* echo '<pre>';
                var_dump($contractN);
                echo '</pre>';
                echo '<pre>';
                var_dump($startingDR);
                echo '</pre>';
                echo '<pre>';
                var_dump($searchedC);
                echo '</pre>';
                echo '<pre>';
                var_dump($searchedCDC);
                echo '</pre>';
                die(); */
                $tickets = Ticket::join('contracts', 'tickets.contract_id', '=', 'contracts.id');

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
                    $tickets = $tickets->where('contracts.client_id', $searchedCDC);
                }

                $tickets = $tickets->select('tickets.*')->get();

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
                        'cdcs' => $cdcs,
                    ]);

                } else {
                    // la ricerca non ha fornito risultati, perchè uno o più campi non sono corretti
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
                            ->select('clients.*')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%')
                            ->groupBy('clients.id')
                            ->get();

            // preparo la query per ottenere la lista dei centri di costo legati a contratti della società del gruppo
            $cdcs = Ticket::join('contracts', 'tickets.contract_id', '=', 'contracts.id')
                            ->join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                            ->join('cdcs', 'cdcs.id', '=', 'tickets.cdc_id')
                            ->select('cdcs.*')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%')
                            ->groupBy('cdcs.id')
                            ->get();

            // preparo la query di base per ottenere i ticket
            $tickets = Ticket::join('contracts', 'tickets.contract_id', '=', 'contracts.id')
                            ->join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                            ->select('tickets.*')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%');
                
            if (count($dff) == 0) {
                // ritorno tutti i ticket perchè nn ho filtri attivi
                $tickets = $tickets->get();


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
               /*  echo '<pre>';
                var_dump($contractN);
                echo '</pre>';
                echo '<pre>';
                var_dump($startingDR);
                echo '</pre>';
                echo '<pre>';
                var_dump($searchedC);
                echo '</pre>';
                echo '<pre>';
                var_dump($searchedCDC);
                echo '</pre>';
                die(); */
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

                $tickets = $tickets->get();

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

        // recupero i centri di costo da passare alla view
        $cdcs = Cdc::all();


        // se sono admin vedo tutti gli utenti
        if ($activeUser['role'] == 'admin') {

            $users = User::all();

            // se sono user vedo solo gli utenti dell'azienda del mio gruppo
        } else {
            
            $users = User::where('email', 'like', '%'. $emailArray[1] .'%')->get();

        }

        if (isset($data['slug'])) {

            $contract = Contract::where('slug', $data['slug'])->first();

            $companyName = strtolower(explode(' ', $contract->tycoonGroupCompany->businessName)[0]);

            $users = User::where('email', 'like', '%'. $companyName .'%')->get();

            /* echo '<pre>';
            print_r($companyName);
            echo '<pre>'; */

            if(isset($contract)) {
                return view('tickets.create', [
                    'data' => $data,
                    'contract' => $contract,
                    'activeUser' => $activeUser,
                    'users' => $users,
                    'cdcs' => $cdcs,
                ]);
            } else {
                return abort(404);
            }
            

        } else {
            
            if (count($_GET) == 0 && Auth::user()['role'] == 'admin') {
                // passo tutta la lista dei contratti
                $contracts = Contract::all();

                return view('tickets.create', [
                    'contracts' => $contracts,
                    'activeUser' => $activeUser,
                    'users' => $users,
                    'cdcs' => $cdcs,
                ]);
            } else if (count($_GET) == 0 && Auth::user()['role'] == 'user') {
                // devo far scegliere solo tra i contratti aperti dall'azienda di appartenenza dell'user

                $contracts = Contract::join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                                        ->select('contracts.*')
                                        ->where('tycoon_group_companies.businessName', 'like', '%'. $activeUserCompanyName .'%')
                                        ->get();

                /* echo '<pre>';
                print_r($contracts);
                echo '<pre>';
                die(); */
                return view('tickets.create', [
                    'contracts' => $contracts,
                    'activeUser' => $activeUser,
                    'users' => $users,
                    'cdcs' => $cdcs,
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
        $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'workTime' => 'required|numeric',
            'comments' => 'required',
            'performedBy' => 'required',
            'contract_id' => 'required',
            'openBy' => 'required',
            'cdc_id' => 'nullable',
            'extraTime' => 'nullable|numeric',
        ]);

        $data = $request->all();
        

        if ($data['extraTime'] == null) {
            $data['extraTime'] = 0;
        }

        $contract = Contract::findOrFail($data['contract_id']);

        // ho bisogno anche delle ore già utilizzate per questo contratto
        // in modo da poter controllare se con l'aggiunta di questo ticket 
        // il monte ore totale del contratto viene raggiunto o superato

        $contract->hours = DB::table('tickets')
                                    ->where('tickets.contract_id', $contract->id)
                                    ->sum(DB::raw('tickets.workTime + tickets.extraTime'));


        // devo controllare che le ore che sto aggiungendo non facciano superare il limite del contratto
        $x = $contract->totHours;
        $y = $contract->hours;
        $z = ($data['workTime'] + $data['extraTime']);

        $lastTenPercHours = ($x * 10)/100;
        
        $availableHours = $x -$y;

        $result = $x - ($y + $z);

        $client = $contract->client->businessName;

        // controlliamo se l'azienda cliente è già presente nella lista dei centri di costo
        $cdcRecordExist = Cdc::where('cdcs.businessName', 'like', '%'. $client .'%')
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

        $newCDC = Cdc::where('cdcs.businessName', 'like', '%'. $client .'%')
                                ->select('cdcs.id')->first();

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

        
        // controlli sulle ore rimanenti di un contratto                    
        if ($result < 0) {
            // significa che le ore del contratto sono finite
            // e che con l'aggiunta di questo ticket si andrebbe oltre
            return back()->with("error", "Attenzione, le ore aggiunte superano quelle rimaste a disposizione, per il seguente contratto! Ore rimaste " . $availableHours)
                        ->withInput($request->input());

        } else if ($lastTenPercHours > $result && $result > 0) {
            // siamo nell'ultimo 10% di ore disponibili
            $ticket->save();

            return redirect('tickets')->with("warning", "ATTENZIONE!!! " . " " . $contract->name . " " . "Percentuale di Ore rimaste disponibili < 10%");

        } else if ($result == 0) {
            // il contratto ha esaurito le ore disponibili
            // dopo la creazione di questo ticket 
            // è necessario chiudere il contratto
            $ticket->save();

            // devo disattivare il contratto
            unset($contract['hours']);
            $contract->active = 'N';
            $contract->save();

            return redirect('tickets')->with("warning", "ATTENZIONE!!! " . "Le ore disponibili per il contratto: " . $contract->name . " " . "sono terminate! Il contratto è quindi stato chiuso");

        } else {

            $ticket->save();

            return redirect()->route('tickets.index');
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        return view('tickets.show', ['ticket' => $ticket]);
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

        if ($ticket->performedBy == Auth::user()['name'] || Auth::user()['role'] == 'admin') {

            return view('tickets.edit', ['ticket' => $ticket]);

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
        ]);

        $data = $request->all();

        $ticket = Ticket::findOrFail($id);

        // ore segnate nel ticket che sto modificando (sia quelle user sia quelle admin)
        $savedTicketHours = ($ticket->workTime) + ($ticket->extraTime);

        $contract = $ticket->contract;

        // ore totali già usate
        // N.B. in queste ore ci sono anche le savedTicketHours
        $contract->hours = DB::table('tickets')
                                    ->where('tickets.contract_id', $contract->id)
                                    ->sum(DB::raw('tickets.workTime + tickets.extraTime'));

        // nuove ore inserite 
        $modifiedTicketHours = ($data['workTime']) + ($data['extraTime']);
        
        // prima di effettuare l'update, devo accertarmi che le ore aggiunte non sforino il limite delle ore totali del contratto
        // quindi devo trovare la differenza tra le ore già presenti nel ticket e quelle dell'aggiornamento
        // e valutare se sono congrue con quanto detto sopra
        // ho bisogno anche di sapere quante ore mancano alla fine del contratto

        $remainingHours = $contract->totHours - $contract->hours;
        $diffHours = $modifiedTicketHours - $savedTicketHours;

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

                return back()->with("success", "Il ticket è stato eliminato con successo e il contratto è stato riattivato");

            } else {

                $ticket->delete();

                return back()->with("success", "Il ticket è stato eliminato con successo!");
            }



        } else {

            return back()->with("warning", "Non puoi eliminare il ticket selezionato, in quanto non sei tu l'autore dell'intervento!");

        }

    }

    public function export(Request $request) {

        $params = [];
        $name = isset($request['q_name']) ? ($request['q_name']!=='' ? $request['q_name'] : null) : null;
        $group = isset($request['q_group']) ? ($request['q_group']!=='' ? $request['q_group'] : null) : null;
        $bool = isset($request['q_bool']) ? ($request['q_bool']!=='' ? $request['q_bool'] : null) : null;
        if ($name) $params[] = ['name', 'like', '%'.$name.'%'];
        if ($group) $params[] = ['group', 'like', '%'.$group.'%'];
        if ($bool !== null) $params[] = ['active', $bool];

        $promoters = Promoter::join('users', 'users.id', '=', 'promoters.userId')
            ->select('users.name as nominativo', 'users.email as email1', 'promoters.*')
            ->where($params)
            ->get();

        return Excel::download(new PromotersExport($promoters), 'promoters.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }
}
