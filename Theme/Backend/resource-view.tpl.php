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

/** @var \Modules\OnlineResourceWatcher\Models\Resource */
$resource = $this->getData('resource') ?? new \Modules\OnlineResourceWatcher\Models\NullResource();
$reports  = $resource->reports;
?>
<div class="row">
    <div class="col-xs-12 col-sm-8">
        <div class="portlet">
            <form id="iResource" action="<?= UriFactory::build('{/api}orw/resource?csrf={$CSRF}'); ?>" method="post">
                <div class="portlet-head"><?= $this->getHtml('Resource'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iName"><?= $this->getHtml('Name'); ?></label>
                        <input id="iName" name="name" type="text" value="<?= $this->printHtml($resource->title); ?>">
                    </div>

                    <div class="form-group">
                        <label for="iStatus"><?= $this->getHtml('Status'); ?></label>
                        <select id="iStatus" name="status">
                            <option value="1"<?= $resource->status === 1 ? ' selected' : ''; ?>><?= $this->getHtml(':status-1'); ?></option>
                            <option value="2"<?= $resource->status === 2 ? ' selected' : ''; ?>><?= $this->getHtml(':status-2'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="iUrl"><?= $this->getHtml('Url'); ?></label>
                        <input id="iUrl" name="uri" type="text" value="<?= $this->printHtml($resource->uri); ?>" required>
                    </div>

                    <!--
                    <div class="form-group">
                        <label for="iXPath"><?= $this->getHtml('XPath'); ?></label>
                        <input id="iXPath" name="xpath" type="text" value="<?= $this->printHtml($resource->xpath); ?>">
                    </div>
                    -->
                </div>
                <div class="portlet-foot">
                    <input id="iSubmitUser" name="submitUser" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>

    <div class="col-xs-12 col-sm-4">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('History'); ?></div>
            <div class="slider">
            <table class="default sticky">
				<thead>
					<tr>
						<td><?= $this->getHtml('Date'); ?>
						<td class="wf-100"><?= $this->getHtml('Status'); ?>
				<tbody>
					<?php foreach ($reports as $report) : ?>
					<tr>
						<td><?= $this->printHtml($report->createdAt->format('Y-m-d')); ?>
						<td><?= $this->getHtml('rstatus-' . $report->status); ?>
					<?php endforeach; ?>
			</table>
            </div>
        </div>
    </div>
</div>
<!-- @bug Some iframes reset the session because the page they load have relative paths -> loading the page itself -> resetting the session because it's loaded in an iframe -->
<?php include __DIR__ . '/resource-comparison-inline.tpl.php';
