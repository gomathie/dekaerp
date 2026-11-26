<?php

namespace Webkul\Account\Services;

use Carbon\CarbonInterface;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;

/**
 * An expense paid straight from a bank or cash account, as one posted
 * journal entry: each expense account debited, the account the money left
 * credited, on the expense date, with the vendor on every line.
 *
 * One builder for every caller (user, 2026-11-26): the Zoho Books import
 * uses it now, and the planned Expenses screen is to post through it, so
 * both write the same entry. The caller chooses the journal: the import
 * uses Miscellaneous Operations, because Move::getValidJournalTypes() keeps
 * bank and cash journals for payments and statement lines. The bank or cash
 * balance is right either way: it is the balance of the account credited.
 *
 * Totals and posting are DEKA's own (computeAccountMove, confirmMove), so the
 * lock date and the balance check apply as for an entry typed by hand.
 */
class ExpenseEntry
{
    /**
     * @param  list<array{account_id: int, amount: float, name: string}>  $lines
     */
    public function post(
        int $companyId,
        int $currencyId,
        int $journalId,
        CarbonInterface|string $date,
        array $lines,
        int $paidFromAccountId,
        string $paidFromLabel,
        ?int $partnerId = null,
        ?string $reference = null,
        ?string $narration = null,
        ?int $creatorId = null,
    ): Move {
        $move = Move::create([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'company_id'  => $companyId,
            'journal_id'  => $journalId,
            'currency_id' => $currencyId,
            'partner_id'  => $partnerId,
            'date'        => $date,
            'reference'   => $reference,
            'narration'   => $narration,
            'creator_id'  => $creatorId,
        ]);

        $total = 0.0;

        foreach ($lines as $line) {
            $amount = round((float) $line['amount'], 2);
            $total += $amount;

            $this->line($move, (int) $line['account_id'], $partnerId, $line['name'], $amount, 0.0, $creatorId);
        }

        $this->line($move, $paidFromAccountId, $partnerId, $paidFromLabel, 0.0, round($total, 2), $creatorId);

        return AccountFacade::confirmMove(AccountFacade::computeAccountMove($move->refresh()));
    }

    protected function line(Move $move, int $accountId, ?int $partnerId, string $name, float $debit, float $credit, ?int $creatorId): void
    {
        $move->lines()->create([
            'name'            => $name,
            'date'            => $move->date,
            'creator_id'      => $creatorId,
            'parent_state'    => $move->state,
            // Set explicitly: a line saved without a display type is costed
            // through the "default" branch of MoveLine::computeAccountId(),
            // which replaces account_id with the journal's default account,
            // and MoveCalculator zeroes the totals of anything not PRODUCT.
            'display_type'    => DisplayType::PRODUCT,
            'account_id'      => $accountId,
            'partner_id'      => $partnerId,
            'company_id'      => $move->company_id,
            'currency_id'     => $move->currency_id,
            'debit'           => $debit,
            'credit'          => $credit,
            'balance'         => $debit - $credit,
            'amount_currency' => $debit - $credit,
        ]);
    }
}
