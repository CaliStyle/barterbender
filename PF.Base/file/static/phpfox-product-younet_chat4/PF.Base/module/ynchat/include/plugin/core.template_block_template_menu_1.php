<?php
;

if(Phpfox::isUser() && Phpfox::isModule('ynchat')){
	$sAgent = Phpfox::getService('ynchat.helper')->getBrowser();
	$type = 'web';
	if(Phpfox::getService('ynchat.helper')->isMobile()){
	    $type = 'mobile';
	}  

	/*fix for case page*/
	$sCurrentModule = Phpfox_Url::instance()->reverseRewrite(Phpfox::getLib('request')->get((Phpfox::getLib('request')->get('req1') == 'pages' ? 'req3' : 'req2')));
	if(Phpfox::isModule($sCurrentModule)){
		Phpfox::getBlock('ynchat.maincontent');
	}


	if(($type == 'mobile') && Phpfox::isUser() && Phpfox::isModule('ynchat')){
		Phpfox::getLib('setting')->setParam('core.is_auto_hosted',false);
		    $sSiteLink = Phpfox::getParam('core.path_file');
		?>
			<link id="ynchat_link_css" type="text/css" href="<?php echo $sSiteLink; ?>ynchat/css.php?js_mobile_version=1&v=<?php echo time(); ?>" rel="stylesheet" charset="utf-8">
			<link id="ynchat_link_css_mobile" type="text/css" href="<?php echo $sSiteLink; ?>ynchat/static/css/ynchatmobile.css?js_mobile_version=1&v=<?php echo time(); ?>" rel="stylesheet" charset="utf-8">
			<script id="ynchat_script_js" type="text/javascript" src="<?php echo $sSiteLink; ?>ynchat/js.php?js_mobile_version=1&v=<?php echo time(); ?>" charset="utf-8"></script>
		<?php 
	}	
}

;
?>