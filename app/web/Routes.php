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

    '^/*$'             => [
        [
            'dest' => '\Controllers\FrontController:frontView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/features'        => [
        [
            'dest' => '\Controllers\FrontController:featureView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/pricing'        => [
        [
            'dest' => '\Controllers\FrontController:pricingView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/signup'        => [
        [
            'dest' => '\Controllers\FrontController:signupView',
            'verb' => RouteVerb::GET,
        ],
    ],
];
