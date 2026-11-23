<?php

return [
    'activity-log-failed' => [
        'events' => [
            'created'      => 'новое :model был создано',
            'updated'      => ':model был обновлено',
            'deleted'      => ':model был удалено',
            'soft-deleted' => ':model был soft удалено',
            'hard-deleted' => ':model был permanently удалено',
            'restored'     => ':model был restored',
        ],
    ],
];
