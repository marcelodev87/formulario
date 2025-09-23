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
        $member = $this->route('member');
        $invite = $this->route('invite');
        $institutionId = $member?->institution_id
            ?? $invite?->institution_id
            ?? $this->user()?->institution?->id;

        $emailRule = Rule::unique('members', 'email');
        $cpfRule = Rule::unique('members', 'cpf');

        if ($institutionId) {
            $emailRule = $emailRule->where(fn ($query) => $query->where('institution_id', $institutionId));
            $cpfRule = $cpfRule->where(fn ($query) => $query->where('institution_id', $institutionId));
        }

        if ($member) {
            $emailRule = $emailRule->ignore($member->id);
            $cpfRule = $cpfRule->ignore($member->id);
        }

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
                $cpfRule,
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
            'marital_status' => ['required', 'string', 'max:100'],
            'profession' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', $emailRule],
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
