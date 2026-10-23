<?php

return [
    'errors' => [
        'empty'           => 'The file has no rows.',
        'no-columns'      => 'No recognised columns were found in the heading row. Expected columns: :columns.',
        'missing-name'    => 'The file must have a "name" column.',
        'max-rows'        => 'Only the first :max rows were imported. Split the file and upload the rest.',
        'row'             => 'Row :row: :reason',
        'formula'         => 'Row :row: the ":column" value starts with a character a spreadsheet reads as a formula. Remove it and upload again.',
        'duplicate-email' => 'Row :row: a contact with the email :email already exists.',
        'unknown-country' => 'Row :row: no country matches ":country".',
        'unknown-state'   => 'Row :row: no state matches ":state".',
        'unknown-parent'  => 'Row :row: no existing contact matches the company ":parent".',
    ],
];
