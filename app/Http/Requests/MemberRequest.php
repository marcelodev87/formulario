<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'birthplace' => ['required', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'cpf',
                'regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/',
                // sem unique
            ],
            'rg' => ['required', 'string', 'max:50'],
            'rg_issuer' => ['required', 'string', 'max:50'],
            'role' => [
                'required',
                Rule::in([
                    'Presidente',
                    'Vice Presidente',
                    'Tesoureiro',
                    'Segundo Tesoureiro',
                    'Secretario',
                    'Segundo Secretario',
                    'Conselho Fiscal',
                ]),
            ],
            'gender' => ['required', 'string', Rule::in(config('people.genders'))],
            'marital_status' => ['required', 'string', Rule::in(config('people.marital_statuses'))],
            'profession' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns'],
            'phone' => ['required', 'string', 'phone_br'],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'uf' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'cep' => ['required', 'string', 'cep'],
        ];
    }
}
