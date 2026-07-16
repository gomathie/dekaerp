<?php

namespace Webkul\Support\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Webkul\Support\Services\CurrentCompany;

class CompanySwitcher extends Component
{
    public function switchCompany(int|string $companyId): void
    {
        if (! CurrentCompany::set($companyId)) {
            return;
        }

        $this->redirect(request()->header('Referer') ?: url()->current());
    }

    public function render(): View
    {
        return view('support::livewire.company-switcher', [
            'companies'     => CurrentCompany::allowedCompanies(),
            'selection'     => CurrentCompany::selection(),
            'activeCompany' => CurrentCompany::company(),
        ]);
    }
}
