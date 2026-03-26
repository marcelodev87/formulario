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

        return [
            'institution_name' => ['required', 'string', 'max:255'],
            'institution_document' => [
                'required',
                'string',
                'cpf',
                Rule::unique('institutions', 'document')->ignore($institutionId),
            ],
            'institution_email' => [
                'required',
                'email:rfc,dns',
                Rule::unique('institutions', 'email')->ignore($institutionId),
            ],
            'institution_phone' => ['required', 'string', 'phone_br'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'institution_document.cpf' => 'Informe o CPF da instituicao no formato valido.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'institution_document' => 'CNPJ ou CPF da instituicao',
            'institution_phone' => 'telefone da instituicao',
        ];
    }
}
