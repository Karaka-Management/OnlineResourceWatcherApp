<?php
declare(strict_types=1);

use phpOMS\Uri\UriFactory;
?>

<footer>
    <div class="floater">
        <div id="copyright">(c) Dennis Eichhorn</div>
        <ul id="bottomnav">
			<li><a href="<?= UriFactory::build('{/lang}/terms'); ?>"><?= $this->getHtml('Terms', '0', '0'); ?></a></li>
			<li><a href="<?= UriFactory::build('{/lang}/privacy'); ?>"><?= $this->getHtml('Privacy', '0', '0'); ?></a></li>
			<li><a href="<?= UriFactory::build('{/lang}/imprint'); ?>"><?= $this->getHtml('Imprint', '0', '0'); ?></a></li>
			<li><a href="<?= UriFactory::build('{/lang}/contact'); ?>"><?= $this->getHtml('Contact', '0', '0'); ?></a></li>
        </ul>
    </div>
</footer>