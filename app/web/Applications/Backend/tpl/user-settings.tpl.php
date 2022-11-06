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
?>
<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<form id="iUserSettings" action="<?= UriFactory::build('{/api}user/settings'); ?>" method="post">
				<div class="portlet-head"><?= $this->getHtml('UserSettings', '0', '0'); ?></div>
				<div class="portlet-body">
					<div class="form-group">
	                    <label for="iLogin"><?= $this->getHtml('Login', '0', '0'); ?></label>
	                    <input id="iLogin" name="login" type="text" disabled>
	                </div>

	                <div class="form-group">
	                    <label for="iPassword"><?= $this->getHtml('NewPassword', '0', '0'); ?></label>
	                    <input id="iPassword" name="password" type="password">
	                </div>

	                <div class="form-group">
	                    <label for="iEmail"><?= $this->getHtml('Email', '0', '0'); ?></label>
	                    <input id="iEmail" name="email" type="email" required>
	                </div>

	                <div class="form-group">
	                    <label for="iEmail"><?= $this->getHtml('Organization', '0', '0'); ?></label>
	                    <input id="iEmail" name="organization" type="text" disabled="">
	                    <input type="submit" value="<?= $this->getHtml('Exit', '0', '0'); ?>">
	                </div>
				</div>
				<div class="portlet-foot">
					<input id="iSubmitUser" name="submitUser" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
				</div>
			</form>
		</div>
	</div>
</div>