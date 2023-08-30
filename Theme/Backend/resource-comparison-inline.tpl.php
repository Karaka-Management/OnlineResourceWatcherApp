<?php declare(strict_types=1);
use Modules\OnlineResourceWatcher\Models\ReportStatus;
use phpOMS\Uri\UriFactory;

?>

<div class="row row-simple">
    <?php
        $old = null;
        $new = null;

        foreach ($reports as $report) {
            if ($report->status === ReportStatus::DOWNLOAD_ERROR) {
                continue;
            }

            $old = $new;
            $new = $report;

            if ($old === null) {
                $old = $report;
            }
        }

        if ($resource->checkedAt !== null) :
            $type = '';

            if ($new !== null) {
                $newBasePath = __DIR__ . '/../../Files/' . $resource->path . '/' . $new->versionPath;
                $newWebPath  = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $new->versionPath;

                if (\is_file($newBasePath . '/index.jpg')) {
                    $type = 'img';
                    $newWebPath .= '/index.jpg';
                } else {
                    $files = \scandir($newBasePath);
                    if ($files !== false) {
                        foreach ($files as $file) {
                            if ($file === '.' || $file === '..') {
                                continue;
                            }

                            $newWebPath .= '/' . $file;

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
            }

            if ($old !== null) {
                $oldBasePath = __DIR__ . '/../../Files/' . $resource->path . '/' . $old->versionPath;
                $oldWebPath  = 'Modules/OnlineResourceWatcher/Files/' . $resource->path . '/' . $old->versionPath;

                if (\is_file($oldBasePath . '/index.jpg')) {
                    $type = 'img';
                    $oldWebPath .= '/index.jpg';
                } else {
                    $files = \scandir($oldBasePath);
                    if ($files !== false) {
                        foreach ($files as $file) {
                            if ($file === '.' || $file === '..') {
                                continue;
                            }

                            $oldWebPath .= '/' . $file;

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
            }
    ?>

    <?php if ($type === 'img') : ?>
    <div class="col-xs-12 col-simple">
        <div class="portlet col-simple">
            <div class="portlet-body col-simple">
                <?php
                if ($old !== null) : ?>
                    <div class="image-comparison">
                        <div>
                            <img src="<?= UriFactory::build($oldWebPath); ?>" alt="<?= $this->printHtml($resource->title); ?>">
                        </div>
                        <img src="<?= UriFactory::build($newWebPath); ?>" alt="<?= $this->printHtml($resource->title); ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php elseif ($type !== 'img' && $old !== null) : ?>
    <div class="col-xs-6 col-simple">
        <div class="portlet col-simple">
            <div class="portlet-body col-simple">
                <iframe class="col-simple" id="iRenderFrame" src="Resources/mozilla/Pdf/web/viewer.html?file=<?= \urlencode(UriFactory::build('{/api}orw/resource/render?path=' . $oldWebPath)); ?>" loading="lazy" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <div class="col-xs-6 col-simple">
        <div class="portlet col-simple">
            <div class="portlet-body col-simple">
                <iframe class="col-simple" id="iRenderFrame" src="Resources/mozilla/Pdf/web/viewer.html?file=<?= \urlencode(UriFactory::build('{/api}orw/resource/render?path=' . $newWebPath)); ?>" loading="lazy" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>