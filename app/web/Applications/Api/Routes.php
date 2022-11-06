<?php
declare(strict_types=1);

use phpOMS\Router\RouteVerb;

return [
    '.*?/download' => [
        [
            'dest' => '\Controllers\ApiController:downloadView',
            'verb' => RouteVerb::GET,
        ],
    ],

    '^.*?/login(\?.*|$)' => [
        [
            'dest'       => '\Controllers\ApiController:apiLogin',
            'verb'       => RouteVerb::SET,
            'permission' => [
            ],
        ],
    ],
    '^.*?/logout(\?.*|$)' => [
        [
            'dest'       => '\Controllers\ApiController:apiLogout',
            'verb'       => RouteVerb::SET,
            'permission' => [
            ],
        ],
    ],
];
