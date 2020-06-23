<?php
;

if(Phpfox::isModule($sType)){

	Phpfox::getService('foxfavorite.process')->add($sType, (int) $iItemId);
}

;
?>