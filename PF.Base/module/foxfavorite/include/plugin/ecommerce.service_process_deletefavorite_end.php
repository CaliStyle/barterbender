<?php
;

if(Phpfox::isModule($sType)){

	Phpfox::getService('foxfavorite.process')->UnFavorite($sType, $aItem['product_id']);
}

;
?>