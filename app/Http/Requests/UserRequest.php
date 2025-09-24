<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Apenas administradores podem gerenciar usuários
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => ['required', 'string', 'max:255'],
            'saram' => [
                'required',
                'string',
                'size:7',
                'regex:/^\d{7}$/',
                Rule::unique('users', 'saram')->ignore($userId)
            ],

            'role' => ['required', 'in:admin,voter'],
            'password' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'password_confirmation' => [
                $isUpdate && $this->filled('password') ? 'required' : 'nullable',
                'string'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'saram.required' => 'O SARAM é obrigatório.',
            'saram.size' => 'O SARAM deve ter exatamente 7 dígitos.',
            'saram.regex' => 'O SARAM deve conter apenas números.',
            'saram.unique' => 'Este SARAM já está em uso.',

            'role.required' => 'O papel do usuário é obrigatório.',
            'role.in' => 'O papel deve ser admin ou voter.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password_confirmation.required' => 'A confirmação da senha é obrigatória.'
        ];
    }
}
