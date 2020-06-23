<?php

$aHideConds = [];
Phpfox::getService('ynfeed')->getHideCondition($aHideConds);
$sMoreWhere .= implode(' ', $aHideConds);