<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use App\Models\Client;
use App\Models\Cdc;
use App\Models\TycoonGroupCompany;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contracts = Contract::all();

        foreach ($contracts as $contract) {
            $contract->hours = Contract::join('tickets', 'contracts.id', '=', 'tickets.contract_id')
                                        ->where('tickets.contract_id', $contract->id)
                                        ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
        }

        return response()->json([
            'success' => true,
            'contracts' => $contracts,
        ]);
    }

    public function filter()
    {
        /* $contracts = Contract::join('tickets', 'tickets.contract_id', '=', 'contracts.id')
                            ->where('contracts.type', 'decrease')
                            ->groupBy('contracts.id')
                            ->get(); */
        $contracts = Contract::join('tickets', 'tickets.contract_id', '=', 'contracts.id')
                        ->join('clients', 'clients.id', '=', 'contracts.client_id')
                        ->join('cdcs', 'tickets.cdc_id', '=', 'cdcs.id')
                        ->select('contracts.name', 'clients.businessName as azienda cliente', 'tickets.id',
                                'tickets.workTime', 'tickets.extraTime', 'tickets.cdc_id',
                                'cdcs.businessName as centro di costo')
                        ->where('type', 'decrease')
                        ->get();
        foreach ($contracts as $contract) {
            $contract->hours = $contract->where('tickets.contract_id', $contract->id)
            ->sum(DB::raw('tickets.workTime + tickets.extraTime'));
        }                       

        return response()->json([
            'success' => true,
            'contracts' => $contracts,
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
