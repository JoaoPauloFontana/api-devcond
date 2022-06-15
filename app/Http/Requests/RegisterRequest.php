<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome é obrigatório!',
            'email.required' => 'O e-mail é obrigatório!',
            'email.email' => 'O e-mail informado não é válido!',
            'email.unique' => 'O e-mail informado já está cadastrado!',
            'cpf.required' => 'O CPF é obrigatório!',
            'cpf.digits' => 'O CPF informado não é válido!',
            'cpf.unique' => 'O CPF informado já está cadastrado!',
            'password.required' => 'A senha é obrigatória!',
            'password_confirm.required' => 'A confirmação de senha é obrigatória!',
            'password_confirm.same' => 'A confirmação de senha não confere!'
        ];
    }
}
