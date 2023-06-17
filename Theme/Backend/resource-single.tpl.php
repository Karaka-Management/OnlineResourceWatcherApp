<?php
/**
 * Jingga
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

use Modules\OnlineResourceWatcher\Models\ReportStatus;
use phpOMS\Uri\UriFactory;

/** @var \Modules\OnlineResourceWatcher\Models\Resource */
$resource = $this->getData('resource') ?? new \Modules\OnlineResourceWatcher\Models\NullResource();
$reports  = $resource->reports;
?>
<div class="row">
    <div class="col-xs-12 col-sm-8">
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
            <table class="default">
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

<div class="row col-simple">
    <div class="col-xs-12 col-simple">
        <?php if ($resource->checkedAt !== null) : ?>
        <div class="portlet col-simple">
            <div class="portlet-body col-simple">
                <?php

                    $type     = '';
                    $basePath = __DIR__ . '/../../Files/' . $resource->path . '/' . $resource->lastVersionPath;
                    $path     = '';
                    $webPath  = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $resource->lastVersionPath;

                    if (\is_file($basePath . '/index.jpg')) {
                        $type = 'img';
                        $path = $basePath . '/index.jpg';
                        $webPath .= '/index.jpg';
                    } else {
                        $files = \scandir($basePath);

                        if ($files !== false) {
                            foreach ($files as $file) {
                                if ($file === '.' || $files === '..') {
                                    continue;
                                }

                                $path    = $basePath . '/' . $file;
                                $webPath .= '/' . $file;

                                if (\stripos($file, '.jpg') !== false
                                    || \stripos($file, '.jpeg') !== false
                                    || \stripos($file, '.png') !== false
                                    || \stripos($file, '.gif') !== false
                                ) {
                                    $type = 'img';
                                    break;
                                } elseif (\stripos($file, '.pdf') !== false) {
                                    $type = 'pdf';
                                    break;
                                }
                            }
                        }
                    }
                ?>

                <?php if ($report->status !== ReportStatus::DOWNLOAD_ERROR) : ?>
                    <?php if ($type === 'img') : ?>
                        <img src="<?= UriFactory::build($webPath); ?>" alt="<?= $this->printHtml($resource->title); ?>">
                    <?php elseif ($type === 'pdf') : ?>
                        <iframe class="col-simple" id="iRenderFrame" src="Resources/mozilla/Pdf/web/viewer.html?file=<?= \urlencode($webPath); ?>" loading="lazy" allowfullscreen></iframe>
                    <?php else : ?>
                        <iframe class="col-simple" id="iRenderFrame" src="<?= UriFactory::build('{/api}orw/resource/render?id=' . $resource->id); ?>" loading="lazy" sandbox="allow-forms allow-scripts" allowfullscreen></iframe>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
