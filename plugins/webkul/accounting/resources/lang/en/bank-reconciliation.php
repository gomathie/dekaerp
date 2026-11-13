<?php

return [
    'statement-name' => 'Statement :from to :to',
    'entry-label'    => 'Bank statement line',

    'fields' => [
        'date'   => 'date',
        'amount' => 'amount',
    ],

    'errors' => [
        'not-allowed'            => 'You cannot reconcile this bank account.',
        'not-a-bank-journal'     => 'Choose a bank or cash journal.',
        'foreign-currency'       => 'This journal uses a foreign currency. Only bank accounts in the company currency can be reconciled for now.',
        'missing-bank-account'   => 'This journal has no bank account set. Set its default account first.',
        'empty-file'             => 'The file has no statement lines.',
        'too-many-rows'          => 'The file has more than :max lines. Split it into smaller statements.',
        'bad-row'                => 'Line :row: the :field cannot be read. Check the column numbers and the date format.',
        'already-reconciled'     => 'This line is already reconciled.',
        'payment-does-not-match' => 'That payment does not match this line: it must be in the same bank journal, for the same amount and direction, and not already matched.',
        'account-not-allowed'    => 'That account cannot be used here. Customer and supplier accounts are settled by matching a payment.',
    ],
];
