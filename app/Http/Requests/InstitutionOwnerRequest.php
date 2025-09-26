<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstitutionOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $institutionId = $this->user()?->institution?->id;
        $userId = $this->user()?->id;

        return [
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_phone' => ['required', 'string', 'phone_br'],
            'owner_cpf' => [
                'required',
                'string',
                'cpf',
                'regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/',
                Rule::unique('users', 'cpf')->ignore($userId),
            ],
            'owner_birth_date' => ['required', 'date', 'before:today'],
            'owner_birthplace' => ['required', 'string', 'max:255'],
            'owner_nationality' => ['required', 'string', 'max:255'],
            'owner_father_name' => ['required', 'string', 'max:255'],
            'owner_mother_name' => ['required', 'string', 'max:255'],
            'owner_rg' => ['required', 'string', 'max:50'],
            'owner_rg_issuer' => ['required', 'string', 'max:50'],
            'owner_gender' => ['required', 'string', Rule::in(config('people.genders'))],
            'owner_marital_status' => ['required', 'string', Rule::in(config('people.marital_statuses'))],
            'owner_profession' => ['required', 'string', 'max:120'],
            'owner_street' => ['required', 'string', 'max:255'],
            'owner_number' => ['required', 'string', 'max:20'],
            'owner_complement' => ['nullable', 'string', 'max:255'],
            'owner_district' => ['required', 'string', 'max:255'],
            'owner_city' => ['required', 'string', 'max:255'],
            'owner_uf' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'owner_cep' => ['required', 'string', 'cep'],
            'institution_name' => ['required', 'string', 'max:255'],
            'institution_document' => [
                'required',
                'string',
                'regex:/^(\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}|\d{3}\.\d{3}\.\d{3}\-\d{2})$/',
                Rule::unique('institutions', 'document')->ignore($institutionId),
            ],
            'institution_email' => [
                'required',
                'email:rfc,dns',
                Rule::unique('institutions', 'email')->ignore($institutionId),
            ],
            'institution_phone' => ['required', 'string', 'phone_br'],
            'institution_street' => ['required', 'string', 'max:255'],
            'institution_number' => ['required', 'string', 'max:20'],
            'institution_complement' => ['nullable', 'string', 'max:255'],
            'institution_district' => ['required', 'string', 'max:255'],
            'institution_city' => ['required', 'string', 'max:255'],
            'institution_uf' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'institution_cep' => ['required', 'string', 'cep'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'owner_cpf.regex' => 'Informe o CPF no formato 000.000.000-00.',
            'institution_document.regex' => 'Informe o CNPJ ou CPF da instituicao no formato valido.',
            'institution_uf.regex' => 'Informe a UF com duas letras maiusculas.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'owner_cpf' => 'CPF do presidente',
            'owner_phone' => 'telefone do presidente',
            'institution_document' => 'CNPJ ou CPF da instituicao',
            'institution_phone' => 'telefone da instituicao',
            'owner_birth_date' => 'data de nascimento do presidente',
            'owner_birthplace' => 'naturalidade do presidente',
            'owner_nationality' => 'nacionalidade do presidente',
            'owner_father_name' => 'nome do pai do presidente',
            'owner_mother_name' => 'nome da mae do presidente',
            'owner_rg' => 'RG do presidente',
            'owner_rg_issuer' => 'orgao emissor do RG do presidente',
            'owner_gender' => 'genero do presidente',
            'owner_marital_status' => 'estado civil do presidente',
            'owner_profession' => 'profissao do presidente',
            'owner_street' => 'logradouro do presidente',
            'owner_number' => 'numero do endereco do presidente',
            'owner_complement' => 'complemento do presidente',
            'owner_district' => 'bairro do presidente',
            'owner_city' => 'cidade do presidente',
            'owner_uf' => 'UF do presidente',
            'owner_cep' => 'CEP do presidente',
            'institution_uf' => 'UF',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('institution_uf')) {
            $this->merge([
                'institution_uf' => strtoupper($this->input('institution_uf')),
            ]);
        }

        if ($this->has('owner_uf')) {
            $this->merge([
                'owner_uf' => strtoupper($this->input('owner_uf')),
            ]);
        }
    }
}
