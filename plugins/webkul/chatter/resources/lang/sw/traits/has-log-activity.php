<?php

return [
    'activity-log-failed' => [
        'events' => [
            'created'      => ':model mpya imeundwa',
            'updated'      => ':model imesasishwa',
            'deleted'      => ':model imefutwa',
            'soft-deleted' => ':model imehamishwa kwenye tupio',
            'hard-deleted' => ':model imefutwa kabisa',
            'restored'     => ':model imerejeshwa',
        ],
    ],
];
