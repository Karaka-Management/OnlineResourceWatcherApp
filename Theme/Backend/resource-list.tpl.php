<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\OnlineResourceWatcher
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View                               $this
 * @var \Modules\OnlineResourceWatcher\Models\Resource[] $resources
 */
$resources = $this->getData('resources') ?? [];

$tableView            = $this->getData('tableView');
$tableView->id        = 'resourceList';
$tableView->baseUri   = '{/prefix}orw/resource/list';
$tableView->setObjects($resources);

$previous = $tableView->getPreviousLink(
    $this->request,
    empty($this->objects) || !$this->getData('hasPrevious') ? null : \reset($this->objects)
);

$next = $tableView->getNextLink(
    $this->request,
    empty($this->objects) ? null : \end($this->objects),
    $this->getData('hasNext') ?? false
);

?>
<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head">
                <?= $tableView->renderTitle(
                    $this->getHtml('Resources')
                ); ?>
                <a class="button button end-xs save" href="<?= UriFactory::build('{/base}/'); ?>orw/resources/create"><?= $this->getHtml('New', '0', '0'); ?></a>
            </div>
            <div class="slider">
            <table id="<?= $tableView->id; ?>" class="default sticky">
                <thead>
                <tr>
                    <td><?= $tableView->renderHeaderElement(
                        'id',
                        $this->getHtml('ID', '0', '0'),
                        'number'
                    ); ?>
                    <td class="wf-100"><?= $tableView->renderHeaderElement(
                        'resource',
                        $this->getHtml('Resource'),
                        'text'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'status',
                        $this->getHtml('Status'),
                        'text'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'lastChecked',
                        $this->getHtml('Checked'),
                        'date'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'createdAt',
                        $this->getHtml('Date'),
                        'date'
                    ); ?>
                <tbody>
                <?php $count = 0;
                foreach ($resources as $key => $resource) : ++$count;
                    $url = UriFactory::build('{/base}/orw/resource?id=' . $resource->id); ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td><?= $resource->id; ?>
                        <td><?= $this->printHtml($resource->title); ?>
                        <td><?= $this->printHtml((string) $resource->getStatus()); ?>
                        <td><?= $this->printHtml($resource->checkedAt->format('Y-m-d H:i')); ?>
                        <td><?= $this->printHtml($resource->createdAt->format('Y-m-d')); ?>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
            <?php if ($this->getData('hasPrevious') || $this->getData('hasNext')) : ?>
            <div class="portlet-foot">
                <?php if ($this->getData('hasPrevious')) : ?>
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><i class="fa fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php if ($this->getData('hasNext')) : ?>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><i class="fa fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
