/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
#ifndef CONTROLLER_API_H
#define CONTROLLER_API_H

#include <stdio.h>
#include <stdlib.h>

#include "cOMS/Utils/ArrayUtils.h"
#include "cOMS/Utils/FileUtils.h"
#include "cOMS/Utils/WebUtils.h"
#include "cOMS/Hash/MeowHash.h"
#include "cOMS/Utils/MathUtils.h"

namespace Controller {
    namespace ApiController {
        void printHelp(int argc, char **argv)
        {
            printf("    The Online Resource Watcher app developed by jingga checks online or local resources\n");
            printf("    for changes and and informs the user about them.\n\n");
            printf("    Run: ./App ....\n\n");
            printf("    -h: Prints the help output\n");
            printf("    -v: Prints the version\n");
            printf("\n");
            printf("    Website: https://jingga.app\n");
            printf("    Copyright: jingga (c) Dennis Eichhorn\n");
        }

        void printVersion(int argc, char **argv)
        {
            printf("Version: 1.0.0\n");
        }

        void notInstalled(int argc, char **argv)
        {
            printf("No config file available, is the application installed?\n");
            printf("If not, run the application with:\n");
            printf("    --install -t 1  or\n");
            printf("    --install -t 2\n");
            printf("where 1 = web installation and 2 = local installation.\n\n");
            printf("Usually, '-t 2' is necessary if you see this message since the web\n");
            printf("installation is performed in the web installer as described in the README.\n");
        }

        void checkResources(int argc, char **argv)
        {
            unsigned long long resourceId = atoll(Utils::ArrayUtils::get_arg("-r", argv, argc));

            // @todo handle resources
            // load config
            // get resources
                // active
                // last check older than 23 h
            // check if resource changed
                // save new version
                // find differences
            // inform users

            //Resource res[10];
        }

        inline
        bool isResourceDateModified(char *filename, time_t lastChange)
        {
            return oms_abs(Utils::FileUtils::last_modification(filename) - lastChange) > 1;
        }

        inline
        bool hasResourceContentChanged(Utils::FileUtils::file_body f1, Utils::FileUtils::file_body f2)
        {
            Hash::Meow::meow_u128 h1 = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, f1.size, f1.content);
            Hash::Meow::meow_u128 h2 = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, f2.size, f2.content);

            return Hash::Meow::MeowHashesAreEqual(h1, h2);
        }

        Utils::FileUtils::file_body hasChanged(char *oldResource, char *newResource, time_t lastChange)
        {
            char *t;
            int length = 0;

            for (t = newResource; *t != '\0' && length < 7; ++t) {
                ++length;
            }

            Utils::FileUtils::file_body f1;
            Utils::FileUtils::file_body f2;

            bool isFileModified = false;
            if (length > 5
                && (strncmp(newResource, "https:", 6) || strncmp(newResource, "www.", 4))
            ) {
                // web resource
                f1 = Utils::FileUtils::read_file(oldResource);
                f2 = Utils::WebUtils::download(newResource);
            } else {
                // local resource
                isFileModified = isResourceDateModified(oldResource, lastChange);
                if (isFileModified) {
                    f1 = Utils::FileUtils::read_file(oldResource);
                    f2 = Utils::FileUtils::read_file(newResource);
                }
            }

            bool hasChanged = isFileModified || hasResourceContentChanged(f1, f2);

            free(f1.content);
            f1.size = -1;

            if (hasChanged) {
                free(f2.content);
                f2.size = -1;
            }

            return f2;
        }

        void saveResourceChange(char *url, char *oldResource)
        {
            Utils::FileUtils::file_body dowloadData = Utils::WebUtils::download(url);

            Utils::FileUtils::file_body fileData = Utils::FileUtils::read_file(oldResource);
        }
    }
}

#endif