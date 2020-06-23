<?php

namespace Apps\YNC_PhotoViewPop;

use Phpfox;
use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('yncphotovp', 'YNC_PhotoViewPop')
    ->addTemplateDirs(array('yncphotovp' => PHPFOX_DIR_SITE_APPS . 'ync-photovp' . PHPFOX_DS . 'views'))
    ->addComponentNames('controller', array(
        'yncphotovp.view' => Controller\ViewController::class
    ))
    ->addComponentNames('ajax', array(
        'yncphotovp.ajax' => Ajax\Ajax::class
    ));
