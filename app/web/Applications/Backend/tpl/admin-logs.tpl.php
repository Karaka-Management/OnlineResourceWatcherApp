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
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View            $this
 * @var \Modules\Audit\Models\Audit[] $audits
 */
$audits = $this->getData('audits') ?? [];

$tableView            = $this->getData('tableView');
$tableView->id        = 'auditList';
$tableView->baseUri   = '{/prefix}admin/audit/list';
$tableView->setObjects($audits);

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
                    $this->getHtml('Logs', '0', '0')
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
                    <td><?= $tableView->renderHeaderElement(
                        'action',
                        $this->getHtml('Action', '0', '0'),
                        'select',
                        [
                            'create' => $this->getHtml('CREATE', '0', '0'),
                            'modify' => $this->getHtml('UPDATE', '0', '0'),
                            'delete' => $this->getHtml('DELETE', '0', '0'),
                        ],
                        false // don't render sort
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'type',
                        $this->getHtml('Type', '0', '0'),
                        'number'
                    ); ?>
                    <td class="wf-100"><?= $tableView->renderHeaderElement(
                        'trigger',
                        $this->getHtml('Trigger', '0', '0'),
                        'text'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'createdBy',
                        $this->getHtml('By', '0', '0'),
                        'text'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'ref',
                        $this->getHtml('Ref', '0', '0'),
                        'text',
                        [],
                        true,
                        true,
                        false
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'createdAt',
                        $this->getHtml('Date', '0', '0'),
                        'date'
                    ); ?>
                <tbody>
                <?php $count = 0;
                foreach ($audits as $key => $audit) : ++$count;
                    $url = UriFactory::build('{/prefix}admin/audit/single?id=' . $audit->getId()); ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td><?= $audit->getId(); ?>
                        <td><?php if ($audit->getOld() === null) : echo $this->getHtml('CREATE', '0', '0'); ?>
                            <?php elseif ($audit->getOld() !== null && $audit->getNew() !== null) : echo $this->getHtml('UPDATE', '0', '0'); ?>
                            <?php elseif ($audit->getNew() === null) : echo $this->getHtml('DELETE', '0', '0'); ?>
                            <?php else : echo $this->getHtml('UNKNOWN', '0', '0'); ?>
                            <?php endif; ?>
                        <td><?= $audit->getType(); ?>
                        <td><?= $audit->getTrigger(); ?>
                        <td><a class="content" href="<?= UriFactory::build('{/prefix}admin/account/settings?id=' . $audit->createdBy->getId()); ?>"><?= $this->printHtml(
                                $this->renderUserName('%3$s %2$s %1$s', [$audit->createdBy->name1, $audit->createdBy->name2, $audit->createdBy->name3, $audit->createdBy->login])
                            ); ?></a>
                        <td><?= $this->printHtml($audit->getRef()); ?>
                        <td><?= $audit->createdAt->format('Y-m-d H:i:s'); ?>
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
