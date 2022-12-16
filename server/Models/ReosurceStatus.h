/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
#ifndef MODELS_RESOURCE_STATUS_H
#define MODELS_RESOURCE_STATUS_H

namespace Models {
    typedef enum {
        ACTIVE = 1,
        INACTIVE = 2
    } ResourceStatus;
}

#endif