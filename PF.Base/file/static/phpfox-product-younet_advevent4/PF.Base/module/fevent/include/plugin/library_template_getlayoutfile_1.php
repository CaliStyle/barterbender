<?php
$sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');
if(isset($sFullControllerName) == true && strpos($sFullControllerName, "fevent") !== false)
{
	$aSearchTool = Phpfox::getLib('template')->getVar('aSearchTool');
	if(isset($aSearchTool) == true 
		&& is_array($aSearchTool) == true 
		&& isset($aSearchTool['filters']) == true 
		&& isset($aSearchTool['filters'][_p('core.when')]) == true
		&& isset($aSearchTool['filters'][_p('core.when')]['data']) == true
		)
	{
		$when = Phpfox::getLib('request')->get('when');
		$iExistUpComing = 0;
		$existOnGoing = 0;
		$existPast = 0;
		$link = null;
		foreach($aSearchTool['filters'][_p('core.when')]['data'] as $data)
		{
			$link = $data['link'];
			if(strpos($data['link'], "upcoming") !== false){
				$iExistUpComing = 1;
			}
			if(strpos($data['link'], "ongoing") !== false){
				$existOnGoing = 1;
			}
			if(strpos($data['link'], "past") !== false){
				$existPast = 1;
			}
		}
		$position = strpos($link, "/when_");
		if($position !== false){
			
			$link = substr($link, 0, $position + 1);  
		}
		
		$position = strpos($link, "&when=");
		if($position !== false){
			
			$link = substr($link, 0, $position + 1);  
		}
		$position = strpos($link, "?when=");
		if($position !== false){
			
			$link = substr($link, 0, $position + 1);  
		}
		
		if(strpos($link, "/view_") === false && strpos($link, "?view=") === false && strpos($link, "&view=") === false){
			
			$link = $link.'view=all&';  
		}
		
		if(!$iExistUpComing)
		{
			$obj = array(
					'nofollow' => 1
					, 'link' => $link . 'when=upcoming'
					, 'phrase' => _p('core.upcoming')
				); 
			if(isset($when) == true && 'upcoming' == $when){
				$obj['is_active'] = 1;
				$aSearchTool['filters'][_p('core.when')]['active_phrase'] = _p('core.upcoming');
			}
			$aSearchTool['filters'][_p('core.when')]['data'][] = $obj;
		}
		
		if(!$existOnGoing)
		{
			$obj = array(
					'nofollow' => 1
					, 'link' => $link . 'when=ongoing'
					, 'phrase' => _p('s_ongoing')
				); 
			if(isset($when) == true && 'ongoing' == $when){
				$obj['is_active'] = 1;
				$aSearchTool['filters'][_p('core.when')]['active_phrase'] = _p('s_ongoing');
			}

			$aSearchTool['filters'][_p('core.when')]['data'][] = $obj;
		}
		
		if(!$existPast){
			$obj = array(
					'nofollow' => 1
					, 'link' => $link . 'when=past'
					, 'phrase' => _p('s_past')
				);
			if(isset($when) == true && 'past' == $when){
				$obj['is_active'] = 1;
				$aSearchTool['filters'][_p('core.when')]['active_phrase'] = _p('s_past');
			}

			$aSearchTool['filters'][_p('core.when')]['data'][] = $obj;
		}


		if($existOnGoing == false || $existPast == false || $iExistUpComing){
			Phpfox::getLib('template')->assign('aSearchTool', $aSearchTool);
		}
	}
}

?>
