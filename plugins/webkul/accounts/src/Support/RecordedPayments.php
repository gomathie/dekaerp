<?php

namespace Webkul\Account\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Payment;

/**
 * The payments that count as money received (inbound) or paid out
 * (outbound) between two dates, by payment date, in the company scope.
 *
 * One rule for the Overview cash chart (Invoices) and the cards above every
 * payments list, Invoices' and Accounting's (plan #9 R-9). It lives in
 * Accounts because both plugins depend on it and Accounting does not depend
 * on Invoices; a copy in each would let the list and the dashboard disagree.
 * In process counts with paid: the money has left the customer (see
 * OverviewCashFlowChart::dailyPayments()).
 */
class RecordedPayments
{
    public static function between(PaymentType $type, Carbon $start, Carbon $end): Builder
    {
        $query = Payment::query();

        return $query
            ->where($query->qualifyColumn('payment_type'), $type)
            ->whereIn($query->qualifyColumn('state'), [PaymentStatus::PAID, PaymentStatus::IN_PROCESS])
            ->whereBetween($query->qualifyColumn('date'), [$start->toDateString(), $end->toDateString()]);
    }
}
