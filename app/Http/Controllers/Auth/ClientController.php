<?php

namespace App\Http\Controllers\Auth;

use App\Models\Client;
use App\Models\Cdc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dff = $request->only(['searchedC']);

        if(!isset($dff['searchedC'])) {
            // sono nella situazione in cui non c'è filtro o si filtra senza scrivere nulla
            $clients = Client::paginate(10);

            return view('clients.index', ['clients' => $clients]); 

        } else {
            // ho attivato il filtro
            $query = $dff['searchedC'];

            $clients = Client::select('*')->where('businessName', 'like', $query.'%')->paginate(10);

            if (count($clients) > 0) {

                return view('clients.index', ['clients' => $clients, 'client' => $query]);

            } else { // in questo caso non sono state trovate aziende con quel nome o per quella query
                
                return back()->with("warning", "La ricerca non ha prodotto risultati! RIPROVARE CON PARAMETRI DIVERSI!")->withInput($request->input());

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
        $cdcs = Cdc::all();

        return view('clients.create', ['cdcs' => $cdcs]);
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
            'businessName' => 'required',
            'email' => 'required|unique:clients,email',
            'pIva' => 'required|max:11',
            'address' => 'nullable',
            'buldingNum' => 'nullable',
            'city' => 'nullable',
            'province' => 'nullable|max:2',
            'country' => 'nullable',
            'postalCode' => 'nullable|max:5',
            'phone' => 'nullable|max:10',
        ]);

        $data = $request->all();

        /* echo '<pre>';
        print_r($data);
        echo '</pre>';
        die(); */

        $client = new Client();

        $client['businessName'] = $data['businessName'];
        $client['email'] = $data['email'];
        $client['pIva'] = $data['pIva'];
        $client['address'] = $data['address'];
        $client['buldingNum'] = $data['buldingNum'];
        $client['city'] = $data['city'];
        $client['province'] = $data['province'];
        $client['country'] = $data['country'];
        $client['postalCode'] = $data['postalCode'];
        $client['phone'] = $data['phone'];

        $client->save();


        // una volta creata l'azienda vado a creare un'ombra anche nella sezione centri di costo
        // questo perchè quando il ticket viene creato per l'azienda direttamente cliente
        // la stessa azienda è anche un centro di costo
        $dataForCdc = $request->only('businessName');

        $cdc = new Cdc();

        $cdc['businessName'] = $dataForCdc['businessName'];

        $cdc->save();

        /* echo '<pre>';
        print_r($dataForCdc);
        echo '</pre>';
        die(); */

        $client->cdcs()->sync($data['cdc_id']);

        return redirect()->route('clients.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);

        $cdcs = $client->cdcs()->where('clientID', $id)->get();
        
        return view('clients.show', ['client' => $client, 'cdcs' => $cdcs]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);

        // nella finestra di modifica devo fornire tutti i cdcs 
        // e confrontarli con quelli selezionati in fase di creazione
        // per permettere l'aggiunta di cdcs nuovi
        $cdcs = $client->cdcs()->where('clientID', $id)->get();

        $allCdcs = Cdc::all();

        return view('clients.edit', ['client' => $client, 'cdcs' => $cdcs, 'allCdcs' => $allCdcs]);
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
            'businessName' => 'required',
            'email' => 'required',
            'pIva' => 'required',
            'address' => 'required',
            'buldingNum' => 'required',
            'city' => 'required',
            'province' => 'required|max:2',
            'country' => 'required',
            'postalCode' => 'required|max:5',
            'phone' => 'required|max:10',
        ]);

        $data = $request->all();

        $client = Client::findOrFail($id);

        /* echo '<pre>';
        print_r($data['cdc_id']);
        echo '</pre>'; */
        
        // prima di fare l'update devo controllare che i cdc selezionati siano o meno già presenti come relazione
        // se ci sono già non metterli più
        // se non ci sono aggiungerli
        // se vengono tolti, eliminarli
        // trovo la lunghezza del nuovo array di cdc selezionati nell'area di modifica
        $numCDC = count($data['cdc_id']);
        /* echo '<pre>';
        echo $numCDC;
        echo '</pre>'; */

        // trovo i cdc già presenti
        $cdcs = $client->cdcs()->where('clientID', $id)->get();
        $numOldCdc = count($cdcs);
        /* echo '<pre>';
        print_r($cdcs);
        echo '</pre>'; */
        
        if ($numOldCdc > 0) {

            for ($i=0; $i < $numOldCdc; $i++) {
                // ottengo l'id di ogni centro di costo già selezionato al momento della creazione 
                $cdcID = $cdcs[$i]['id'];
                // vado a togliere il link con la tabella pivot
                $client->cdcs()->detach($cdcID);
                /* echo '<pre>';
                echo 'detach eseguito';
                echo '</pre>'; */
            }

        } else {
            //echo 'non ci sono cdc selezionati';
        }

        // adesso salvo i nuovi cdc selezionati
        $client->cdcs()->attach($data['cdc_id']);
        // proviamo la funzione updating existing pivot
        //$client->cdcs()->updateExistingPivot($data['cdc_id'], []);

        $client->update($data);

        return redirect()->route('clients.show', ['id' => $client->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //die('funzione ancora da attivare');
        $client = Client::findOrFail($id);
        

        if (count($client->contracts) == 0) {
            $client->delete();
        }

        return back()->with('success', "L'azienda " . $client->businessName . " è stata eliminata con successo!");
    }
}
