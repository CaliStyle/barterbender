<?php
;

if(Phpfox::isModule('directory')){
	Phpfox::getService('foxfavorite.process')->add('directory', (int) $iItemId);
}

;
?>