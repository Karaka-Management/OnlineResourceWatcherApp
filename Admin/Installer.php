<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\OnlineResourceWatcher\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\OnlineResourceWatcher\Admin;

use phpOMS\Application\ApplicationAbstract;
use phpOMS\Config\SettingsInterface;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;
use phpOMS\System\OperatingSystem;
use phpOMS\System\SystemType;

/**
 * Installer class.
 *
 * @package Modules\OnlineResourceWatcher\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * {@inheritdoc}
     */
    public static function install(ApplicationAbstract $app, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        if (OperatingSystem::getSystem() === SystemType::LINUX && !\is_writable('/var/www')) {
            $app->logger->error(
                'Directory /var/www is not writable. Please allow the apache user (www-data) to write to this directory.'
            );

            \var_dump('NOT WRITABLE');

            return;
        }

        parent::install($app, $info, $cfgHandler);

        if (!\is_dir(__DIR__ . '/../Files')) {
            \mkdir(__DIR__ . '/../Files');
        }
    }
}
