/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
#ifndef MODELS_ACCOUNT_STATUS_H
#define MODELS_ACCOUNT_STATUS_H

namespace Models {
    typedef enum {
        ACCOUNT_ACTIVE = 1,
        ACCOUNT_INACTIVE = 2
    } AccountStatus;
}

#endif