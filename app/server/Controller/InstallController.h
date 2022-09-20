/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
#ifndef CONTROLLER_INSTALL_H
#define CONTROLLER_INSTALL_H

#include <stdio.h>
#include <stdlib.h>

#include "cOMS/Utils/Parser/Json.h"

#include "../Models/InstallType.h"

namespace Controller {
    namespace InstallController {
        void installApplication(int argc, char **argv)
        {
            // @todo handle install
            // create config
            // check install type
                // web = copy config from web
                // local
                    // create sqlite db
                    // create config from template
        }

        void install(Models::InstallType type = Models::InstallType::LOCAL)
        {
            if (type == Models::InstallType::LOCAL) {
                // create sqlite database

            } else {

            }

            // create config file
            nlohmann::json config;
        }

        void parseConfigFile()
        {
            FILE *fp = fopen("config.json", "r");

            nlohmann::json config = nlohmann::json::parse(fp);
        }
    }
}

#endif