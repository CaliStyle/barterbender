<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 18/01/2017
 * Time: 11:44
 */

$installer = new Core\App\Installer();
$installer->onInstall(function() use ($installer) {
    (new \Apps\YNC_Affiliate\Installation\Data\YncAffiliatev401())->process();
    (new \Apps\YNC_Affiliate\Installation\Data\YncAffiliatev401p1())->process();
    (new \Apps\YNC_Affiliate\Installation\Data\YncAffiliatev401p2())->process();
});