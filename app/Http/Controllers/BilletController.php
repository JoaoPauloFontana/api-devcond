<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Billet;
use App\Models\Unit;

class BilletController extends Controller
{
    public function getAll(Request $req)
    {
        $array = ['error' => ''];

        $property = $req->input('property');

        if ($property) {
            $userLogged = auth()->user();

            $unit = Unit::where('id', $property)
            ->where('id_owner', $userLogged['id'])
            ->count();

            if ($unit > 0) {
                $billets = Billet::where('id_unit', $property)->get();

                foreach($billets as $billetKey => $billetValue){
                    $billets[$billetKey]['fileurl'] = asset('storage/'.$billetValue['fileurl']);
                }

                $array['list'] = $billets;
            }else{
                $array['error'] = 'Esta propriedade não é sua.';
            }

        } else {
            $array['error'] = 'A propriedade é obrigatória.';
        }

        return $array;
    }
}
