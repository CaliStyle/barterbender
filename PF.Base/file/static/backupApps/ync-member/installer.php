<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 13/01/2017
 * Time: 17:17
 */

$installer = new Core\App\Installer();

$installer->onInstall(function() use ($installer) {
    (new \Apps\YNC_Member\Installation\Data\YnMemberv401())->process();
    (new \Apps\YNC_Member\Installation\Data\YnMemberv401p1())->process();
});