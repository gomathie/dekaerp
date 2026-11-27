<?php

return [
    'title'       => 'Send reminder',
    'heading'     => 'Send a reminder?',
    'description' => 'This sends the invoice reminder to the customer.',
    'saved'       => 'Reminder sent.',
    'subject'     => 'Reminder: invoice :number',
    'greeting'    => 'Dear :name,',
    'body'        => 'Invoice :number from :company for :amount is overdue. Please arrange payment.',
    'not-overdue' => 'Only an overdue customer invoice can be reminded.',
    'no-email'    => 'This customer has no email address.',
    'company'     => 'You cannot remind an invoice in that company.',
];
