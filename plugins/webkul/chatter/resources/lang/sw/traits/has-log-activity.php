<?php

return [
    'activity-log-failed' => [
        'events' => [
            'created'      => 'mpya :model ilikuwa imeundwa',
            'updated'      => ':model ilikuwa imesasishwa',
            'deleted'      => ':model ilikuwa imefutwa',
            'soft-deleted' => ':model ilikuwa soft imefutwa',
            'hard-deleted' => ':model ilikuwa permanently imefutwa',
            'restored'     => ':model ilikuwa restored',
        ],
    ],
];
