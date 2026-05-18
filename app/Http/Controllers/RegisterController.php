<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{

    public function register(Request $request)
    {

        $request->validate([

            'name' => 'required',
            'email' => 'required|email|unique:users',
            'telephone' => 'required',
            'profession' => 'required',
            'password' => 'required|min:4'

        ]);

        $user = User::create([

            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'profession' => $request->profession,
            'password' => Hash::make($request->password),
            'statut' => 'attente'

        ]);

        Mail::send('emails.inscription', [

            'user' => $user

        ], function($message){

            $message->to('leprincef67@gmail.com')
                    ->subject('Nouvelle inscription');

        });

        return back()->with('success', 'Inscription envoyée avec succès');

    }

}