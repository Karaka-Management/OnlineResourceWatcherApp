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
#include <iostream>
#include <regex>

#include "Controller/ApiController.h"
#include "Controller/InstallController.h"
#include "cOMS/Stdlib/HashTable.h"

typedef void (*Fptr)(int, char **);

Stdlib::HashTable::ht *generate_routes()
{
    Stdlib::HashTable::ht *table = Stdlib::HashTable::create_table(4, true);
    if (table == NULL) {
        return NULL;
    }

    Stdlib::HashTable::set_entry(table, "^.*?\\-h *.*$", (void *) &Controller::ApiController::printHelp);
    Stdlib::HashTable::set_entry(table, "^.*?\\-v *.*$", (void *) &Controller::ApiController::printVersion);
    Stdlib::HashTable::set_entry(table, "^.*?\\-r *.*$", (void *) &Controller::ApiController::checkResources);

    Stdlib::HashTable::set_entry(table, "^.*?\\-\\-install *.*$", (void *) &Controller::InstallController::installApplication);

    return table;
}

Fptr match_route(Stdlib::HashTable::ht *routes, char *uri)
{
    Fptr ptr = NULL;
    Stdlib::HashTable::it itr = Stdlib::HashTable::table_iterator(routes);

    std::regex regex;
    std::cmatch match;

    while (Stdlib::HashTable::next(&itr)) {
        regex = std::regex(itr.key);

        bool status = std::regex_search(uri, match, regex);
        if (status) {
            ptr = (Fptr) itr.value;
        }
    }

    // No endpoint found
    if (ptr == NULL) {
        ptr = &Controller::ApiController::printHelp;
    }

    return ptr;
}

#endif