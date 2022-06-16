<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileWarningRequest;
use App\Http\Requests\WarningRequest;
use App\Models\Unit;
use App\Models\Warning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WarningController extends Controller
{
    public function getMyWarnings(Request $req)
    {
        $array = ['error' => ''];

        $property = $req['id_unit'];

        if($property){
            $userLogged = auth()->user();

            $unit = Unit::where('id', $property)
            ->where('id_owner', $userLogged['id'])
            ->count();

            if($unit > 0){
                $warnings = Warning::where('id_unit', $property)
                    ->orderBy('datecreated', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->get();

                    foreach($warnings as $warnKey => $warnValue){
                        $warnings[$warnKey]['datecreated'] = date('d/m/Y', strtotime($warnValue['datecreated']));
                        $photoList = [];

                        $photos = explode(',', $warnValue['photos']);

                        foreach($photos as $photo){
                            if(!empty($photo)){
                                $photoList[] = asset('storage/'.$photo);
                            }
                        }

                        $warnings[$warnKey]['photos'] = $photoList;


                    }

                    $array['list'] = $warnings;
            }else{
                $array['error'] = 'Esta propriedade não é sua.';
            }
        }else{
            $array['error'] = 'A propriedade é necessária!';
        }

        return $array;
    }

    public function addWarningFile(Request $req)
    {
        $array = ['error' => ''];

        $warning = $req->all();

        if($warning){
            $file = $req->file('photo')->store('public');

            $array['photo'] = asset(Storage::url($file));
        }else{
            $array['error'] = 'Adicione uma foto!';
        }

        return $array;
    }

    public function setWarning(Request $req)
    {
        $array = ['error' => ''];

        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'id_unit' => 'required',
        ]);

        if(!$validator->fails()){
            $list = $req['list'];

            $newWarn = new Warning();
            $newWarn->id_unit = $req['id_unit'];
            $newWarn->title = $req['title'];
            $newWarn->status = 'IN_REVIEW';
            $newWarn->datecreated = date('Y-m-d');

            if($list && is_array($list)){
                $photos = [];

                foreach($list as $listItem){
                    $url = explode('/', $listItem);

                    $photos[] = end($url);
                }

                $newWarn->photos = implode(',', $photos);
            }else{
                $newWarn->photos = '';
            }

            $newWarn->save();
        }else{
            $array['error'] = $validator->errors()->first();

            return $array;
        }

        return $array;
    }
}
