/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
#ifndef ROUTES_H
#define ROUTES_H

#include <stdio.h>
#include <stdlib.h>

#include "Controller/ApiController.h"
#include "Controller/InstallController.h"
#include "cOMS/Router/Router.h"

Router::Router generate_routes()
{
    Router::Router router = Router::create_router(4);

    Router::set(&router, "^.*?\\-h *.*$", (void *) &Controller::ApiController::printHelp);
    Router::set(&router, "^.*?\\-v *.*$", (void *) &Controller::ApiController::printVersion);
    Router::set(&router, "^.*?\\-r *.*$", (void *) &Controller::ApiController::checkResources);
    Router::set(&router, "^.*?\\-\\-install *.*$", (void *) &Controller::InstallController::installApplication);

    return router;
}

#endif