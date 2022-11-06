<?php
declare(strict_types=1);

use phpOMS\Router\RouteVerb;

return [
    '.*?/imprint'      => [
        [
            'dest' => '\Controllers\FrontController:imprintView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/terms'        => [
        [
            'dest' => '\Controllers\FrontController:termsView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/privacy'      => [
        [
            'dest' => '\Controllers\FrontController:privacyView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/contact'      => [
        [
            'dest' => '\Controllers\FrontController:contactView',
            'verb' => RouteVerb::GET,
        ],
    ],
];
