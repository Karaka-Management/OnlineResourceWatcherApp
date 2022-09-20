/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
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
    void freeResourceInfo(ResourceInfo *obj)
    {
        if (obj->mail != NULL) {
            free(obj->mail);
            obj->mail = NULL;

        }

        if (obj->account != NULL) {
            freeAccount(obj->account);
            obj->account = NULL;
        }

        free(obj);
    }
}

#endif