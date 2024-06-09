<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class profileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    public function index(Request $request)
    {
        $cookie = $request->cookie("user_role");
        $entreprise = Entreprise::all()->first();
        $user = User::all()->where("username",json_decode($cookie,true)["username"])->first();
        if ($user->role != "admin") {
            $buttonAction = false;
            return view("entreprise_profile", compact("entreprise", "buttonAction","user"));
        } else {
            $buttonAction = true;
            return view("entreprise_profile", compact("entreprise","buttonAction","user"));
        }
    }

    public function userIndex(Request $request)
    {
        $cookie = $request->cookie("user_role");
        $entreprise = Entreprise::all()->first();
        $user = User::all()->where("username",json_decode($cookie,true)["username"])->first();
        if ($user->role != "admin") {
            $buttonAction = false;
        return view("user_profile",compact("user","entreprise","buttonAction"));
        } else {
        $buttonAction = true;
        return view("user_profile", compact("entreprise","buttonAction","user"));
    }


    }
    public function update(Request $request)
    {
        $UpdateContent = Entreprise::where('titre', $request->referenceVaue)->first();


        $file = $request->file("logo");

        if ($file) {
            $destinationPath = "uploads";
            $filename = $file->getClientOriginalName();
        }

        if ($UpdateContent) {
            $UpdateContent->titre = $request->titre;
            if ($file && $file->move($destinationPath,$filename)){
                $UpdateContent->logo = $filename;
            }
            $UpdateContent->description = $request->description;
            $UpdateContent->save();
            return redirect()->route("profile.index")->with('success', 'Le profile de entreprise a été modifié avec succès.');
        }
    }

    public function userUpdate(Request $request) {
        $UpdateContent = User::where('username', $request->referenceVaue)->first();


        if ($UpdateContent) {
                $UpdateContent->nom = $request->nom;
                $UpdateContent->prenom = $request->prenom;
                $UpdateContent->save();
                return redirect()->route("profile.userIndex")->with('success', 'Le profile de l\'utilisateur a été modifié avec succès.');    
            }
    }

    public function update_password(Request $request) {
        $UpdateContent = User::where('username', $request->referenceVaue)->first();
        if ($UpdateContent) {
            $UpdateContent->password = password_hash($request->password,PASSWORD_DEFAULT);
            $UpdateContent->save();
            return redirect()->route("profile.userIndex")->with('success', 'password de l\'utilisateur a été modifié avec succès.');    
        
        }

    }

}
