<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function unauthorized()
    {
        return response()->json([
            'error' => 'Não autorizado!'
        ], 401);
    }

    public function register(Request $req)
    {
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

            $userLogged = auth()->user();
            $array['user'] = $userLogged;

            $properties = Unit::select(['id', 'name'])
            ->where('id_owner', $userLogged['id'])
            ->get();

            $array['user']['properties'] = $properties;

        }else{
            $array['error'] = 'Não foi possível cadastrar o usuário!';

            return $array;
        }

        return $array;
    }

    public function login(Request $req)
    {
        $array = ['error' => ''];

        $validator = Validator::make($req->all(), [
            'cpf' => 'required|digits:11',
            'password' => 'required'
        ]);

        if (!$validator->fails()) {
            $cpf = $req['cpf'];
            $password = $req['password'];

            if($password == '' || $cpf == ''){
                $array['error'] = 'CPF ou senha são obrigatórios!';

                return $array;
            }

            $token = auth()->attempt([
                'cpf' => $req['cpf'],
                'password' => $req['password']
            ]);

            if(!$token){
                $array['error'] = 'CPF e/ou senha estão errados!';
                return $array;
            }

            $array['token'] = $token;

            $userLogged = auth()->user();
            $array['user'] = $userLogged;

            $properties = Unit::select(['id', 'name'])
            ->where('id_owner', $userLogged['id'])
            ->get();

            $array['user']['properties'] = $properties;
        }else{
            $array['error'] = $validator->errors()->first();

            return $array;
        }

        return $array;
    }

    public function validateToken()
    {
        $array = ['error' => ''];

        $userLogged = auth()->user();
        $array['user'] = $userLogged;

        $properties = Unit::select(['id', 'name'])
        ->where('id_owner', $userLogged['id'])
        ->get();

        $array['user']['properties'] = $properties;

        return $array;
    }

    public function logout()
    {
        $array = ['error' => ''];

        auth()->logout();

        return $array;
    }
}
