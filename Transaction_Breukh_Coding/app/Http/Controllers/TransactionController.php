<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'expediteur' => 'required|exists:users,telephone',
            'destinataire' => 'required|exists:users,telephone',
            'montant' => 'required|numeric|min:500',
            'type' => [
                'required',
                Rule::in(['transfert compte']),
            ],
        ]);

        if ($valided->fails()) {
            return Response(["message" => $valided->errors()],401);
        } 

        if ($request->type == 'transfert compte') {
            $UserExpediteur_id = User::where('telephone',$request->expediteur)->first();
            $UserDestinataire_id = User::where('telephone',$request->destinataire)->first();

            $CompteExpediteur_id = Compte::where('user_id',$UserExpediteur_id->id)
            ->where('fournisseur',$request->fournisseur)
            ->first();
            $CompteDestinataire_id = Compte::where('user_id',$UserDestinataire_id->id)
            ->where('fournisseur',$request->fournisseur)
            ->first();

            if (!$CompteExpediteur_id || !$CompteDestinataire_id) {
                return [ "message" => "Un(1) de ses numero n'a pas de Compte Sur se fournisseur il est conseiller de faire le transfert par code." ];
            }

            if ($CompteExpediteur_id->id == $CompteDestinataire_id->id) {
                return [ "message" => "Impossible Vous voulez effectuer une Un Envoie sur le meme Numero" ];
            }


            $fraisPourcentage = 0;
            switch ($request->fournisseur) {
            case 'OrangeMoney':
            case 'Wave':
                $fraisPourcentage = 0.01;
                break;
            case 'Wari':
                $fraisPourcentage = 0.02;
                break;
            case 'Compte Banquaire':
                $fraisPourcentage = 0.05;
                break;
            default:
            return response(["message" => "Fournisseur inconnu ou non spécifié"], 400);
        }

        $frais = $request->montant * $fraisPourcentage;

        $montantTotal = $request->montant + $frais;

        // return $montantTotal;
        
        // return $CompteExpediteur_id->solde;
        if ($CompteExpediteur_id->solde >= $montantTotal) {

            $CompteExpediteur_id->solde -= $montantTotal;
            $CompteExpediteur_id->save();

            $CompteDestinataire_id->solde += $request->montant;
            $CompteDestinataire_id->save();

            $retrait = Transaction::create([
                'expediteur_id' => $CompteExpediteur_id->id,
                'destinataire_id' => $CompteDestinataire_id->id,
                'montant' => $request->montant,
                'type' => $request->type
            ]);

            return [ 
                "message" => "votre nouveau solde est de " . $CompteExpediteur_id->solde,
                "transaction" => $retrait
            ];
        }
        return [ "message" => "Montant Insuffisant pour effectuer un envoyer"];
    }
        return [ "message" => "Transaction No effecteur veillez regarder le type de transaction"];
    }


    public function chargeHistorique(Request $request)
    {
        $valided = Validator::make($request->all(),[
            'telephone' => 'required|numeric'
        ]);
        if ($valided->fails()) {
            return Response(["message" => $valided->errors()],401);
        } 
        $user = User::where('telephone',$request->telephone)->first();

        if (!$user) {
            return Response(["message" => "User not found"]);
        }

        //dans le tableau
        $userDepot = Transaction::where('expediteur_id',$user->id)
        ->where('type',"depot")
        ->get();
        //dans le tableau
        $userCode = Transaction::where('destinataire_id',$user->id)
        ->where('type',"retait")
        ->where('code','!=',null)
        ->get();
        
        if ($request->fournisseur) {
            $compteExiste = Compte::where('user_id',$user->id)
            ->where('fournisseur',$request->fournisseur)
            ->first();
    
            //dans le tableau
            $userSansCode = Transaction::where('destinataire_id',$compteExiste->id)
            ->where('code', null)
            ->get();

            // return $userCompteTransaction;
            return [
                "Fairedepot" => $userDepot,
                "receptionViaCode" => $userCode,
                "ReceptionClient" => $userSansCode
            ];
        }
        return [
            "Fairedepot" => $userDepot,
            "receptionViaCode" => $userCode
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
