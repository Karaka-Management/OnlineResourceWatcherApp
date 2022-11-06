<?php
declare(strict_types=1);

use phpOMS\Router\RouteVerb;

return [
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
