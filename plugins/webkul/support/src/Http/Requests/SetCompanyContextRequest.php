<?php

namespace Webkul\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

class SetCompanyContextRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        // Refused here, not only hidden in the UI: the switch is a plain POST,
        // so anyone could send it by hand if the button were the only guard.
        return $user instanceof User
            && $user->is_active
            && app(CompanyContext::class)->canSwitchCompanies();
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
