/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
#ifndef MODELS_RESOURCE_H
#define MODELS_RESOURCE_H

#include <stdio.h>
#include <stdlib.h>

#include "ReosurceStatus.h"
#include "Organization.h"
#include "ResourceInfo.h"

namespace Models {
    typedef struct {
        int id = 0;

        ResourceStatus status = ResourceStatus::INACTIVE;

        char *uri = NULL;

        char *xpath = NULL;

        char *hash = NULL;

        char *last_version_path = NULL;

        time_t last_version_date = 0;

        time_t checked_at = 0;

        Organization *org = NULL;

        time_t created_at = 0;

        ResourceInfo *info = NULL;
    } Resource;

    inline
    void free_Resource(Resource *obj)
    {
        if (obj->uri != NULL) {
            free(obj->uri);
            obj->uri = NULL;

        }

        if (obj->xpath != NULL) {
            free(obj->xpath);
            obj->xpath = NULL;

        }

        if (obj->hash != NULL) {
            free(obj->hash);
            obj->hash = NULL;

        }

        if (obj->last_version_path != NULL) {
            free(obj->last_version_path);
            obj->last_version_path = NULL;

        }

        if (obj->info != NULL) {
            free_ResourceInfo(obj->info);
            free(obj->info);

            obj->info = NULL;

        }

        if (obj->org != NULL) {
            free_Organization(obj->org);
            free(obj->org);

            obj->org = NULL;

        }
    }
}

#endif