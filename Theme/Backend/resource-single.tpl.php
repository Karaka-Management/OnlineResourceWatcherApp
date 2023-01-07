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

$resource = $this->getData('resource');
?>
<div class="row">
	<div class="col-xs-8">
		<div class="portlet">
			<form id="iResource" action="<?= UriFactory::build('{/api}resource'); ?>" method="post">
				<div class="portlet-head"><?= $this->getHtml('Resource'); ?></div>
				<div class="portlet-body">
					<div class="form-group">
	                    <label for="iName"><?= $this->getHtml('Name'); ?></label>
	                    <input id="iName" name="name" type="text" value="<?= $this->printHtml($resource->title); ?>">
	                </div>

	                <div class="form-group">
	                    <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
	                    <select id="iStatus" name="status">
                            <option value="1"<?= $resource->status === 1 ? ' selected' : ''; ?>>Active</option>
                            <option value="2"<?= $resource->status === 2 ? ' selected' : ''; ?>>Inactive</option>
                        </select>
	                </div>

	                <div class="form-group">
	                    <label for="iUrl"><?= $this->getHtml('Url'); ?></label>
	                    <input id="iUrl" name="uri" type="text" required>
	                </div>

	                <div class="form-group">
	                    <label for="iXPath"><?= $this->getHtml('XPath'); ?></label>
	                    <input id="iXPath" name="xpath" type="text">
	                </div>
				</div>
				<div class="portlet-foot">
					<input id="iSubmitUser" name="submitUser" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
				</div>
			</form>
		</div>
	</div>

	<div class="col-xs-4">
		<div class="portlet">
			<div class="portlet-head"><?= $this->getHtml('History'); ?></div>
			<div class="portlet-body">
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="portlet">
			<div class="portlet-body">
				<div id="resource" class="tabview tab-2 m-editor wf-100">
                    <ul class="tab-links">
                        <li><label tabindex="0" for="resource-c-tab-1"><?= $this->getHtml('Preview'); ?></label>
                        <li><label tabindex="1" for="resource-c-tab-2"><?= $this->getHtml('Comparison'); ?></label>
                    </ul>
                    <div class="tab-content">
                        <input type="radio" id="resource-c-tab-1" name="tabular-1" checked>
                        <div class="tab">

                        </div>

                        <input type="radio" id="resource-c-tab-2" name="tabular-1">
                        <div class="tab">

                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
