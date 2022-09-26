/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
#ifndef MODELS_ORGANIZATION_H
#define MODELS_ORGANIZATION_H

#include <stdio.h>
#include <stdlib.h>

#include "ReosurceStatus.h"

namespace Models {
    typedef struct {
        int id = 0;
    } Organization;

    inline
    void free_Organization(Organization *obj)
    {
    }
}

#endif