<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
    }


    public function charge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required'
        ]);

        if ($validator->fails()) {
            return Response(['message' => $validator->errors()],401);
        }

        $user = User::where('telephone',$request->telephone)
        ->first();

        return ["nomComplet" => $user->prenom.' '.$user->nom];
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required',
            'prenom' => 'required',
            'telephone' => 'required'
        ]);
        if ($validator->fails()) {
            return Response(['message' => $validator->errors()],401);
        }
        // return $request->nom;
        $user = User::create([
            "nom" => $request->nom,
            "prenom" => $request->prenom,
            "telephone" => $request->telephone
        ]);
        return $user;
    }

    public function supprimerUser(Request $request)
    {
        $user = User::where("id",$request->id)->delete();
        return [
            "utilisateur" => $user,
            "message" => "Utilisateur supprimer avec succes",
        ];
    }

    public function userLister(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            return $user;
        }
        return [ "message" => "Utilisateur no existant" ];
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        
    }
}
