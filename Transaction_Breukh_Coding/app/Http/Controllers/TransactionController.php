<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
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
            'fournisseur' => 'required|in:OrangeMoney,Wave,Compte Banquaire,Wari',
            'expediteur' => 'required',
            'destinataire' => 'required',
            'montant' => 'required|numeric|min:500',
            'type' => 'required'
        ]);

        if ($valided->fails()) {
            return Response(["message" => $valided->errors()],401);
        }

        $expediteur = User::where('telephone',$request->expediteur)
        ->first();
        if (!$expediteur) {
            return ['utilisateur_id' => 'Le compte de l\'expediteur n\'existe pas Ou le fournisseur n\'existe pas'];
        }

        $destinataire = User::where('telephone',$request->destinataire)
        ->first();

        if (!$destinataire) {
            return ['utilisateur_id' => 'Le compte du destinataire n\'existe pas Ou le fournisseur n\'existe pas'];
        }

        $compteDestinataire = Compte::where('user_id',$destinataire->id)
        ->where('fournisseur',$request->fournisseur)
        ->first();
        // return $compteDestinataire;

        if (!$compteDestinataire) {
            return ['utilisateur_id' => 'Le compte du destinataire n\'existe pas dans cette Operateur Ou le fournisseur n\'existe pas'];
        }

        // return $compteDestinataire;
        // $compte = Compte::where('user_id',$request->id)
        // ->where('fournisseur',$request->fournisseur)
        // ->first();

        // if (!$compte) {
        //     return ['utilisateur_id' => 'Le compte de l\'utilisateur n\'existe pas Ou le fournisseur n\'existe pas'];
        // }

        $compteDestinataire->solde += $request->montant;
        $compteDestinataire->save();

        $depot = Transaction::create([
            'expediteur_id' => $expediteur->id,
            'destinataire_id' => $compteDestinataire->id,
            'montant' => $request->montant,
            'type' => $request->type
        ]);

        return [ 
            "message" => "votre nouveau solde est de " . $compteDestinataire->solde,
            "transaction" => $depot
        ];
        
    }

    public function retrait(Request $request)
    {
        $valided = Validator::make($request->all(),[
            'destinataire' => 'required',
            'fournisseur' => 'required|in:OrangeMoney,Wave,Compte Banquaire,Wari',
            'montant' => 'required|numeric|min:500',
            'type' => 'required'
        ]);

        if ($valided->fails()) {
            return Response(["message" => $valided->errors()],401);
        }

        if ($request->type == 'retrait') {

            $expediteur = User::where('telephone',$request->destinataire)
            ->first();

            if (!$expediteur) {
                return ['utilisateur_id' => 'Le compte n\'existe pas Ou le fournisseur n\'existe pas'];
            }
    
            $compteDestinataire = Compte::where('user_id',$expediteur->id)
            ->where('fournisseur',$request->fournisseur)
            ->first();

            if (!$compteDestinataire) {
                return ['utilisateur_id' => 'Le compte du destinataire n\'existe pas Ou le fournisseur n\'existe pas'];
            }

            
            if ($compteDestinataire->solde >= $request->montant) {
                
                $compteDestinataire->solde -= $request->montant;
                $compteDestinataire->save();
                
                
                $retrait = Transaction::create([
                    'expediteur_id' => $expediteur->id,
                    'destinataire_id' => $compteDestinataire->id,
                    'montant' => $request->montant,
                    'type' => $request->type
                ]);
    
                return [ 
                    "message" => "votre nouveau solde est de " . $compteDestinataire->solde,
                    "transaction" => $retrait
                ];
    
                
            }
            return [ "message" => "Montant Insuffisant"];


        }
        return [ "message" => "Transaction No effecteur veillez regarder les parapmetres"];
    }

    public function envoie(Request $request)
    {
        $valided = Validator::make($request->all(),[
            'fournisseur' => 'required|in:OrangeMoney,Wave,Compte Banquaire,Wari',
            'expediteur_id' => 'required|exists:comptes,id',
            'destinataire_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:500',
            'type' => 'required'
        ]);

        if ($valided->fails()) {
            return Response(["message" => $valided->errors()],401);
        } 

        if ($request->type == 'transfert compte') {

            $compte = Compte::where('user_id',$request->expediteur_id)
            ->where('fournisseur',$request->fournisseur)
            ->first();

            // return $compte;
            if (!$compte) {
                return ['utilisateur_id' => 'Le compte de l\'utilisateur n\'existe pas Ou le fournisseur n\'existe pas'];
            }

            $dest = Compte::where('user_id',$request->destinataire_id)
            ->where('fournisseur',$request->fournisseur)
            ->first();

            if (!$dest) {
                return ['utilisateur_id' => 'Le compte du destinataire n\'existe pas Ou le fournisseur n\'existe pas'];
            }
            
            // return $compte->solde;
            if ($compte->solde >= $request->montant) {

                $compte->solde -= $request->montant;
                $compte->save();

                $dest->solde += $request->montant;
                $dest->save();

                $retrait = Transaction::create([
                    'expediteur_id' => $request->expediteur_id,
                    'destinataire_id' => $request->destinataire_id,
                    'montant' => $request->montant,
                    'type' => $request->type
                ]);

                return [ 
                    "message" => "votre nouveau solde est de " . $compte->solde,
                    "transaction" => $retrait
                ];
            }
            return [ "message" => "Montant Insuffisant pour effectuer un envoyer"];
        }
        return [ "message" => "Transaction No effecteur veillez regarder le type de transaction"];
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
