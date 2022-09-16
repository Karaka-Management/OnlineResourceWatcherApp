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

namespace Models {
    class Db {
        private:

        public:
            static inline
            int setup_connection (char *cfg)
            {
                return 0;
            }
    };
}

#endif