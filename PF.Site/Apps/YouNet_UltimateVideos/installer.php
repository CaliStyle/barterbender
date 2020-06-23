<?php
/**
 * Created by PhpStorm.
 * User: davidnguyen
 * Date: 7/25/16
 * Time: 6:04 PM
 */

$installer = new Core\App\Installer();
$installer->onInstall(function () use ($installer) {
    (new \Apps\YouNet_UltimateVideos\Install\UltimateVideosv401())->process();
    (new \Apps\YouNet_UltimateVideos\Install\UltimateVideosv401p3())->process();
    (new \Apps\YouNet_UltimateVideos\Install\UltimateVideosv402())->process();
    (new \Apps\YouNet_UltimateVideos\Install\UltimateVideosv403())->process();
    (new \Apps\YouNet_UltimateVideos\Install\UltimateVideosv403p1())->process();
});