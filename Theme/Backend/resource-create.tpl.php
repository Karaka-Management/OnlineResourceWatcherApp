<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Web\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

?>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="iResource" action="<?= UriFactory::build('{/api}orw/resource?csrf={$CSRF}'); ?>" method="put">
                <div class="portlet-head"><?= $this->getHtml('CreateResource', 'OnlineResourceWatcher', 'Backend'); ?></div>
                <div class="portlet-body">
                <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input id="iName" name="name" type="text">
                    </div>

                    <div class="form-group">
                        <label for="iUrl"><?= $this->getHtml('Url'); ?></label>
                        <input id="iUrl" name="uri" type="text" required>
                    </div>

                    <!--
                    <div class="form-group">
                        <label for="iXPath"><?= $this->getHtml('XPath'); ?></label>
                        <input id="iXPath" name="xpath" type="text">
                    </div>
                    -->
                </div>
                <div class="portlet-foot">
                    <input id="iSubmitUser" name="submitUser" type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>
