<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstitutionPropertyRequest extends FormRequest
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
        return [
            'iptu_registration' => ['nullable', 'string', 'max:120'],
            'built_area_sqm' => ['nullable', 'numeric', 'min:0'],
            'land_area_sqm' => ['nullable', 'numeric', 'min:0'],
            'tenure_type' => ['required', 'string', 'in:own,rented'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'floors' => ['nullable', 'integer', 'min:0'],
            'activity_floor' => ['nullable', 'string', 'max:120'],
            'property_use' => ['nullable', 'string', 'max:120'],
            'property_section' => ['nullable', 'string', 'max:120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tenure_type.in' => 'Selecione se o imovel e proprio ou alugado.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'iptu_registration' => 'numero de inscricao do IPTU',
            'built_area_sqm' => 'area construida',
            'land_area_sqm' => 'area total do terreno',
            'tenure_type' => 'tipo de posse do imovel',
            'capacity' => 'capacidade do imovel',
            'floors' => 'quantidade de andares',
            'activity_floor' => 'andar de funcionamento',
            'property_use' => 'uso do imovel',
            'property_section' => 'identificacao da unidade',
        ];
    }
}
