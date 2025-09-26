<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchLeaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $process = $this->route('process');
        $location = $process?->location;
        $leader = $location?->leader;
        $locationId = $location?->id;
        $leaderId = $leader?->id;

        $emailRule = Rule::unique('leaders', 'email');
        $cpfRule = Rule::unique('leaders', 'cpf');

        if ($locationId) {
            $emailRule = $emailRule->where(fn ($query) => $query->where('location_id', $locationId));
            $cpfRule = $cpfRule->where(fn ($query) => $query->where('location_id', $locationId));
        }

        if ($leaderId) {
            $emailRule = $emailRule->ignore($leaderId);
            $cpfRule = $cpfRule->ignore($leaderId);
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
            'gender' => ['required', 'string', Rule::in(config('people.genders'))],
            'marital_status' => ['required', 'string', Rule::in(config('people.marital_statuses'))],
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

    public function messages(): array
    {
        return [
            'cpf.regex' => 'Informe o CPF no formato 000.000.000-00.',
            'uf.regex' => 'Informe a UF com duas letras maiusculas.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('uf')) {
            $this->merge([
                'uf' => strtoupper((string) $this->input('uf')),
            ]);
        }
    }
}
