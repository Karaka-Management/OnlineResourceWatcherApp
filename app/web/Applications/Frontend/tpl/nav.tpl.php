<?php
declare(strict_types=1);

use phpOMS\Uri\UriFactory;
?>

<nav>
    <ul id="topnav">
        <li><a href="<?= UriFactory::build('{/lang}'); ?>"><?= $this->getHtml('Home', '0', '0'); ?></a></li>
        <li><a href="<?= UriFactory::build('{/lang}/features'); ?>"><?= $this->getHtml('Features', '0', '0'); ?></a></li>
        <li><a href="<?= UriFactory::build('{/lang}/pricing'); ?>"><?= $this->getHtml('Pricing', '0', '0'); ?></a></li>
    </ul>
    <ul id="toplogin">
        <li><a id="signinButton" target="_blank" href="<?= UriFactory::build('{/lang}/backend'); ?>"><?= $this->getHtml('SignIn', '0', '0'); ?></a></li>
        <li><a id="signupButton" href="<?= UriFactory::build('{/lang}/signup'); ?>"><?= $this->getHtml('SignUp', '0', '0'); ?></a></li>
    </ul>
</nav>
