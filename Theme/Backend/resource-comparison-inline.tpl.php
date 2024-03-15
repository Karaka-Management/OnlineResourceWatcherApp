<?php
declare(strict_types=1);

use Modules\OnlineResourceWatcher\Models\ReportStatus;
use phpOMS\Uri\UriFactory;

?>

<div class="tabview tab-2">
    <div class="box">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('Comparison'); ?></label>
            <li><label for="c-tab-2"><?= $this->getHtml('Difference'); ?></label>
            </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row row-simple">
                <?php
                    $old = null;
                    $new = null;

                    foreach ($reports as $report) {
                        if ($report->status === ReportStatus::DOWNLOAD_ERROR) {
                            continue;
                        }

                        $old = $new ?? $report;
                        $new = $report;
                    }

                    if ($resource->checkedAt !== null) :
                        $type = '';

                        if ($old !== null) {
                            $oldBasePath = __DIR__ . '/../../Files/' . $resource->path . '/' . $old->versionPath;
                            $oldWebPath  = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $old->versionPath;

                            if (\is_file($oldBasePath . '/index.jpg')) {
                                $type = 'img';
                                $oldWebPath .= '/index.jpg';
                            } elseif (\is_dir($oldBasePath)) {
                                $files = \scandir($oldBasePath);
                                if ($files !== false) {
                                    foreach ($files as $file) {
                                        if ($file === '.' || $file === '..' || \str_starts_with($file, '_')) {
                                            continue;
                                        }

                                        $oldWebPath .= '/' . $file;

                                        if (\stripos($file, '.jpg') !== false
                                            || \stripos($file, '.jpeg') !== false
                                            || \stripos($file, '.png') !== false
                                            || \stripos($file, '.gif') !== false
                                            || \stripos($file, '.webp') !== false
                                        ) {
                                            $type = 'img';
                                            break;
                                        } elseif (\stripos($file, '.pdf') !== false) {
                                            $type = 'pdf';
                                            break;
                                        } elseif (\stripos($file, '.htm') !== false) {
                                            $type = 'htm';
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $oldWebPath = '../../../../Web/Backend/img/404.svg';
                                $type       = 'img';
                            }
                        }

                        if ($new !== null) {
                            $newBasePath = __DIR__ . '/../../Files/' . $resource->path . '/' . $new->versionPath;
                            $newWebPath  = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $new->versionPath;

                            if (\is_file($newBasePath . '/index.jpg')) {
                                $type = 'img';
                                $newWebPath .= '/index.jpg';
                            } elseif (\is_dir($newBasePath)) {
                                $files = \scandir($newBasePath);
                                if ($files !== false) {
                                    foreach ($files as $file) {
                                        if ($file === '.' || $file === '..' || \str_starts_with($file, '_')) {
                                            continue;
                                        }

                                        $newWebPath .= '/' . $file;

                                        if (\stripos($file, '.jpg') !== false
                                            || \stripos($file, '.jpeg') !== false
                                            || \stripos($file, '.png') !== false
                                            || \stripos($file, '.gif') !== false
                                            || \stripos($file, '.webp') !== false
                                        ) {
                                            $type = 'img';
                                            break;
                                        } elseif (\stripos($file, '.pdf') !== false) {
                                            $type = 'pdf';
                                            break;
                                        } elseif (\stripos($file, '.htm') !== false) {
                                            $type = 'htm';
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $newWebPath = $oldWebPath;
                            }
                        }

                        $base        = __DIR__ . '/../../../../';
                        $newDiffPath = '';

                        if ($type === 'pdf') {
                            $newDiffPath = \dirname($newWebPath) . '/_' . \basename($newWebPath, '.pdf') . '.htm';
                        } else {
                            $newDiffPath = \dirname($newWebPath) . '/_' . \basename($newWebPath);
                        }
                ?>

                <?php if ($type === 'pdf' && $old !== null) : ?>
                <div class="col-xs-6 col-simple">
                    <div class="portlet col-simple">
                        <div class="portlet-body col-simple">
                            <iframe class="col-simple" id="iRenderOld" src="Resources/mozilla/Pdf/web/viewer.html?file=<?= \urlencode(UriFactory::build('{/api}orw/resource/render?path=' . $oldWebPath)); ?>" loading="lazy" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>

                <div class="col-xs-6 col-simple">
                    <div class="portlet col-simple">
                        <div class="portlet-body col-simple">
                            <iframe class="col-simple" id="iRenderNew" src="Resources/mozilla/Pdf/web/viewer.html?file=<?= \urlencode(UriFactory::build('{/api}orw/resource/render?path=' . $newWebPath)); ?>" loading="lazy" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
                <?php elseif ($old !== null) : ?>
                <div class="col-xs-6 col-simple">
                    <div class="portlet col-simple">
                        <div class="portlet-body col-simple">
                            <iframe class="col-simple" id="iRenderOld" sandbox="allow-scripts" src="<?= $oldWebPath; ?>" loading="lazy" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>

                <div class="col-xs-6 col-simple">
                    <div class="portlet col-simple">
                        <div class="portlet-body col-simple">
                            <iframe class="col-simple" id="iRenderNew" sandbox="allow-scripts" src="<?= $newWebPath; ?>" loading="lazy" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php endif; ?>
            </div>
        </div>

        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-2' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row row-simple">
                <div class="col-xs-6 col-simple">
                    <div class="portlet col-simple">
                        <div class="portlet-body col-simple">
                            <?php if (\is_file($base . $newDiffPath)) : ?>
                            <iframe class="col-simple" id="iRenderNew" sandbox="allow-scripts" src="<?= $newDiffPath; ?>" loading="lazy" allowfullscreen></iframe>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>