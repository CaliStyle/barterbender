<?php

include "cli.php";

@ini_set('display_startup_errors',1);
@ini_set('display_errors',1);
@ini_set('error_reporting',-1);
require(PHPFOX_DIR . 'vendor' . PHPFOX_DS . 'autoload.php');
Phpfox::getService('fevent')->executeCron();
echo 'DONE';