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

$head = $this->getData('head');
?>
<!DOCTYPE HTML>
<html lang="<?= $this->printHtml($this->response->getLanguage()); ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <base href="<?= UriFactory::build('{/base}'); ?>/">
    <meta name="theme-color" content="#1e3182">
    <meta name="msapplication-navbutton-color" content="#1e3182">
    <meta name="apple-mobile-web-app-status-bar-style" content="#1e3182">
    <meta name="description" content="<?= $this->getHtml(':meta', '0', '0'); ?>">
    <link rel="shortcut icon" href="<?= UriFactory::build('Applications/Backend/img/favicon.ico'); ?>" type="image/x-icon">
    <?= $head->meta->render(); ?>
    <title><?= $this->printHtml($head->title); ?></title>
    <style><?= $head->renderStyle(); ?></style>
    <script><?= $head->renderScript(); ?></script>
    <?= $head->renderAssets(); ?>
</head>
<body>
<main>
    <div id="login-container">
        <div id="login-logo">
            <img alt="<?= $this->getHtml('Logo', '0', '0'); ?>" src="<?= UriFactory::build('Applications/Backend/img/logo.png'); ?>">
        </div>
        <header><h1><?= $this->getHtml('SignIn', '0', '0'); ?></h1></header>
        <div id="login-form">
            <form id="login" method="POST" action="<?= UriFactory::build('{/api}login?{?}'); ?>">
                <label for="iName"><?= $this->getHtml('Username', '0', '0'); ?>:</label>
                <div class="inputWithIcon">
                    <input id="iName" type="text" name="user" tabindex="1" value="" autocomplete="off" spellcheck="false" autofocus>
                    <i class="frontIcon fa fa-user fa-lg fa-fw" aria-hidden="true"></i>
                    <i class="endIcon fa fa-times fa-lg fa-fw" aria-hidden="true"></i>
                </div>
                <label for="iPassword"><?= $this->getHtml('Password', '0', '0'); ?>:</label>
                <div class="inputWithIcon">
                    <input id="iPassword" type="password" name="pass" tabindex="2" value="">
                    <i class="frontIcon fa fa-lock fa-lg fa-fw" aria-hidden="true"></i>
                    <i class="endIcon fa fa-times fa-lg fa-fw" aria-hidden="true"></i>
                </div>
                <input id="iLoginButton" name="loginButton" type="submit" value="<?= $this->getHtml('SignIn', '0', '0'); ?>" tabindex="3">
            </form>
        </div>
        <div id="below-form"><a href="<?= UriFactory::build('{/prefix}forgot'); ?>" tabindex="4"><?= $this->getHtml('ForgotPassword', '0', '0'); ?></a></div>
    </div>
</main>
<footer>
    <ul>
        <li><a href="/privacy"><?= $this->getHtml('PrivacyPolicy', '0', '0'); ?></a>
        <li><a href="/terms"><?= $this->getHtml('Terms', '0', '0'); ?></a>
        <li><a href="/imprint"><?= $this->getHtml('Imprint', '0', '0'); ?></a>
    </ul>
</footer>

<?= $head->renderAssetsLate(); ?>
