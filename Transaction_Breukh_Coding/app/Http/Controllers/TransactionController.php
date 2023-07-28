<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function depot(Request $request)
    {
        $valided = Validator::make($request->all(),[
            'expediteur_id' => 'required|exists:comptes,id',
            'destinataire_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:500',
            'type' => 'required'
        ]);
        if ($valided->fails()) {
            return Response(["message" => $valided->errors()],401);
        }

        $compte = Compte::where('id',$request->id)->first();

        if (!$compte) {
            return redirect()->back()->withErrors(['utilisateur_id' => 'Le compte de l\'utilisateur n\'existe pas.']);
        }

        $compte->solde += $request->montant;
        $compte->save;

        $depot = Transaction::create([
            'expediteur_id' => $request->expediteur_id,
            'destinataire_id' => $request->destinataire_id,
            'montant' => $request->montant,
            'type' => $request->type
        ]);

        return [ 
            "message" => "votre nouveau solde est de " . $compte->solde,
            "transaction" => $depot
        ];
        
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
