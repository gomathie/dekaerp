<?php

namespace Webkul\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Security\Models\User;

class SetCompanyContextRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof User && $user->is_active;
    }

    public function rules(): array
    {
        return [
            'action'      => ['nullable', Rule::in(['reset'])],
            'companies'   => [Rule::requiredIf(fn (): bool => $this->input('action') !== 'reset'), 'array', 'min:1'],
            'companies.*' => ['integer', 'distinct'],
            'current'     => ['nullable', 'integer'],
        ];
    }
}
