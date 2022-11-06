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
	<div class="col-xs-12 col-md-6">
		<div class="portlet">
			<form id="iOrganizationSettings" action="<?= UriFactory::build('{/api}organization/settings'); ?>" method="post">
				<div class="portlet-head"><?= $this->getHtml('BillingSettings', '0', '0'); ?></div>
				<div class="portlet-body">
	                <div class="form-group">
	                    <label for="iName"><?= $this->getHtml('Name', '0', '0'); ?></label>
	                    <input id="iName" name="name" type="text" required>
	                </div>

	                <div class="form-group">
	                    <label for="iAddress"><?= $this->getHtml('Address', '0', '0'); ?></label>
	                    <input id="iAddress" name="address" type="text" required>
	                </div>

	                <div class="form-group">
	                	<div class="input-control">
		                    <label for="iPostal"><?= $this->getHtml('Postal', '0', '0'); ?></label>
		                    <input id="iPostal" name="postal" type="text">
		                </div>
		                <div class="input-control">
		                	<label for="iCity"><?= $this->getHtml('City', '0', '0'); ?></label>
	                    	<input id="iCity" name="city" type="text" required>
		                </div>
	                </div>

	                <div class="form-group">
	                    <label for="iEmail"><?= $this->getHtml('BillingEmail', '0', '0'); ?></label>
	                    <input id="iEmail" name="email" type="email" required>
	                </div>
				</div>
				<div class="portlet-foot">
					<input id="iSubmitOrganization" name="submitOrganization" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
				</div>
			</form>
		</div>
	</div>

	<div class="col-xs-12 col-md-6">
		<div class="portlet">
			<form id="iPlanSettings" action="<?= UriFactory::build('{/api}organization/plan'); ?>" method="post">
				<div class="portlet-head"><?= $this->getHtml('PlanSettings', '0', '0'); ?></div>
				<div class="portlet-body">

				</div>
				<div class="portlet-foot">
					<input id="iSubmitPlan" name="sbmitPlan" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
				</div>
			</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<div class="portlet-head"><?= $this->getHtml('Statistics', '0', '0'); ?></div>
			<div class="portlet-body">

			</div>
			<div class="portlet-foot">
				<input id="iSubmitPlan" name="sbmitPlan" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
			</div>
		</div>
	</div>
</div>