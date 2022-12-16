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
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;
?>
<div id="t-nav-container">
    <ul id="t-nav" role="navigation">
        <li><a id="nav-logout" class="active" href="api/logout"
            data-action='[{"key":1,"listener":"click","action":[{"key":1,"type":"event.prevent"},{"key":2,"type":"message.request","uri":"api\/logout","method":"POST","request_type":"raw"},{"key":3,"type":"dom.reload"}]}]' ><?= $this->getHtml('SignOut', '0', '0'); ?></a></li>
    </ul>
</div>