<?php
declare(strict_types=1);

return [
    '.*?/about'        => 'FrontController:aboutView',
    '.*?/imprint'      => 'FrontController:imprintView',
    '.*?/terms'        => 'FrontController:termsView',
    '.*?/privacy'      => 'FrontController:privacyView',
    '.*?/contact'      => 'FrontController:contactView',

    '^/*$'             => 'FrontController:frontView',
    '.*?/login'        => 'FrontController:loginView',

    '.*?/api/download' => 'ApiController:downloadView',
];
