<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Exceptions\PeriodLockedException;
use Webkul\Account\Models\LockDate;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\PeriodLock;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

/**
 * Accounting plan #7, A2 (2026-11-12): once a period is locked, nothing in it
 * that is posted may change, from any path. Paying an old invoice stays
 * allowed, and drafts stay editable.
 */
beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->income = AccountHelper::account('income');
    $this->company = AccountHelper::company();
});

function lockDateTestInvoice(string $date, float $price = 100): Move
{
    $move = AccountHelper::invoice(MoveType::OUT_INVOICE, null, null, ['invoice_date' => $date, 'date' => $date]);

    AccountHelper::productLine($move, test()->income, qty: 1, priceUnit: $price);

    return AccountHelper::compute($move);
}

function lockCompanyUpTo(int $companyId, string $date): void
{
    LockDate::query()->create(['company_id' => $companyId, 'lock_date' => $date]);
}

it('refuses to post an entry dated inside a locked period', function () {
    $draft = lockDateTestInvoice('2026-01-15');

    lockCompanyUpTo($this->company->id, '2026-01-31');

    expect(fn () => AccountHelper::post($draft))
        ->toThrow(PeriodLockedException::class, 'Entries dated on or before 2026-01-31 are locked.');

    expect($draft->refresh()->state)->toBe(MoveState::DRAFT);
});

it('refuses to reset, cancel or delete a posted entry in a locked period', function () {
    $posted = AccountHelper::post(lockDateTestInvoice('2026-01-15'));

    lockCompanyUpTo($this->company->id, '2026-01-31');

    expect(fn () => AccountHelper::resetToDraft($posted->refresh()))->toThrow(PeriodLockedException::class)
        ->and(fn () => AccountHelper::cancel($posted->refresh()))->toThrow(PeriodLockedException::class)
        ->and(fn () => $posted->refresh()->delete())->toThrow(PeriodLockedException::class);

    expect($posted->refresh()->state)->toBe(MoveState::POSTED);
});

it('refuses to move a posted entry out of a locked period by re-dating it', function () {
    $posted = AccountHelper::post(lockDateTestInvoice('2026-01-15'));

    lockCompanyUpTo($this->company->id, '2026-01-31');

    expect(fn () => $posted->refresh()->update(['date' => '2026-02-10']))->toThrow(PeriodLockedException::class);
});

it('refuses to change a line amount of a posted entry in a locked period', function () {
    $posted = AccountHelper::post(lockDateTestInvoice('2026-01-15'));

    lockCompanyUpTo($this->company->id, '2026-01-31');

    $line = $posted->refresh()->lines->first();

    expect(fn () => $line->update(['debit' => (float) $line->debit + 10, 'balance' => (float) $line->balance + 10]))
        ->toThrow(PeriodLockedException::class);
});

it('still lets a locked invoice be paid in the open period', function () {
    $posted = AccountHelper::post(lockDateTestInvoice('2026-01-15', 300));

    lockCompanyUpTo($this->company->id, '2026-01-31');

    AccountHelper::pay($posted->refresh());

    expect($posted->refresh()->payment_state)->toBeIn([PaymentState::PAID, PaymentState::IN_PAYMENT]);
});

it('leaves drafts in a locked period editable and posts after the lock', function () {
    lockCompanyUpTo($this->company->id, '2026-01-31');

    $draft = lockDateTestInvoice('2026-01-20');

    $draft->update(['date' => '2026-02-05', 'invoice_date' => '2026-02-05']);

    expect(AccountHelper::post($draft->refresh())->state)->toBe(MoveState::POSTED);
});

it('records every change of the lock date as history', function () {
    $lock = app(PeriodLock::class);
    $admin = auth()->user();

    $lock->setLockDate($this->company->id, $admin, '2026-01-31');
    $lock->setLockDate($this->company->id, $admin, '2026-02-28');
    $lock->setLockDate($this->company->id, $admin, null);

    $rows = LockDate::query()->where('company_id', $this->company->id)->orderBy('id')->get();

    expect($rows)->toHaveCount(3)
        ->and($rows[1]->previous_lock_date->toDateString())->toBe('2026-01-31')
        ->and($rows[2]->lock_date)->toBeNull()
        ->and($lock->lockDateFor($this->company->id))->toBeNull();
});

it('refuses a lock date in the future', function () {
    expect(fn () => app(PeriodLock::class)->setLockDate($this->company->id, auth()->user(), now()->addDay()))
        ->toThrow(ValidationException::class, 'The lock date cannot be in the future.');
});

it('refuses a member without the unlock permission', function () {
    $clerk = CompanyHelper::actingAsCompanyUser($this->company);

    expect(fn () => app(PeriodLock::class)->setLockDate($this->company->id, $clerk, '2026-01-31'))
        ->toThrow(AuthorizationException::class, 'Only a Company Admin or an accountant with the unlock permission');
});

it('refuses an accountant of another company even with the unlock permission', function () {
    $other = CompanyHelper::company();

    $accountant = CompanyHelper::actingAsCompanyUser($other, [PeriodLock::PERMISSION]);

    expect(app(PeriodLock::class)->mayChange($accountant, $other->id))->toBeTrue()
        ->and(fn () => app(PeriodLock::class)->setLockDate($this->company->id, $accountant, '2026-01-31'))
        ->toThrow(AuthorizationException::class);
});

it('does not touch a company without a lock', function () {
    $posted = AccountHelper::post(lockDateTestInvoice('2020-01-15'));

    expect(AccountHelper::resetToDraft($posted->refresh())->state)->toBe(MoveState::DRAFT);
});
