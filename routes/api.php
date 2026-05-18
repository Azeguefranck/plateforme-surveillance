<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;



Route::post('/capteurs', function (Request $request) {

    DB::table('mesures')->insert([

        'temperature' => $request->temperature,
        'humidite' => $request->humidite,
        'gaz' => $request->gaz,
        'courant' => $request->courant,
        'puissance' => $request->puissance,

        'created_at' => now(),
        'updated_at' => now()

    ]);


    $temperature = $request->temperature;
    $gaz = $request->gaz;


    if($temperature >= 40 || $gaz >= 500){

        DB::table('alertes')->insert([

            'type_alerte' => 'danger',

            'message' =>
            'Depassement seuil critique',

            'niveau' => 'critique',

            'created_at' => now()

        ]);


        $users = DB::table('users')
            ->where('validation_status','valide')
            ->get();


        foreach($users as $user){

            Mail::raw(

                'ALERTE : depassement seuil critique',

                function($mail) use ($user){

                    $mail->to($user->email)
                    ->subject('ALERTE SERVEUR');

                }

            );

        }

    }


    return response()->json([

        'success' => true

    ]);

});




Route::get('/dashboard-data', function () {

    return DB::table('mesures')
        ->latest()
        ->first();

});