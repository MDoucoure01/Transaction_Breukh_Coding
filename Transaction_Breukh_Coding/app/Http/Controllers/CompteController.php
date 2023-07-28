<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Compte;
use Illuminate\Http\Request;

class CompteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Compte::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            "user_id" => 'required',
            "fournisseur" => 'required',
            "numero_compte" => 'required'
        ]);

        if ($validate->fails()) {
            return Response(['message' => $validate->errors()],401);
        }
        // return $request->fournisseur;
        return Compte::create([
            "user_id" => $request->user_id,
            "fournisseur" => $request->fournisseur,
            "numero_compte" => $request->numero_compte
        ]);
    }

    public function supprimerCompte(Request $request)
    {
        $user = Compte::where("id",$request->id)->delete();
        return [
            "utilisateur" => $user,
            "message" => "Utilisateur supprimer avec succes",
        ];
    }
    /**
     * Display the specified resource.
     */
    public function show(Compte $compte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Compte $compte)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compte $compte)
    {
        //
    }
}
