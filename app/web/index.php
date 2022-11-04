<?php
declare(strict_types=1);

\ob_start();

require_once __DIR__ . '/phpOMS/Autoloader.php';

$config = \json_decode(\file_get_contents(__DIR__ . '/config.json'), true);

$App = new \WebApplication($config);

if (\ob_get_level() > 0) {
    \ob_end_flush();
}
