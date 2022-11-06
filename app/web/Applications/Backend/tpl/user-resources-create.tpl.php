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
			<form id="iUserSettings" action="<?= UriFactory::build('{/api}user/settings'); ?>" method="post">
				<div class="portlet-head"><?= $this->getHtml('CreateResource', '0', '0'); ?></div>
				<div class="portlet-body">
					<div class="form-group">
	                    <label for="iLogin"><?= $this->getHtml('Url', '0', '0'); ?></label>
	                    <input id="iLogin" name="rul" type="text" required>
	                </div>

	                <div class="form-group">
	                    <label for="iElement"><?= $this->getHtml('Element', '0', '0'); ?></label>
	                    <input id="iElement" name="element" type="text">
	                </div>

	                <div class="form-group">
	                    <label for="iHeader"><?= $this->getHtml('Header', '0', '0'); ?></label>
	                    <textarea id="iHeader" name="header"></textarea>
	                </div>
				</div>
				<div class="portlet-foot">
					<input id="iSubmitUser" name="submitUser" type="submit" value="<?= $this->getHtml('Create', '0', '0'); ?>">
				</div>
			</form>
		</div>
	</div>

	<div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="iAddAccountToGroup" action="<?= UriFactory::build('{/api}admin/group/account'); ?>" method="put">
                <div class="portlet-head"><?= $this->getHtml('Inform', '0', '0'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iAccount"><?= $this->getHtml('Name', '0', '0'); ?></label>
                        <input id="iAccount" name="account" type="text">
                    </div>

                    <div class="form-group">
                        <label for="iAccount"><?= $this->getHtml('Email', '0', '0'); ?></label>
                        <input id="iAccount" name="email" type="email">
                    </div>
                </div>
                <div class="portlet-foot">
                    <input type="submit" value="<?= $this->getHtml('Add', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Inform', '0', '0'); ?></div>
            <table class="default">
                <thead>
                    <tr>
                        <td><?= $this->getHtml('ID', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                        <td><?= $this->getHtml('User', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                        <td><?= $this->getHtml('Email', '0', '0'); ?><i class="sort-asc fa fa-chevron-up"></i><i class="sort-desc fa fa-chevron-down"></i>
                <tbody>
                    <?php $c = 0; foreach ([] as $key => $value) : ++$c; $url = UriFactory::build('{/prefix}admin/account/settings?{?}&id=' . $value->getId()); ?>
                    <tr data-href="<?= $url; ?>">
                        <td>
                        <td>
                        <td>
                    <?php endforeach; ?>
                    <?php if ($c === 0) : ?>
                        <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                    <?php endif; ?>
            </table>
            <div class="portlet-foot"></div>
        </div>
    </div>
</div>