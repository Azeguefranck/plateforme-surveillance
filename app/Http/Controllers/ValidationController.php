<?php

namespace App\Http\Controllers;

use App\Models\User;

class ValidationController extends Controller
{

    public function valider($id)
    {

        $user = User::findOrFail($id);

        $user->statut = "valide";

        $user->save();

        return "
        <h1 style='color:green;text-align:center'>
        UTILISATEUR VALIDÉ
        </h1>
        ";

    }

    public function refuser($id)
    {

        $user = User::findOrFail($id);

        $user->statut = "bloque";

        $user->save();

        return "
        <h1 style='color:red;text-align:center'>
        UTILISATEUR BLOQUÉ
        </h1>
        ";

    }

    public function attente($id)
    {

        $user = User::findOrFail($id);

        $user->statut = "attente";

        $user->save();

        return "
        <h1 style='color:orange;text-align:center'>
        UTILISATEUR EN ATTENTE
        </h1>
        ";

    }

}