<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Convert extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		exit('Use AJAX method');
		
		Phpfox::isUser(true);
		
		if (!($iId = $this->request()->get('id')))
		{
			return Phpfox_Error::display(_p('videochannel.id_must_be_defined'));
		}
		
		echo '<script type="text/javascript">';		
		if (Phpfox::getService('videochannel.convert')->process($iId))
		{
			if ($this->request()->get('video-inline') == '1')
			{				
				$iFeedId = Phpfox::getService('feed.process')->getLastId();
				
		    	echo 'window.parent.$.ajaxCall(\'videochannel.displayFeed\', \'id=' . $iFeedId . '&video_id=' . $iId . '\', \'GET\');';
			}
			elseif ($this->request()->get('isajax'))
			{
				echo 'window.parent.Editor.insert({type: \'video\', id: \'' . (int) $iId . '\', editor_id: \'' . base64_decode($this->request()->get('editor-id')) . '\'});';
			}
			else 
			{
				$aVideo = Phpfox::getService('videochannel')->getForEdit($iId);
				echo 'window.parent.location.href = \'' . Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']) . '\';';	
			}
		}
		else 
		{
			Phpfox::getService('videochannel.process')->delete($iId);
			echo 'window.parent.document.getElementById(\'js_video_upload_error\').style.display = \'block\';';			
			echo 'window.parent.document.getElementById(\'js_video_upload_message\').innerHTML = \'' . implode('<br />', Phpfox_Error::get()) . '\';';
			echo 'window.parent.document.getElementById(\'js_upload_inner_form\').style.display = \'block\';';
			echo 'window.parent.document.getElementById(\'js_video_detail\').style.display = \'none\';';
			echo 'window.parent.document.getElementById(\'js_video_process\').style.display = \'none\';';			
		}
		echo '</script>';
		
		exit;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_convert_clean')) ? eval($sPlugin) : false);
	}
}

?>