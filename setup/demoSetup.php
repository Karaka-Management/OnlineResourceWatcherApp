<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Karaka
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

\ini_set('memory_limit', '2048M');
\ini_set('display_errors', '1');
\ini_set('display_startup_errors', '1');
\error_reporting(\E_ALL);

// For seeded test environment

require_once __DIR__ . '/../app/web/phpOMS/Autoloader.php';

/**
 * This script is usefull when you want to manually install the app without resetting an old database/app or new empty database.
 */

