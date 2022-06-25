<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitPet;
use App\Models\UnitVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function getInfo($id)
    {
        $array = ['error' => ''];

        $unit = Unit::find($id);

        if($unit){
            $peoples = UnitPeople::where('id_unit', $id)->get();
            $vehicles = UnitVehicle::where('id_unit', $id)->get();
            $pets = UnitPet::where('id_unit', $id)->get();

            foreach($peoples as $peopleKey => $people){
                $peoples[$peopleKey]['birthdate'] = date('d/m/Y', strtotime($people->birthdate));
            }

            $array['peoples'] = $peoples;
            $array['vehicles'] = $vehicles;
            $array['pets'] = $pets;
        }else{
            $array['error'] = 'Propriedade inexistente!';

            return $array;
        }

        return $array;
    }

    public function addPerson($id, Request $req)
    {
        $array = ['error' => ''];

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'birthdate' => 'required|date'
        ]);

        if(!$validator->fails()){
            UnitPeople::create([
                'id_unit' => $id,
                'name' => $req['name'],
                'birthdate' => $req['birthdate']
            ]);
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addVehicle($id, Request $req)
    {
        $array = ['error' => ''];

        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'color' => 'required',
            'plate' => 'required',
        ]);

        if(!$validator->fails()){
            UnitVehicle::create([
                'id_unit' => $id,
                'title' => $req['title'],
                'color' => $req['color'],
                'plate' => $req['plate'],
            ]);
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addPet($id, Request $req)
    {
        $array = ['error' => ''];

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'race' => 'required'
        ]);

        if(!$validator->fails()){
            UnitPet::create([
                'id_unit' => $id,
                'name' => $req['name'],
                'race' => $req['race']
            ]);
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
