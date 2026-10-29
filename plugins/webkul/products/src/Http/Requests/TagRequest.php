<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tagId = $this->route('id');

        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? ['sometimes', 'required'] : ['required'];

        $rules = [
            // The database and TagResource both scope uniqueness by company;
            // a global API rule would reject a name another client may use.
            'name'  => [
                ...$requiredRule,
                'string',
                'max:255',
                Rule::unique('products_tags', 'name')
                    ->where(fn ($query) => $query->where('company_id', current_company_id()))
                    ->ignore($tagId),
            ],
            'color' => ['nullable', 'string', 'max:7'],
        ];

        return $rules;
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Tag name (max 255 characters).',
                'example'     => 'Featured',
            ],
            'color' => [
                'description' => 'Tag color in hex format (max 7 characters).',
                'example'     => '#FF5733',
            ],
        ];
    }
}
