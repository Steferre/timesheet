<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use App\Models\Client;
use App\Models\Cdc;
use App\Models\TycoonGroupCompany;
use App\Models\Ticket;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contracts = Contract::all();
        $clients = Client::all();
        $companies = TycoonGroupCompany::all();
        $users = User::all();

        return response()->json([
            'success' => true,
            'contracts' => $contracts,
            'clients' => $clients,
            'companies' => $companies,
            'users' => $users,
        ]);
    }

    public function getCDCs(Request $request)
    {
        $data = $request->all();
        $id = $data['id'];
        $client = Client::findOrFail($id);
        // trovo i centri di costo che sono legati al cliente in questione
        $cdcs = $client->cdcs()->where('clientID', $id)->get();
        return response()->json([
            'success' => true,
            'cdcs' => $cdcs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
