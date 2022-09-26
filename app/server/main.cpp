/**
 * Karaka
 *
 * @package   App
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */

#include <stdio.h>
#include <stdlib.h>

#include "cOMS/Utils/ApplicationUtils.h"
#include "DataStorage/Database/Connection/ConnectionAbstract.h"
#include "cOMS/Utils/Parser/Json.h"
#include "Stdlib/HashTable.h"

#include "Routes.h"

#ifndef OMS_DEMO
    #define OMS_DEMO false
#endif

typedef struct {
    DataStorage::Database::ConnectionAbstract *db;
    nlohmann::json config;
} App;

App app;

int main(int argc, char **argv)
{
    char *arg = Utils::ApplicationUtils::compile_arg_line(argc, argv);

    // Set program path as cwd
    char *cwd = Utils::ApplicationUtils::cwd();
    if (cwd == NULL) {
        printf("Couldn't get the CWD\n");

        return -1;
    }

    Utils::ApplicationUtils::chdir_application(cwd, argv[0]);

    // Load config
    if (!Utils::FileUtils::file_exists("config.json")) {
        Controller::ApiController::notInstalled(argc, argv);

        return -1;
    }

    // Handle routes
    Stdlib::HashTable::ht *routes = generate_routes();
    if (routes == NULL) {
        return -1;
    }

    Fptr ptr = match_route(routes, arg);

    // Dispatch found endpoint
    (*ptr)(argc, argv);

    Stdlib::HashTable::free_table(routes);
    free(routes);

    free(arg);
    arg = NULL;

    // Reset CWD (don't know if this is necessary)
    #ifdef _WIN32
        _chdir(cwd);
    #else
        chdir(cwd);
    #endif

    free(cwd);
}
