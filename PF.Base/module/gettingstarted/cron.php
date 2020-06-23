<?php

include "cli.php";

@ini_set('display_startup_errors',1);
@ini_set('display_errors',1);
@ini_set('error_reporting',-1);

require(PHPFOX_DIR . 'vendor' . PHPFOX_DS . 'autoload.php');
$service =  Phpfox::getService("gettingstarted.process");
$service->InsertMailToQueue();
// remember for email POLL / EVENT / BLOG / PHOTO
/*
 * this send mail to only user who already posted POLL / EVENT / BLOG / PHOTO.
 * it not send if user never post any thing in module POLL / EVENT / BLOG / PHOTO.
 */
$service->SendMail();
