<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function unauthorized(){
        return response()->json([
            'error' => 'Não autorizado!'
        ], 401);
    }

    public function register(RegisterRequest $req){
        $array = ['error' => ''];

        $users = $req->all();

        if ($users) {
            $hash = password_hash($req['password'], PASSWORD_DEFAULT);

            $newUser = new User();
            $newUser->name = $req['name'];
            $newUser->email = $req['email'];
            $newUser->cpf = $req['cpf'];
            $newUser->password = $hash;
            $newUser->save();

            $token = auth()->attempt([
                'cpf' => $req['cpf'],
                'password' => $req['password']
            ]);

            if(!$token){
                $array['error'] = 'Ocorreu um erro!';
                return $array;
            }

            $array['token'] = $token;

            $user = auth()->user();
            $array['user'] = $user;

            $properties = Unit::select(['id', 'name'])
            ->where('id_owner', $user['id'])
            ->get();

            $array['user']['properties'] = $properties;

        }else{
            $array['error'] = 'Não foi possível cadastrar o usuário!';
        }

        return $array;
    }
}
