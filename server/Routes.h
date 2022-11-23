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

Router generate_routes()
{
    Router router = Router::create_router(4);

    router.set("^.*?\\-h *.*$", (void *) &Controller::ApiController::printHelp);
    router.set("^.*?\\-v *.*$", (void *) &Controller::ApiController::printVersion);
    router.set("^.*?\\-r *.*$", (void *) &Controller::ApiController::checkResources);
    router.set("^.*?\\-\\-install *.*$", (void *) &Controller::InstallController::installApplication);

    return router;
}

#endif