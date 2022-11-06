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
    <?php include __DIR__ . '/tpl/header.tpl.php'; ?>
    <main>
        <?php include __DIR__ . '/tpl/nav-side.tpl.php'; ?>
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
