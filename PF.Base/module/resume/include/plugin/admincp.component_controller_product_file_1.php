<?php
	$product_id = $this->request()->get('id');
	if($product_id == 'younet_resume'){
		if(Phpfox::isModule('resume'))
		{
			Phpfox::getService('resume.basic.process')->synchronise();
		}
	}
?>