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

#include "cOMS/Utils/Parser/Json.h"
#include "Stdlib/HashTable.h"

#include "Routes.h"

#ifndef OMS_DEMO
    #define OMS_DEMO false
#endif

void parseConfigFile()
{
    FILE *fp = fopen("config.json", "r");

    nlohmann::json config = nlohmann::json::parse(fp);
}

char *compile_arg_line(int argc, char **argv)
{
    size_t max    = 512;
    size_t length = 0;
    char *arg     = (char *) calloc(max, sizeof(char));

    for (int i = 1; i < argc; ++i) {
        size_t argv_length = strlen(argv[i]);
        if (length + strlen(argv[i]) + 1 > max) {
            char *tmp = (char *) calloc(max + 128, sizeof(char));
            memcpy(tmp, arg, (length + 1) * sizeof(char));

            free(arg);
            arg  = tmp;
            max += 128;
        }

        strcat(arg, argv[i]);
        length += argv_length;
    }

    return arg;
}

int main(int argc, char **argv)
{
    char *arg = compile_arg_line(argc, argv);

    // @todo: Check is installed?
        // no? install

    // Load config
    if (!Utils::FileUtils::file_exists("config.json")) {
        printf("No config file available.");

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
    free(arg);
    arg = NULL;
}
