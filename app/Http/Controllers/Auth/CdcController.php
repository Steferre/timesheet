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

            $cdcs = Cdc::all();

            return view('cdcs.index', ['cdcs' => $cdcs]);

        } else {

            $query = $dff['searchedC'];

            $cdcs = Cdc::where('businessName', 'like', $query.'%')->get();

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
        //
    }
}
