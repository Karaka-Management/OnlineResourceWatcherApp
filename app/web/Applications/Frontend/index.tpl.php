<?php

/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Applications\Frontend
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
    <base href="<?= UriFactory::build('{/base}'); ?>/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#343a40">
    <meta name="msapplication-navbutton-color" content="#343a40">
    <meta name="apple-mobile-web-app-status-bar-style" content="#343a40">
    <meta name="description" content="<?= $this->getHtml(':meta', '0', '0'); ?>">
    <?= $head->meta->render(); ?>

    <base href="<?= UriFactory::build('{/base}'); ?>/">

    <link rel="shortcut icon" href="<?= UriFactory::build('Applications/Frontend/img/favicon.ico?v=1.0.0'); ?>" type="image/x-icon">

    <title><?= $this->printHtml($head->title); ?></title>

    <?= $head->renderAssets(); ?>

    <style><?= $head->renderStyle(); ?></style>
    <script><?= $head->renderScript(); ?></script>
</head>
<body>
    <?php include __DIR__ . '/tpl/header.tpl.php'; ?>
    <main>
        <div class="floater">
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
                ?>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/tpl/footer.tpl.php'; ?>
<?= $head->renderAssetsLate(); ?>
