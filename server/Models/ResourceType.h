/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
#ifndef MODELS_RESOURCE_TYPE_H
#define MODELS_RESOURCE_TYPE_H

namespace Models {
    typedef enum {
        RESOURCE_ONLINE = 1,
        RESOURCE_OFFLINE = 2
    } ResourceType;
}

#endif