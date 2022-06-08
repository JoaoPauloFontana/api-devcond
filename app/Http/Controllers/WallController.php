<?php

namespace App\Http\Controllers;

use App\Models\Wall;
use App\Models\User;
use App\Models\WallLike;
use Illuminate\Http\Request;

class WallController extends Controller
{
    public function getAll()
    {
        $array = ['error' => '', 'list' => []];

        $userLogged = auth()->user();

        $walls = Wall::all();

        foreach($walls as $wallKey => $wallValue){
            $walls[$wallKey]['likes'] = 0;
            $walls[$wallKey]['liked'] = false;

            $likes = WallLike::where('id_wall', $wallValue['id'])
            ->count();

            $walls[$wallKey]['likes'] = $likes;

            $meLikes = WallLike::where('id_wall', $wallValue['id'])
            ->where('id_user', $userLogged['id'])
            ->count();

            if ($meLikes > 0) {
                $walls[$wallKey]['liked'] = true;
            }
        }

        $array['list'] = $walls;

        return $array;
    }

    public function like($id)
    {
        $array = ['error' => ''];

        $user = auth()->user();

        $meLikes = WallLike::where('id_wall', $id)
        ->where('id_user', $user['id'])
        ->count();

        if($meLikes > 0){
            WallLike::where('id_wall', $id)
            ->where('id_user', $user['id'])
            ->delete();

            $array['liked'] = false;
        }else{
            WallLike::insert([
                'id_wall' => $id,
                'id_user' => $user['id']
            ]);

            $array['liked'] = true;
        }

        $array['likes'] = WallLike::where('id_wall', $id)->count();

        return $array;
    }
}
