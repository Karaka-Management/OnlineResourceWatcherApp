/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
#ifndef MODELS_RESOURCE_INFO_H
#define MODELS_RESOURCE_INFO_H

#include <stdio.h>
#include <stdlib.h>

#include "Account.h"

namespace Models {
    typedef struct {
        int id = 0;

        char *mail = NULL;

        Account *account = NULL;

        int resource = 0;
    } ResourceInfo;

    inline
    void free_ResourceInfo(ResourceInfo *obj)
    {
        if (obj->mail != NULL) {
            free(obj->mail);
            obj->mail = NULL;

        }

        if (obj->account != NULL) {
            free_Account(obj->account);
            free(obj->account);

            obj->account = NULL;
        }
    }
}

#endif