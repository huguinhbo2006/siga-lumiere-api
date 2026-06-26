<?php

namespace App\Http\Controllers;

use App\Ficha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalificacionesController extends Controller
{
    function examenes(Request $request){
        try {
            $examenes = Ficha::find($request['id'])->examenes();
            return response()->json($examenes, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}