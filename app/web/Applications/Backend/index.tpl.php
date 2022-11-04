<?php

/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Web\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/** @var phpOMS\Model\Html\Head $head */
$head = $this->getData('head');

/** @var array $dispatch */
$dispatch = $this->getData('dispatch') ?? [];
?>
<!DOCTYPE HTML>
<html lang="<?= $this->printHtml($this->response->getLanguage()); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1e3182">
    <meta name="msapplication-navbutton-color" content="#1e3182">
    <meta name="apple-mobile-web-app-status-bar-style" content="#1e3182">
    <meta name="description" content="<?= $this->getHtml(':meta', '0', '0'); ?>">
    <?= $head->meta->render(); ?>

    <base href="<?= UriFactory::build('{/base}'); ?>/">

    <link rel="shortcut icon" href="<?= UriFactory::build('Applications/Backend/img/favicon.ico?v=1.0.0'); ?>" type="image/x-icon">

    <title><?= $this->printHtml($head->title); ?></title>

    <?= $head->renderAssets(); ?>

    <style><?= $head->renderStyle(); ?></style>
    <script><?= $head->renderScript(); ?></script>
</head>
<body>
    <div class="vh" id="dim"></div>
    <header>
        <div id="t-nav-container">
            <ul id="t-nav" role="navigation">
                <li><a href=""><span class="link">Logout</span></a></li>
            </ul>
        </div>
    </header>
    <main>
        <nav id="nav-side">
            <div id="nav-side-outer" class="oms-ui-state">
                <ul id="nav-side-inner" class="nav" role="navigation">
                    <li>
                        <input name="category-admin" class="oms-ui-state" id="nav-admin" type="checkbox">
                        <ul>
                            <li>
                                <label for="nav-admin">
                                    <i class=""></i>
                                    <span>Admin</span>
                                    <i class="fa lni lni-chevron-right expand"></i>
                                </label>
                            </li>
                            <li><a href="">Organizations</a>
                            <li><a href="">Users</a>
                            <li><a href="">Bills</a>
                        </ul>
                    </li>
                    <li>
                        <input name="category-org" class="oms-ui-state" id="nav-org" type="checkbox">
                        <ul>
                            <li>
                                <label for="nav-org">
                                    <i class=""></i>
                                    <span>Organization</span>
                                    <i class="fa lni lni-chevron-right expand"></i>
                                </label>
                            </li>
                            <li><a href="">Settings</a>
                            <li><a href="">Users</a>
                            <li><a href="">Resources</a>
                            <li><a href="">Bills</a>
                        </ul>
                    </li>
                    <li>
                        <input name="category-home" class="oms-ui-state" id="nav-home" type="checkbox">
                        <ul>
                            <li>
                                <label for="nav-home">
                                    <i class=""></i>
                                    <span>Home</span>
                                    <i class="fa lni lni-chevron-right expand"></i>
                                </label>
                            </li>
                            <li><a href="">Settings</a>
                            <li><a href="">Resources</a>
                            <li><a href="">Reports</a>
                        </ul>
                    </li>
                    <li>
                        <input name="category-legal" class="oms-ui-state" id="nav-legal" type="checkbox">
                        <ul>
                            <li>
                                <label for="nav-legal">
                                    <i class=""></i>
                                    <span>Legal</span>
                                    <i class="fa lni lni-chevron-right expand"></i>
                                </label>
                            </li>
                            <li><a href="">Terms</a>
                            <li><a href="">Privacy</a>
                            <li><a href="">Imprint</a>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div id="content" class="container-fluid" role="main">
            <?php
            $c = 0;
            foreach ($dispatch as $view) {
                if (!($view instanceof \phpOMS\Views\NullView)
                    && $view instanceof \phpOMS\Contract\RenderableInterface
                ) {
                    ++$c;
                    echo $view->render();
                }
            }

            if ($c === 0) {
                echo '<div class="emptyPage"></div>';
            }
            ?>
        </div>
    </main>
    <div id="app-message-container">
        <template id="app-message-tpl">
            <div class="log-msg">
                <h1 class="log-msg-title"></h1><i class="close fa fa-times"></i>
                <div class="log-msg-content"></div>
            </div>
        </template>
    </div>

<template id="table-context-menu-tpl">
    <div id="table-context-menu" class="context-menu">
        <ul>
            <li class="context-line">
                <label class="checkbox" for="itable1-visibile-">
                    <input type="checkbox" id="itable1-visibile-" name="itable1-visible" checked>
                    <span class="checkmark"></span>
                </label>
            </li>
        </ul>
    </div>
</template>
<?= $head->renderAssetsLate(); ?>
