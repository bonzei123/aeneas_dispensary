<?php

declare(strict_types=1);

return [
    'routes' => [
        ['name' => 'dispensary#index', 'url' => '/dispensary', 'verb' => 'GET'],
        ['name' => 'dispensary#addAmount', 'url' => '/dispensary/add', 'verb' => 'POST'],
        ['name' => 'dispensary#adminIndex', 'url' => '/admin', 'verb' => 'GET'],
        ['name' => 'dispensary#updateAbgabe', 'url' => '/admin/update', 'verb' => 'POST'],
    ]
];
