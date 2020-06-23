<?php

include "cli.php";

@ini_set('display_startup_errors',1);
@ini_set('display_errors',1);
@ini_set('error_reporting',-1);

Phpfox::getService('ynsocialstore')->cronUpdateStore();
Phpfox::getService('ynsocialstore.product')->cronUpdateProduct();
Phpfox::getService('ecommerce.mail.send')->sendEmailsInQueue();
echo 'Cron Run Successfully';