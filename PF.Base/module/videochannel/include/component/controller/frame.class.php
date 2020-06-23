<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Frame extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        $oService = Phpfox::getService('videochannel.process');
		if (!Phpfox::isUser() || !false || !Phpfox::getUserParam('videochannel.can_upload_videos'))
		{
			exit;
		}
		$bMassUploader = false && (isset($_POST['sMethod']) && $_POST['sMethod'] == 'massuploader');
		if (isset($_FILES['Filedata']) && !isset($_FILES['video']))
		{
			$_FILES['video'] = $_FILES['Filedata'];
		}		
		$bIsInline = false;
		$aVals = $this->request()->get('val');
		if (isset($aVals['video_inline']))
		{
			$bIsInline = true;
		}
		if (!isset($_FILES['video']))
		{
			echo '<script type="text/javascript">';					
			if (!$bIsInline)
			{
				echo 'if (window.parent.$Core.exists(\'#js_video_upload_error\')){';
				echo 'window.parent.document.getElementById(\'js_video_upload_error\').style.display = \'block\';';
				echo 'window.parent.document.getElementById(\'js_video_upload_message\').innerHTML = \''._p('videochannel.upload_failed_file_is_too_large').'\';';
				echo 'window.parent.document.getElementById(\'js_upload_inner_form\').style.display = \'block\';';
				echo 'window.parent.document.getElementById(\'js_video_detail\').style.display = \'none\';';
				echo 'window.parent.document.getElementById(\'js_video_process\').style.display = \'none\';';				
				echo '}else{';
				echo 'window.parent.$Core.resetActivityFeedError(\'' . _p('videochannel.upload_failed_file_is_too_large') . '\');';
				echo '}';
			}
			else 
			{
				echo 'window.parent.$Core.resetActivityFeedError(\'' . _p('videochannel.upload_failed_file_is_too_large') . '\');';
			}			
			echo '</script>';
			exit;
		}
		if (($iFlood = Phpfox::getUserParam('videochannel.flood_control_videos')) !== 0)
		{
			$aFlood = array(
				'action' => 'last_post', // The SPAM action
				'params' => array(
					'field' => 'time_stamp', // The time stamp field
					'table' => Phpfox::getT('channel_video'), // Database table we plan to check
					'condition' => 'view_id = 0 AND user_id = ' . Phpfox::getUserId(), // Database WHERE query
					'time_stamp' => $iFlood * 60 // Seconds);	
				)
			);
			// actually check if flooding
			if (Phpfox::getLib('spam')->check($aFlood))
			{
				Phpfox_Error::set(_p('videochannel.you_are_uploading_a_video_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
			}
		}					
		if (!Phpfox_Error::isPassed())
		{
			if (!empty($_FILES['video']['tmp_name']))
			{
				$oService->delete();
			}
			echo '<script type="text/javascript">';			
			if (!$bIsInline)
			{				
				echo 'window.parent.document.getElementById(\'js_video_upload_error\').style.display = \'block\';';
				echo 'window.parent.document.getElementById(\'js_video_upload_message\').innerHTML = \'' . implode('<br />', Phpfox_Error::get()) . '\';';				
				echo 'window.parent.document.getElementById(\'js_upload_inner_form\').style.display = \'block\';';
				echo 'window.parent.document.getElementById(\'js_video_detail\').style.display = \'none\';';
				echo 'window.parent.document.getElementById(\'js_video_process\').style.display = \'none\';';
			}
			else 
			{
				echo 'window.parent.$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');';
			}			
			echo '</script>';
			exit;
		}
		if ($iId = $oService->add($aVals))
		{		
			if ($bMassUploader)
			{
				Phpfox::getLib('ajax')->alert(Phpfox::getLib('image.helper')->display(array('theme' => 'ajax/add.gif', 'class' => 'v_middle')) . ' Your video has successfully been uploaded. Please standby while we convert your video.', ' Converting Video', 600);
				echo '$.ajaxCall(\'videochannel.convert\', \'attachment_id=' . $iId . '&twitter_connection=' . (isset($aVals['connection']['twitter']) ? $aVals['connection']['twitter'] : '0') . '&facebook_connection=' . ((isset($aVals['connection']) && isset($aVals['connection']['facebook'])) ? $aVals['connection']['facebook'] : '0') . '&full=true&custom_pages_post_as_page=' . $this->request()->get('custom_pages_post_as_page') . '\', \'GET\');';
			}
			else
			{
				echo '<script type="text/javascript">'; 
				if (!$bIsInline)
				{
					$sAlert = Phpfox::getLib('ajax')->alert(Phpfox::getLib('image.helper')->display(array('theme' => 'ajax/add.gif', 'class' => 'v_middle')) . ' Your video has successfully been uploaded. Please standby while we convert your video.', ' Converting Video', 600, 150, false, true);
					echo str_replace('tb_show', 'window.parent.tb_show', str_replace('$.ajaxBox', 'window.parent.$.ajaxBox', $sAlert));				
				}
				echo 'window.parent.$.ajaxCall(\'videochannel.convert\', \'attachment_id=' . $iId . '&twitter_connection=' . (isset($aVals['connection']['twitter']) ? $aVals['connection']['twitter'] : '0') . '&facebook_connection=' . ((isset($aVals['connection']) && isset($aVals['connection']['facebook'])) ? $aVals['connection']['facebook'] : '0') . '&' . ($bIsInline ? 'inline=true' : 'full=true') . '&custom_pages_post_as_page=' . $this->request()->get('custom_pages_post_as_page') . '\', \'GET\');';
				echo '</script>';
			}
		}
		else 
		{
			if (!empty($_FILES['video']['tmp_name']))
			{
				$oService->delete($this->request()->get('video_id'));
			}
			echo '<script type="text/javascript">';			
			if (!$bIsInline)
			{
				echo 'window.parent.document.getElementById(\'js_video_upload_error\').style.display = \'block\';';
				echo 'window.parent.document.getElementById(\'js_video_upload_message\').innerHTML = \'' . implode('<br />', Phpfox_Error::get()) . '\';';
				echo 'window.parent.document.getElementById(\'js_upload_inner_form\').style.display = \'block\';';
				echo 'window.parent.document.getElementById(\'js_video_detail\').style.display = \'none\';';
				echo 'window.parent.document.getElementById(\'js_video_process\').style.display = \'none\';';
			}
			else 
			{
				echo 'window.parent.$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');';
			}
			echo '</script>';
		}		
		
		exit;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_frame_clean')) ? eval($sPlugin) : false);
	}
}

?>