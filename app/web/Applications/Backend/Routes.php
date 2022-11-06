<?php
declare(strict_types=1);

use phpOMS\Router\RouteVerb;

return [
    '^/*$'             => [
        [
            'dest' => '\Controllers\BackendController:dashboardView',
            'verb' => RouteVerb::GET,
        ],
    ],

    '^/admin/organizations$'             => [
        [
            'dest' => '\Controllers\BackendController:adminOrganizationsView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/admin/users$'             => [
        [
            'dest' => '\Controllers\BackendController:adminUsersView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/admin/resources$'             => [
        [
            'dest' => '\Controllers\BackendController:adminResourcesView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/admin/bills$'             => [
        [
            'dest' => '\Controllers\BackendController:adminBillsView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/admin/logs$'             => [
        [
            'dest' => '\Controllers\BackendController:adminLogsView',
            'verb' => RouteVerb::GET,
        ],
    ],

    '^/organization/settings$'             => [
        [
            'dest' => '\Controllers\BackendController:organizationSettingsView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/organization/users$'             => [
        [
            'dest' => '\Controllers\BackendController:organizationUsersView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/organization/users/\d+$'             => [
        [
            'dest' => '\Controllers\BackendController:organizationUsersEditView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/organization/resources$'             => [
        [
            'dest' => '\Controllers\BackendController:organizationResourcesView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/organization/bills$'             => [
        [
            'dest' => '\Controllers\BackendController:organizationBillsView',
            'verb' => RouteVerb::GET,
        ],
    ],

    '^/user/settings$'             => [
        [
            'dest' => '\Controllers\BackendController:userSettingsView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/user/resources$'             => [
        [
            'dest' => '\Controllers\BackendController:userResourcesView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/user/resources/create$'             => [
        [
            'dest' => '\Controllers\BackendController:userResourcesCreateView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '^/user/reports$'             => [
        [
            'dest' => '\Controllers\BackendController:userReportsView',
            'verb' => RouteVerb::GET,
        ],
    ],
];
