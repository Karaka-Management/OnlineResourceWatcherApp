/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
#ifndef CONTROLLER_API_H
#define CONTROLLER_API_H

#include <stdio.h>
#include <stdlib.h>

#include "../cOMS/Utils/ArrayUtils.h"
#include "../cOMS/Utils/FileUtils.h"
#include "../cOMS/Utils/WebUtils.h"
#include "../cOMS/Hash/MeowHash.h"
#include "../cOMS/Utils/MathUtils.h"
#include "../cOMS/Threads/Thread.h"

#include "../Models/Resource.h"
#include "../Models/ResourceType.h"

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

        typedef struct {
            Models::Resource **resources;
            int count = 0;
            int simultaneous = 0;
        } ResourcekerData;

        void onlineResourceThreaded(void *arg)
        {
            ResourcekerData *data = (ResourcekerData *) arg;

            char **urls = (char **) malloc(data->count * sizeof(char *));
            for (int i = 0; i < data->count; ++i) {
                urls[i] = data->resources[i]->uri;
            }

            Utils::FileUtils::file_body *multi = Utils::WebUtils::multi_download(urls, data->count, data->simultaneous);

            free(urls);

            // @todo: save temp version
            // @todo: if first download then also download all first level references + css references (= second level)
                // @todo: probably ignore javascript references, they are not useful for static offline comparisons!?
            // @todo: comparison with local version (only download other resources, if xPath is defined and it contains resources)
            // @todo: flag as changed/not changed

            free(data->resources);
            free(arg);
        }

        void offlineResourceThreaded(void *arg)
        {
        }

        void checkResources(int argc, char **argv)
        {
            int idLength                = 0;
            Models::Resource *resources = NULL;

            int i;
            if (Utils::ArrayUtils::has_arg("-r", argv, argc)) {
                char *resourceList       = Utils::ArrayUtils::get_arg("-r", argv, argc);
                char **resourceIdStrings = NULL;

                idLength  = Utils::StringUtils::str_split(resourceIdStrings, resourceList, ',');
                resources = (Models::Resource *) malloc(idLength * sizeof(Models::Resource));

                for (i = 0; i < idLength; ++i) {
                    resources[i].id = atoll(resourceIdStrings[i]);
                }
            } else {
                // find and load all relevant ids from the database
            }

            // @todo: also free points in resources, call Models::free_Resource(resources[i]) on every element.
            free(resources);

            // How many resources are handled in one thread
            // This must be multiplied with the thread count for the over all concurrent max downloads
            int THREAD_SIZE = app.config["app"]["resources"]["online"]["downloads"].get<int>();

            Models::Resource **onlineResources  = (Models::Resource **) malloc(oms_min(idLength, THREAD_SIZE) * sizeof(Models::Resource *));
            Models::Resource **offlineResources = (Models::Resource **) malloc(oms_min(idLength, THREAD_SIZE) * sizeof(Models::Resource *));

            int j = 0;
            int c = 0;
            int k = 0;

            for (i = 0; i < idLength; ++i) {
                if (resources[i].type == Models::ResourceType::ONLINE) {
                    onlineResources[j] = &resources[i];

                    ++j;
                } else {
                    offlineResources[k] = &resources[i];

                    ++k;
                }

                // Handle online resources in batches here:
                if (j > 0 && (j == THREAD_SIZE || i + 1 >= idLength)) {
                    ResourcekerData *data = (ResourcekerData *) malloc(sizeof(ResourcekerData));
                    data->resources       = onlineResources;
                    data->count           = j;
                    data->simultaneous    = THREAD_SIZE;

                    Threads::pool_add_work(app.pool, onlineResourceThreaded, data);

                    if (i + 1 < idLength) {
                        onlineResources = (Models::Resource **) malloc((oms_min(idLength - i, THREAD_SIZE)) * sizeof(Models::Resource *));
                        j = 0;
                    }
                }

                // Handle offline resources in batches here:
                if (k > 0 && (k == THREAD_SIZE || i + 1 >= idLength)) {
                    ResourcekerData *data = (ResourcekerData *) malloc(sizeof(ResourcekerData));
                    data->resources       = offlineResources;
                    data->count           = k;
                    data->simultaneous    = THREAD_SIZE;

                    Threads::pool_add_work(app.pool, offlineResourceThreaded, data);

                    if (i + 1 < idLength) {
                        offlineResources = (Models::Resource **) malloc((oms_min(idLength - i, THREAD_SIZE)) * sizeof(Models::Resource *));
                        k = 0;
                    }
                }
            }

            // @todo handle resources
            // load config
            // get resources
                // active
                // last check older than 23 h
            // check if resource changed
                // load new resource (save temp version)
                // find differences
                // save new version
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
            meow_u128 h1 = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, f1.size, f1.content);
            meow_u128 h2 = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, f2.size, f2.content);

            return MeowHashesAreEqual(h1, h2);
        }

        Utils::FileUtils::file_body hasChanged(char *oldResource, char *newResource, time_t lastChange)
        {
            char *t;
            int length = 0;

            for (t = newResource; *t != '\0' && length < 7; ++t) {
                ++length;
            }

            Utils::FileUtils::file_body f1 = {0};
            Utils::FileUtils::file_body f2 = {0};

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

            bool hasChanged = f1.content && f2.content && (isFileModified || hasResourceContentChanged(f1, f2));

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