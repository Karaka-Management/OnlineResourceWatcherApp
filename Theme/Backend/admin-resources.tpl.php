<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Auditor
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View            $this
 * @var \Modules\Audit\Models\Audit[] $resources
 */
$resources = $this->getData('resources') ?? [];

$tableView            = $this->getData('tableView');
$tableView->id        = 'auditList';
$tableView->baseUri   = '{/prefix}admin/audit/list';
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
                    $this->getHtml('Resources', '0', '0')
                ); ?>
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
                        $this->getHtml('Resource', '0', '0'),
                        'text'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'user_no',
                        $this->getHtml('User', '0', '0'),
                        'number'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'user_name',
                        $this->getHtml('User', '0', '0'),
                        'text'
					); ?>
					<td><?= $tableView->renderHeaderElement(
                        'status',
                        $this->getHtml('Status', '0', '0'),
                        'text'
					); ?>
					<td><?= $tableView->renderHeaderElement(
                        'lastChecked',
                        $this->getHtml('Checked', '0', '0'),
                        'date'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'createdAt',
                        $this->getHtml('Date', '0', '0'),
                        'date'
                    ); ?>
                <tbody>
                <?php $count = 0;
                foreach ($resources as $key => $resource) : ++$count;
                    $url = UriFactory::build('{/lang}/{/app}/admin/audit/single?id=' . $resource->getId()); ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td>
                        <td>
                        <td>
                        <td>
                        <td>
                        <td>
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
