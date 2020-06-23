<?php
;
$sControllFullName = Phpfox::getLib('module')->getFullControllerName();

if(Phpfox::isModule('foxfeedspro') &&  'foxfeedspro.addnews' == $sControllFullName ){
	Phpfox::getLib('setting')->setParam('core.wysiwyg','default');
}

if(Phpfox::isModule('foxfeedspro') &&  'admincp.index' == $sControllFullName ){

	$aParamUrl = Phpfox::getLib('url')->getParams();
	if(!empty($aParamUrl)){
		if(in_array("foxfeedspro", $aParamUrl)){
			Phpfox::getLib('setting')->setParam('core.wysiwyg','default');
		}
	}
}

;
?>