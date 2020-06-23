<?php

namespace Apps\YNC_Core;

use Phpfox_Module;

$module = Phpfox_Module::instance();

$module
    ->addAliasNames('ynccore', 'YNC_Core')
    ->addComponentNames('controller', [])
    ->addComponentNames('block',[]);

// Register template directory
$module->addTemplateDirs([
    'ynccore' => PHPFOX_DIR_SITE_APPS . 'ync-core' . PHPFOX_DS . 'views',
]);

group('/ynccore', function () {
    route('/','ynccore.index');
});