/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
#ifndef MODELS_DB_H
#define MODELS_DB_H

#include <stdio.h>

namespace Models {
    namespace Db {
        inline
        int setup_connection (char *cfg)
        {
            return 0;
        }

        Resource *get_unchecked_resources(time_t olderThan)
        {

        }
    }
}

#endif