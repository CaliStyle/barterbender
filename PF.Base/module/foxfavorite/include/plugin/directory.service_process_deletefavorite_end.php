<?php
;

if(Phpfox::isModule('directory')){
	Phpfox::getService('foxfavorite.process')->UnFavorite('directory', $aItem['business_id']);
}

;
?>