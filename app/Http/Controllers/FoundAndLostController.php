<?php

namespace App\Http\Controllers;

use App\Models\FoundAndLost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FoundAndLostController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $lost = FoundAndLost::where('status', 'LOST')
            ->orderBy('datecreated', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        $recovered = FoundAndLost::where('status', 'RECOVERED')
            ->orderBy('datecreated', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        foreach($lost as $lostKey => $lostValue) {
            $lost[$lostKey]['datecreated'] = date('d/m/Y', strtotime($lostValue->datecreated));
            $lost[$lostKey]['photo'] = asset('storage/' . $lostValue->photo);
        }

        foreach($recovered as $recoveredKey => $recoveredValue) {
            $recovered[$recoveredKey]['datecreated'] = date('d/m/Y', strtotime($recoveredValue->datecreated));
            $recovered[$recoveredKey]['photo'] = asset('storage/' . $recoveredValue->photo);
        }

        $array['recovered'] = $recovered;
        $array['lost'] = $lost;

        return $array;
    }

    public function insert(Request $req)
    {
        $array = ['error' => ''];

        $validator = Validator::make($req->all(), [
            'description' => 'required',
            'where' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        if(!$validator->fails()){
            $file = $req->file('photo')->store('public');
            $file = explode('public/', $file);
            $file = $file[1];

            FoundAndLost::create([
                'description' => $req['description'],
                'where' => $req['where'],
                'photo' => $file,
                'status' => 'LOST',
                'datecreated' => date('Y-m-d H:i:s'),
            ]);
        }else{
            $array['error'] = $validator->errors()->first();

            return $array;
        }

        return $array;
    }

    public function update($id, Request $req)
    {
        $array = ['error' => ''];

        $status = $req['status'];

        if($status && in_array($status, ['lost', 'recovered'])){
            $item = FoundAndLost::find($id);

            if($item){
                $item->status = strtoupper($status);
                $item->save();
            }else{
                $array['error'] = 'Item não encontrado!';

                return $array;
            }
        }else{
            $array['error'] = 'Status inválido';

            return $array;
        }

        return $array;
    }
}
