<?php
/**
 * Karaka
 *
 * PHP Version 8.1
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
$reports = $resource->reports;
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
                        <input id="iUrl" name="uri" type="text" value="<?= $this->printHtml($resource->uri); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="iXPath"><?= $this->getHtml('XPath'); ?></label>
                        <input id="iXPath" name="xpath" type="text" value="<?= $this->printHtml($resource->xpath); ?>">
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
            <table class="default">
				<thead>
					<tr>
						<td><?= $this->getHtml('Date'); ?>
						<td><?= $this->getHtml('Status'); ?>
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

<div class="row col-simple">
    <div class="col-xs-12 col-simple">
        <div class="portlet col-simple">
            <div class="portlet-body col-simple">
                <div id="resource" class="tabview tab-2 m-editor col-simple">
                    <ul class="tab-links">
                        <li><label tabindex="0" for="resource-c-tab-1"><?= $this->getHtml('Preview'); ?></label>
                        <li><label tabindex="1" for="resource-c-tab-2"><?= $this->getHtml('Comparison'); ?></label>
                    </ul>
                    <div class="tab-content col-simple">
                        <input type="radio" id="resource-c-tab-1" name="tabular-1" checked>
                        <div class="tab col-simple">
                            <div class="col-simple">
                                <div class="col-xs-12 col-simple">
                                    <section id="mediaFile" class="portlet col-simple">
                                        <div class="portlet-body col-simple">
                                            <iframe class="col-simple" id="iRenderFrame" src="<?= UriFactory::build('{/api}orw/resource/render?id=' . $resource->id); ?>" loading="lazy" allowfullscreen></iframe>
                                        </div>
                                    </section>
                                </div>
                            </div>
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
