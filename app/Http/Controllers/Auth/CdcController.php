<?php

namespace App\Http\Controllers\Auth;

use App\Models\Cdc;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CdcController extends Controller
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

            $cdcs = Cdc::paginate(10);

            return view('cdcs.index', ['cdcs' => $cdcs]);

        } else {

            $query = $dff['searchedC'];

            $cdcs = Cdc::where('businessName', 'like', $query.'%')->paginate(10);

            if (count($cdcs) > 0) {
                // trovati risultati e/o risultato
                return view('cdcs.index', ['cdcs' => $cdcs, 'cdc' => $query]);

            } else {
                // non sono stati trovati risultati
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
        return view('cdcs.create');
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
        ]);

        $data = $request->all();

        $cdc = new Cdc();

        $cdc['businessName'] = $data['businessName'];

        $cdc->save();

        return redirect()->route('cdcs.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cdc = Cdc::findOrFail($id);
        $clients = $cdc->clients()->where('cdcID', $id)->get(); // ritorna un array
        /* echo '<pre>';
        var_dump(count($clients));
        echo '</pre>'; */
        // se il centro di costo non è legato a nessun cliente si può eliminare direttamente
        // $clients sarà un array vuoto
        if(count($clients) == 0) {
            $cdc->delete();

            return back()->with('success', "Il centro di costo " . $cdc->businessName . " è stato eliminato con successo!");

        } else {
            // $clients avrà tanti elementi quante aziende clienti è legato il cdc
            // in questo caso per poterlo eliminare bisognerebbe eliminare tutte le relazioni
            // con le aziende clienti
            return back()->with('warning', "ATTENZIONE!!! Il centro di costo è legato a n°: " . count($clients) . " azienda/e cliente/i, quindi non può essere eliminato.");
        }



        //die('funzione ancora da scrivere');

    }
}
