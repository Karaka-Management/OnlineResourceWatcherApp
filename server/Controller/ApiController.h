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
#include "../cOMS/DataStorage/Database/Mapper/DataMapperFactory.h"

#include "../Models/Resource.h"
#include "../Models/ResourceMapper.h"
#include "../Models/ResourceType.h"
#include "../Models/ResourceStatus.h"

namespace Controller {
    namespace ApiController {
        static Application::ApplicationAbstract *app = NULL;

        void printHelp(int argc, char **argv)
        {
            printf("    The Online Resource Watcher app developed by jingga checks online or local resources\n");
            printf("    for changes and informs the user about them.\n\n");
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
        } ThreadData;

        void onlineResourceThreaded(void *arg)
        {
            ThreadData *data = (ThreadData *) arg;

            char **urls = (char **) malloc(data->count * sizeof(char *));
            int i;

            for (i = 0; i < data->count; ++i) {
                urls[i] = data->resources[i]->uri;
            }

            Utils::FileUtils::file_body *multi = Utils::WebUtils::multi_download(
                urls,
                data->count,
                5,
                0,
                (ResourceTypes *) {.size = 4, .resources = {"jpg", "png", "gif", "css"}}
            );
            // @todo: flag for downloading resources types (e.g. js, css, img)
            // @todo: limit filesize to abort downloading large files

            if (urls != NULL) {
                free(urls);
            }

            bool hasChanged = false;
            meow_u128 tempHash;

            for (i = 0; i < data->count; ++i) {
                // cachedSource = Utils::FileUtils::read_file(data->resources[i]->last_version_path);

                tempHash = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, multi[i].size, multi[i].content);
                if (hasChanged = (strcmp((char *) Hash::Meow::MeowStringify(tempHash), data->resources[i]->hash) == 0)) {
                    // @todo: do stuff because of change!!!
                        // create website image with pdf?
                        // inform users
                }

                if (hasChanged || data->resources[i]->checked_at == 0) {
                    // @todo: download references + css references (= second level)
                    // @todo: probably ignore javascript references, they are not useful for static offline comparisons!?

                    data->resources[i]->hash = (char *) Hash::Meow::MeowStringify(tempHash);
                    data->resources[i]->last_version_date = time(0);

                    // @todo: store new version
                    // @todo: check if older version can/needs to be removed

                    data->resources[i]->last_version_path = (char *) "PATH_TO_NEWEST_VERSION\0";
                    data->resources[i]->hash = (char *) "Hash_of_new_version\0";
                }

                data->resources[i]->checked_at = time(0);

                // @todo: update data
                //DataStorage::Database::DataMapperFactory::update(&Models::ResourceMapper)
                //    ->execute(data->resources[i]);

                Models::free_Resource(data->resources[i]);
            }

            if (data->resources != NULL) {
                free(data->resources);
            }

            if (arg != NULL) {
                free(arg);
            }
        }

        void offlineResourceThreaded(void *arg)
        {
        }

        void checkResources(int argc, char **argv)
        {
            int idLength                = 0;
            Models::Resource *resources = NULL; // Elements freed in the threads

            int i;
            if (Utils::ArrayUtils::has_arg("-r", argv, argc)) {
                char *resourceList       = Utils::ArrayUtils::get_arg("-r", argv, argc);
                char **resourceIdStrings = NULL;

                idLength  = Utils::StringUtils::str_split(resourceIdStrings, resourceList, ',');
                resources = (Models::Resource *) malloc(idLength * sizeof(Models::Resource));

                for (i = 0; i < idLength; ++i) {
                    resources[i].id = atoll(resourceIdStrings[i]);
                }

                if (resourceIdStrings != NULL) {
                    free(resourceIdStrings);
                }
            } else {
                // @todo: limit memory usage by doing this multiple times in a loop with limits;
                DataStorage::Database::QueryResult results = app->db->query_execute(
                    (char *) "SELECT * from oms.orw_resource WHERE oms.orw_resource_status = 1"
                );

                resources = (Models::Resource *) malloc(results.rows * sizeof(Models::Resource));
                for (size_t row = 0; row < results.rows; ++row) {
                    resources[row] = {};

                    for (i = 0; i < results.columns; ++i) {
                        if (results.results[row * results.columns + i] != NULL) {
                            free(results.results[row * results.columns + i]);
                        }
                    }
                }

                if (results.results != NULL) {
                    free(results.results);
                }
            }

            // How many resources are handled in one thread
            // This must be multiplied with the thread count for the over all concurrent max downloads
            int THREAD_SIZE = app->config["app"]["resources"]["online"]["downloads"].get<int>();

            Models::Resource **onlineResources  = (Models::Resource **) malloc(oms_min(idLength, THREAD_SIZE) * sizeof(Models::Resource *));
            Models::Resource **offlineResources = (Models::Resource **) malloc(oms_min(idLength, THREAD_SIZE) * sizeof(Models::Resource *));

            int j = 0;
            int c = 0;
            int k = 0;

            for (i = 0; i < idLength; ++i) {
                if (resources[i].type == Models::ResourceType::RESOURCE_ONLINE) {
                    onlineResources[j] = &resources[i];

                    ++j;
                } else {
                    offlineResources[k] = &resources[i];

                    ++k;
                }

                // Handle online resources in batches here:
                if (j > 0 && (j == THREAD_SIZE || i + 1 >= idLength)) {
                    ThreadData *data = (ThreadData *) malloc(sizeof(ThreadData));
                    data->resources       = onlineResources;
                    data->count           = j;

                    Threads::pool_add_work(app->pool, onlineResourceThreaded, data);

                    if (i + 1 < idLength) {
                        onlineResources = (Models::Resource **) malloc((oms_min(idLength - i, THREAD_SIZE)) * sizeof(Models::Resource *));
                        j = 0;
                    }
                }

                // Handle offline resources in batches here:
                if (k > 0 && (k == THREAD_SIZE || i + 1 >= idLength)) {
                    ThreadData *data = (ThreadData *) malloc(sizeof(ThreadData));
                    data->resources       = offlineResources;
                    data->count           = k;

                    Threads::pool_add_work(app->pool, offlineResourceThreaded, data);

                    if (i + 1 < idLength) {
                        offlineResources = (Models::Resource **) malloc((oms_min(idLength - i, THREAD_SIZE)) * sizeof(Models::Resource *));
                        k = 0;
                    }
                }
            }

            Threads::pool_wait(app->pool);
            free(resources);
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