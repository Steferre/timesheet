<?php

namespace App\Http\Controllers\Auth;

use App\Models\Contract;
use App\Models\Client;
use App\Models\TycoonGroupCompany;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Exports\ContractsExport;
use Maatwebsite\Excel\Facades\Excel;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loggedUser = Auth::user();

        $dff = $request->only([
            'searchedC',
            //'contractS',
            'contractT',
        ]); // dff = data from filters

        $today = date('Y-m-d'); 
        
        // se sono admin posso vedere tutti i contratti
        if ($loggedUser['role'] == 'admin') {

            $clients = Client::all();

            if (count($dff) == 0) {
                // faccio vedere tutto
                // filtri non attivi
                $contracts = Contract::where('contracts.active', 'Y')->orderBy('name')->paginate(20);

                /* echo '<pre>';
                print_r($contracts);
                echo '</pre>';    
                echo '</pre>';
                echo 'numero contratti attivi: ' . count($contracts);
                echo '</pre>';    
                die();  */
                
                $numContratti = count($contracts);
                
                for ($i=0; $i < $numContratti; $i++) { 
                    $contract = $contracts[$i];
                    
                    // controllo se la data di fine contratto è impostata
                    // perchè potrebbe essere nulla
                    if ($contract->end_date) {
                        // controllo che la data odierna sia successiva alla data di fine del contratto
                        if ($today > $contract->end_date){
                            // se sì il contratto deve chiudersi
                            if ($contract->active == 'Y') {
                                $contract->active = 'N';
                                $contract->save();
                            }
                        }
                    }
                    
                    $contract->start_date = date("d-m-Y", strtotime($contract->start_date));
                    $contract->end_date = date("d-m-Y", strtotime($contract->end_date));
                    
                    
                    $contract->hours = DB::table('contracts')
                                        ->join('tickets', 'contracts.id', '=', 'tickets.contract_id')
                                        ->where('tickets.contract_id', $contract->id)
                                        ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
                               
                }

                return view('contracts.index', [
                    'contracts' => $contracts,
                    'loggedUser' => $loggedUser,   
                    'clients' => $clients,
                    'numContratti' => $numContratti   
                ]);

            } else {
                // count($dff) > 0
                // ho dei filtri attivi
                $searchedC = isset($dff['searchedC']) ? strtolower($dff['searchedC']) : null;
                //$contractS = isset($dff['contractS']) ? strtolower($dff['contractS']) : null;
                $contractT = isset($dff['contractT']) ? strtolower($dff['contractT']) : null;

                // devo recuperare e mostrare i contratti che soddisfano i dati dei filtri
                $contracts = Contract::join('clients', 'contracts.client_id', '=', 'clients.id')
                                    ->join('tycoon_group_companies', 'contracts.company_id', '=', 'tycoon_group_companies.id')
                                    ->select('contracts.*');
                
                if ($searchedC) {
                    $contracts = $contracts->where('clients.id', $searchedC);
                }
                /* if ($contractS) {
                    $contracts = $contracts->where('contracts.active', $contractS);
                } */
                if ($contractT) {
                    $contracts = $contracts->where('contracts.type', $contractT);
                }

                $contracts = $contracts->where('contracts.active', 'Y');
                $contracts = $contracts->orderBy('name');
                $contracts = $contracts->paginate(20);

                $numContratti = count($contracts);

                //echo $numContratti;

                if ($numContratti == 0) {
                    /* echo '<pre>';
                    print_r($request->input());
                    echo '</pre>';
                    die(); */
                    return back()->with('warning', "Non sono stati trovati contratti per questi parametri di ricerca!")->withInput($request->input());

                }

                for ($i=0; $i < $numContratti; $i++) { 
                    $contract = $contracts[$i];

                    if ($contract->end_date) {

                        if ($today > $contract->end_date){
                        
                            if ($contract->active == 'Y') {
                                $contract->active = 'N';
                                $contract->save();
                            }
                        }
                    }
                    
                    $contract->start_date = date("d-m-Y", strtotime($contract->start_date));
                    $contract->end_date = date("d-m-Y", strtotime($contract->end_date));
                    
                    
                    $contract->hours = DB::table('contracts')
                                        ->join('tickets', 'contracts.id', '=', 'tickets.contract_id')
                                        ->where('tickets.contract_id', $contract->id)
                                        ->sum(DB::raw('tickets.workTime + tickets.extraTime'));                 
                }

                return view('contracts.index', [
                    'contracts' => $contracts,
                    'loggedUser' => $loggedUser,
                    'clients' => $clients,   
                    'searchedC' => $searchedC,   
                    //'contractS' => $contractS,   
                    'contractT' => $contractT,
                    'numContratti' => $numContratti  
                ]);

                
                
            }
   
        } else { // sono user
            // dalla mail con la quale ci si logga trovo l'azienda del gruppo
            $userMail = explode('@', $loggedUser['email']);
            $company = explode('.', $userMail[1]);
            $companyName  = $company[0];
            
            // posso vedere solo i contratti che sono legati all'azienda
            // del gruppo di cui si fa parte
            //echo count($dff);

            // devo trovare le azienda clienti $clients per l'azienda del gruppo di cui faccio parte
            $clients = Client::join('contracts', 'contracts.client_id', '=', 'clients.id')
                            ->join('tycoon_group_companies', 'contracts.company_id', '=', 'tycoon_group_companies.id')
                            ->select('clients.id', 'clients.businessName')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%')
                            ->groupBy('clients.id', 'clients.businessName')
                            ->get();
            
            // parto a preparare la query per trovare i contratti che posso mostrare
            $contracts = Contract::join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                                    ->select('contracts.*')
                                    ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%');
            
            
            if (count($dff) == 0) {
                // filtro non attivo 
                // passo i contratti recuperati
                $contracts = $contracts->where('contracts.active', 'Y');
                $contracts = $contracts->orderBy('name');
                $contracts = $contracts->paginate(20);

                $numContratti = count($contracts);
                
                for ($i=0; $i < $numContratti; $i++) { 
                    $contract = $contracts[$i];

                    if ($contract->end_date) {

                        if ($today > $contract->end_date){
                        
                            if ($contract->active == 'Y') {
                                $contract->active = 'N';
                                $contract->save();
                            }
                        }
                    }
                            
                    $contract->start_date = date("d-m-Y", strtotime($contract->start_date));
                    $contract->end_date = date("d-m-Y", strtotime($contract->end_date));
                                                
                                                
                    $contract->hours = DB::table('tickets')
                                    ->where('tickets.contract_id', $contract->id)
                                    ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
                                                                                                
                }

                return view('contracts.index', [
                    'contracts' => $contracts,
                    'loggedUser' => $loggedUser,  
                    'clients' => $clients,  
                ]);

            } else {
                // filtro attivo
                $searchedC = isset($dff['searchedC']) ? strtolower($dff['searchedC']) : null;
                $contractS = isset($dff['contractS']) ? strtolower($dff['contractS']) : null;
                $contractT = isset($dff['contractT']) ? strtolower($dff['contractT']) : null;
                
                if ($searchedC) {
                    $contracts = $contracts->where('client_id', $searchedC);
                }
                if ($contractS) {
                    $contracts = $contracts->where('contracts.active', $contractS);
                }
                if ($contractT) {
                    $contracts = $contracts->where('contracts.type', $contractT);
                }

                $contracts = $contracts->where('contracts.active', 'Y');
                $contracts = $contracts->orderBy('name');
                $contracts = $contracts->paginate(20);

                $numContratti = count($contracts);

                if ($numContratti == 0) {
                    /* echo '<pre>';
                    print_r($request->input());
                    echo '</pre>';
                    die(); */
                    return back()->with('warning', "Non sono stati trovati contratti per questi parametri di ricerca!")->withInput($request->input());
                }
            
                for ($i=0; $i < $numContratti; $i++) { 
                    $contract = $contracts[$i];

                    if ($contract->end_date) {

                        if ($today > $contract->end_date){
                        
                            if ($contract->active == 'Y') {
                                $contract->active = 'N';
                                $contract->save();
                            }
                        }
                    }
                       
                    $contract->start_date = date("d-m-Y", strtotime($contract->start_date));
                    $contract->end_date = date("d-m-Y", strtotime($contract->end_date));
                                                
                                                
                    $contract->hours = DB::table('tickets')
                                    ->where('tickets.contract_id', $contract->id)
                                    ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
                                                                                                
                }

                return view('contracts.index', [
                    'contracts' => $contracts,
                    'loggedUser' => $loggedUser,
                    'clients' => $clients,
                    'searchedC' => $searchedC,   
                    //'contractS' => $contractS,   
                    'contractT' => $contractT,
                ]);
            }
        }

    }

    /* altra funzione index contratti chiusi */
    public function indexConClose(Request $request)
    {
        $loggedUser = Auth::user();

        $dff = $request->only([
            'searchedC',
            //'contractS',
            'contractT',
        ]); // dff = data from filters

        $today = date('Y-m-d'); 
        
        // se sono admin posso vedere tutti i contratti
        if ($loggedUser['role'] == 'admin') {

            $clients = Client::all();

            if (count($dff) == 0) {
                // faccio vedere tutto
                // filtri non attivi
                $contracts = Contract::where('contracts.active', 'N')->orderBy('name')->paginate(20);
                
                $numContratti = count($contracts);
                
                for ($i=0; $i < $numContratti; $i++) { 
                    $contract = $contracts[$i];
                    
                    // controllo se la data di fine contratto è impostata
                    // perchè potrebbe essere nulla
                    if ($contract->end_date) {
                        // controllo che la data odierna sia successiva alla data di fine del contratto
                        if ($today > $contract->end_date){
                            // se sì il contratto deve chiudersi
                            if ($contract->active == 'Y') {
                                $contract->active = 'N';
                                $contract->save();
                            }
                        }
                    }
                    
                    $contract->start_date = date("d-m-Y", strtotime($contract->start_date));
                    $contract->end_date = date("d-m-Y", strtotime($contract->end_date));
                    
                    
                    $contract->hours = DB::table('contracts')
                                        ->join('tickets', 'contracts.id', '=', 'tickets.contract_id')
                                        ->where('tickets.contract_id', $contract->id)
                                        ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
                               
                }

                return view('contracts.indexConClose', [
                    'contracts' => $contracts,
                    'loggedUser' => $loggedUser,   
                    'clients' => $clients,   
                ]);

            } else {
                // count($dff) > 0
                // ho dei filtri attivi
                $searchedC = isset($dff['searchedC']) ? strtolower($dff['searchedC']) : null;
                //$contractS = isset($dff['contractS']) ? strtolower($dff['contractS']) : null;
                $contractT = isset($dff['contractT']) ? strtolower($dff['contractT']) : null;

                // devo recuperare e mostrare i contratti che soddisfano i dati dei filtri
                $contracts = Contract::join('clients', 'contracts.client_id', '=', 'clients.id')
                                    ->join('tycoon_group_companies', 'contracts.company_id', '=', 'tycoon_group_companies.id')
                                    ->select('contracts.*');
                
                if ($searchedC) {
                    $contracts = $contracts->where('clients.id', $searchedC);
                }
                /* if ($contractS) {
                    $contracts = $contracts->where('contracts.active', $contractS);
                } */
                if ($contractT) {
                    $contracts = $contracts->where('contracts.type', $contractT);
                }

                $contracts = $contracts->where('contracts.active', 'N');
                $contracts = $contracts->orderBy('name');
                $contracts = $contracts->paginate(20);

                $numContratti = count($contracts);

                if ($numContratti == 0) {
                    /* echo '<pre>';
                    print_r($request->input());
                    echo '</pre>';
                    die(); */
                    return back()->with('warning', "Non sono stati trovati contratti per questi parametri di ricerca!")->withInput($request->input());
                }
                
                for ($i=0; $i < $numContratti; $i++) { 
                    $contract = $contracts[$i];

                    if ($contract->end_date) {

                        if ($today > $contract->end_date){
                        
                            if ($contract->active == 'Y') {
                                $contract->active = 'N';
                                $contract->save();
                            }
                        }
                    }
                    
                    $contract->start_date = date("d-m-Y", strtotime($contract->start_date));
                    $contract->end_date = date("d-m-Y", strtotime($contract->end_date));
                    
                    
                    $contract->hours = DB::table('contracts')
                                        ->join('tickets', 'contracts.id', '=', 'tickets.contract_id')
                                        ->where('tickets.contract_id', $contract->id)
                                        ->sum(DB::raw('tickets.workTime + tickets.extraTime'));                 
                }

                return view('contracts.indexConClose', [
                    'contracts' => $contracts,
                    'loggedUser' => $loggedUser,
                    'clients' => $clients,   
                    'searchedC' => $searchedC,   
                    //'contractS' => $contractS,   
                    'contractT' => $contractT,   
                ]);

            }
   
        } else { // sono user
            // dalla mail con la quale ci si logga trovo l'azienda del gruppo
            $userMail = explode('@', $loggedUser['email']);
            $company = explode('.', $userMail[1]);
            $companyName  = $company[0];
            
            // posso vedere solo i contratti che sono legati all'azienda
            // del gruppo di cui si fa parte
            //echo count($dff);

            // devo trovare le azienda clienti $clients per l'azienda del gruppo di cui faccio parte
            $clients = Client::join('contracts', 'contracts.client_id', '=', 'clients.id')
                            ->join('tycoon_group_companies', 'contracts.company_id', '=', 'tycoon_group_companies.id')
                            ->select('clients.id', 'clients.businessName')
                            ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%')
                            ->groupBy('clients.id', 'clients.businessName')
                            ->get();
            
            // parto a preparare la query per trovare i contratti che posso mostrare
            $contracts = Contract::join('tycoon_group_companies', 'tycoon_group_companies.id', '=', 'contracts.company_id')
                                    ->select('contracts.*')
                                    ->where('tycoon_group_companies.website', 'like', '%'. $companyName .'%');
            
            
            if (count($dff) == 0) {
                // filtro non attivo 
                // passo i contratti recuperati
                $contracts = $contracts->where('contracts.active', 'N');
                $contracts = $contracts->orderBy('name');
                $contracts = $contracts->paginate(20);

                $numContratti = count($contracts);
                
                for ($i=0; $i < $numContratti; $i++) { 
                    $contract = $contracts[$i];

                    if ($contract->end_date) {

                        if ($today > $contract->end_date){
                        
                            if ($contract->active == 'Y') {
                                $contract->active = 'N';
                                $contract->save();
                            }
                        }
                    }
                            
                    $contract->start_date = date("d-m-Y", strtotime($contract->start_date));
                    $contract->end_date = date("d-m-Y", strtotime($contract->end_date));
                                                
                                                
                    $contract->hours = DB::table('tickets')
                                    ->where('tickets.contract_id', $contract->id)
                                    ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
                                                                                                
                }

                return view('contracts.indexConClose', [
                    'contracts' => $contracts,
                    'loggedUser' => $loggedUser,  
                    'clients' => $clients,  
                ]);

            } else {
                // filtro attivo
                $searchedC = isset($dff['searchedC']) ? strtolower($dff['searchedC']) : null;
                //$contractS = isset($dff['contractS']) ? strtolower($dff['contractS']) : null;
                $contractT = isset($dff['contractT']) ? strtolower($dff['contractT']) : null;
                
                if ($searchedC) {
                    $contracts = $contracts->where('client_id', $searchedC);
                }
                /* if ($contractS) {
                    $contracts = $contracts->where('contracts.active', $contractS);
                } */
                if ($contractT) {
                    $contracts = $contracts->where('contracts.type', $contractT);
                }

                $contracts = $contracts->where('contracts.active', 'N');
                $contracts = $contracts->orderBy('name');
                $contracts = $contracts->paginate(20);

                $numContratti = count($contracts);

                if ($numContratti == 0) {
                    /* echo '<pre>';
                    print_r($request->input());
                    echo '</pre>';
                    die(); */
                    return back()->with('warning', "Non sono stati trovati contratti per questi parametri di ricerca!")->withInput($request->input());
                }
            
                for ($i=0; $i < $numContratti; $i++) { 
                    $contract = $contracts[$i];

                    if ($contract->end_date) {

                        if ($today > $contract->end_date){
                        
                            if ($contract->active == 'Y') {
                                $contract->active = 'N';
                                $contract->save();
                            }
                        }
                    }
                       
                    $contract->start_date = date("d-m-Y", strtotime($contract->start_date));
                    $contract->end_date = date("d-m-Y", strtotime($contract->end_date));
                                                
                                                
                    $contract->hours = DB::table('tickets')
                                    ->where('tickets.contract_id', $contract->id)
                                    ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
                                                                                                
                }

                return view('contracts.indexConClose', [
                    'contracts' => $contracts,
                    'loggedUser' => $loggedUser,
                    'clients' => $clients,
                    'searchedC' => $searchedC,   
                    //'contractS' => $contractS,   
                    'contractT' => $contractT,
                ]);
            }
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = TycoonGroupCompany::all();
        $clients = Client::all();

        return view('contracts.create', [
            'companies' => $companies,
            'clients' => $clients,
        ]);
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
            'name' => 'required',
            'uniCode' => 'nullable|unique:contracts,uniCode',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable',
            'active' => 'required',
            'type' => 'required',
            'totHours' => 'nullable|numeric',
            'company_id' => 'required',
            'client_id' => 'required',
        ]);

        $data = $request->all();

        /* echo '<pre>';
        print_r($data);
        echo '</pre>';    
        die('dopo la validazione'); */
        
        $slug = substr(md5($data['name']. '$' . $data['_token']), 0, 10);
        
        $contract = New Contract();

        $contract['name'] = $data['name'];
        $contract['uniCode'] = $data['uniCode'];
        $contract['start_date'] = $data['start_date'];
        $contract['end_date'] = $data['end_date'];
        $contract['description'] = $data['description'];
        $contract['totHours'] = $data['totHours'];
        $contract['active'] = $data['active'];
        $contract['type'] = $data['type'];
        $contract['company_id'] = $data['company_id'];
        $contract['client_id'] = $data['client_id'];
        $contract['slug'] = $slug;

        $contract->save();

        return redirect()->route('contracts.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data = $request->all();
        $contract = Contract::findOrFail($id);

        return view('contracts.show', ['contract' => $contract, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = $request->all();
        $contract = Contract::findOrFail($id);

        if(Auth::user()['role'] == 'admin') {

            return view('contracts.edit', ['contract' => $contract, 'data' => $data]);

        } else {

            return back()->with("warning", "Non puoi modificare il contratto, perchè non hai i privilegi di amministratore!");

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
            'name' => 'required',
            'uniCode' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable',
            'totHours' => 'nullable|numeric',
            'active' => 'required',
            'type' => 'required',
            'company_id' => 'required',
            'client_id' => 'required',
        ]);

        $data = $request->all();

        $contract = Contract::findOrFail($id);
        /* echo '<pre>';
        print_r($data);
        echo '</pre>';
        die(); */

        $contract->update($data);

        return redirect()->route('contracts.show', ['id' => $contract->id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //die('funzione da attivare');
        $contract = Contract::findOrFail($id);

        if(count($contract->tickets) == 0) {
            $contract->delete();
            return back()->with('success', 'Il contratto ' . $contract->name . ' è stato eliminato con successo!');
        } else {
            //return back()->with('warning', 'Attenzione! Il contratto ' . $contract->name . ' presenta dei ticket attivi! Non può essere eliminato.');
        }

    }

    public function exportCON(Request $request)
    {
        $dff = $request->only([
            'searchedC',
            'contractS',
            'contractT',
        ]);

        /* echo '<pre>';
        print_r($dff);
        echo '</pre>';
        die(); */
        $contracts = Contract::join('clients', 'contracts.client_id', '=', 'clients.id')
                                    ->join('tycoon_group_companies', 'contracts.company_id', '=', 'tycoon_group_companies.id')
                                    ->join('tickets', 'contracts.id', '=', 'tickets.contract_id')
                                    ->select('contracts.name', 'contracts.uniCode',
                                    'contracts.start_date','contracts.end_date', 'contracts.totHours',
                                    DB::raw("SUM(tickets.workTime + tickets.extraTime) as hours"),
                                    DB::raw("ROUND((SUM(tickets.workTime + tickets.extraTime)/contracts.totHours)*100) as perc_ore_utiizzate"),
                                    'contracts.active', 'contracts.type',
                                    'clients.businessName as Azienda Cliente', 'contracts.description')
                                    ->groupBy('contracts.id', 'contracts.name', 'contracts.uniCode',
                                    'contracts.start_date','contracts.end_date',
                                    'contracts.active', 'contracts.type',
                                    'contracts.totHours', 'contracts.description', 'clients.businessName');                   

        if (Auth::user()['role'] == 'admin') {

            $searchedC = isset($dff['searchedC']) ? strtolower($dff['searchedC']) : null;
            $contractS = isset($dff['contractS']) ? strtolower($dff['contractS']) : null;
            $contractT = isset($dff['contractT']) ? strtolower($dff['contractT']) : null;

            if ($searchedC) {
                $contracts = $contracts->where('clients.id', $searchedC);
            }
            if ($contractS) {
                $contracts = $contracts->where('contracts.active', $contractS);
            }
            if ($contractT) {
                $contracts = $contracts->where('contracts.type', $contractT);
            }

        } else {
            // accesso come user normale
            $userMail = explode('@', Auth::user()['email']);
            $company = explode('.', $userMail[1]);
            $companyName  = $company[0];

            $searchedC = isset($dff['searchedC']) ? strtolower($dff['searchedC']) : null;
            $contractS = isset($dff['contractS']) ? strtolower($dff['contractS']) : null;
            $contractT = isset($dff['contractT']) ? strtolower($dff['contractT']) : null;

            if ($searchedC) {
                $contracts = $contracts->where('clients.id', $searchedC);
            }
            if ($contractS) {
                $contracts = $contracts->where('contracts.active', $contractS);
            }
            if ($contractT) {
                $contracts = $contracts->where('contracts.type', $contractT);
            }

        }
        $contracts = $contracts->orderBy('name');
        $contracts = $contracts->get();
        /* echo '<pre>';
        print_r($contracts);
        echo '</pre>';

        die(); */

        return Excel::download(new ContractsExport($contracts), 'contracts.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }

}
