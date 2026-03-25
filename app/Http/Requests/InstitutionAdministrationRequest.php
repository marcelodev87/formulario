<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstitutionAdministrationRequest extends FormRequest
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
            'dissolution_mode' => ['required', Rule::in(['president', 'members'])],
            'governance_model' => ['required', Rule::in(['episcopal', 'presbiterial', 'congregacional'])],
            'president_term_type' => ['required', Rule::in(['indefinite', 'years'])],
            'president_term_years' => ['nullable', 'integer', 'min:1', 'max:50'],
            'board_term_years' => ['required', 'integer', 'min:1', 'max:50'],
            'ordination_decision' => ['required', Rule::in(['president', 'leadership', 'members'])],
            'financial_responsible' => ['required', Rule::in(['president', 'president_treasurer'])],
            'ministerial_roles' => ['nullable', 'array'],
            'ministerial_roles.*' => [Rule::in([
                'apostolo',
                'bispo',
                'diacono',
                'dirigente',
                'evangelista',
                'missionario',
                'obreiro',
                'pastor',
                'presbitero',
            ])],
            'stipend_policy' => ['required', Rule::in(['all_pastors', 'only_president', 'none'])],
            'additional_info' => ['nullable', 'string', 'max:5000'],
            'name_option_1' => ['nullable', 'string', 'max:255'],
            'name_option_2' => ['nullable', 'string', 'max:255'],
            'name_option_3' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('president_term_type') === 'years' && empty($this->input('president_term_years'))) {
                $validator->errors()->add('president_term_years', 'Informe o tempo de mandato do presidente em anos.');
            }

            if ($this->input('president_term_type') === 'indefinite') {
                $this->merge(['president_term_years' => null]);
            }
        });
    }
}