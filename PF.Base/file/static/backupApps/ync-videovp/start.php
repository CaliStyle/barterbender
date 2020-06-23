<?php

namespace Apps\YNC_VideoViewPop;

use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('yncvideovp', 'YNC_VideoViewPop')
    ->addTemplateDirs(array('yncvideovp' => PHPFOX_DIR_SITE_APPS . 'ync-videovp' . PHPFOX_DS . 'views'))
    ->addComponentNames('controller', array(
        'yncvideovp.view' => Controller\ViewController::class
    ))
    ->addComponentNames('ajax', array(
        'yncvideovp.ajax' => Ajax\Ajax::class
    ))
    ->addServiceNames(array(
        'yncvideovp' => Service\Yncvideovp::class
    ))
;