<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

public function validateUser($id)
{

DB::table('users')
->where('id',$id)
->update([
'statut'=>'VALIDE'
]);

return redirect('/parametres');

}

public function rejectUser($id)
{

DB::table('users')
->where('id',$id)
->update([
'statut'=>'REFUSE'
]);

return redirect('/parametres');

}

public function pendingUser($id)
{

DB::table('users')
->where('id',$id)
->update([
'statut'=>'EN_ATTENTE'
]);

return redirect('/parametres');

}

public function deleteUser($id)
{

DB::table('users')
->where('id',$id)
->delete();

return redirect('/parametres');

}

}
