<?php
;

if($iId == 12 && Phpfox::isUser() && Phpfox::isModule('ynchat')  && !in_array('ynchat.maincontent', $aBlocks[$iId])){
	$aBlocks[$iId][] = 'ynchat.maincontent';
}

;
?>
