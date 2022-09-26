/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
#ifndef MODELS_ACCOUNT_H
#define MODELS_ACCOUNT_H

#include <stdio.h>
#include <stdlib.h>

#include "AccountStatus.h"
#include "Organization.h"

namespace Models {
    typedef struct {
        int id = 0;

        AccountStatus status = AccountStatus::ACTIVE;

        char *email = NULL;

        char *lang = NULL;

        Organization *org = NULL;

        time_t created_at = 0;
    } Account;

    inline
    void free_Account(Account *obj)
    {
        if (obj->email != NULL) {
            free(obj->email);
            obj->email = NULL;

        }

        if (obj->lang != NULL) {
            free(obj->lang);
            obj->lang = NULL;

        }

        if (obj->org != NULL) {
            free_Organization(obj->org);
            free(obj->org);

            obj->org = NULL;
        }
    }
}

#endif