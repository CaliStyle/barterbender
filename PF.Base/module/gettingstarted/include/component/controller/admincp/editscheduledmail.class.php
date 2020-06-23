<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_component_controller_admincp_editscheduledmail extends Phpfox_Component{

	public function process()
	{
		$id=$this->request()->get("id");

		$bool_test=0;
		if(isset($_POST['submit_editscheduledmail'])==true)
		{

			$aVals = $this->request()->get('val');

			if($aVals['message']=="")
			{
				Phpfox_Error::set(_p('gettingstarted.message_is_not_allowed_empty'));
				$bool_test=1;
			}
			if($aVals['name']=="")
			{
				Phpfox_Error::set(_p('gettingstarted.subtitle_is_not_allowed_empty'));
				$bool_test=1;
			}
			if($aVals['time']=="")
			{
				Phpfox_Error::set(_p('gettingstarted.time_is_not_allowed_empty'));
				$bool_test = 1;
			}
			else
			{
				$strTime = '';
				$regexp = "/^\d+$/";
				$aVals['time'] = trim($aVals['time']);
				$atime = explode(" ", $aVals['time']);
				if(preg_match($regexp, $aVals['time']) == 1)
				{
					$aVals['time'] = (int)$aVals['time'];
					if($aVals['time'] > 999)
					{
						$bool_test = 1;
						Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
					}
					else
						$strTime = $aVals['time']."h";

				}
				else
				{
					$bCheck = explode("m", $aVals['time']);
					if(count($bCheck) > 2)
					{
						$bool_test = 1;
						Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
					}
					$bCheck = explode("w", $aVals['time']);
					if(count($bCheck) > 2)
					{
						$bool_test = 1;
						Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
					}
					$bCheck = explode("d", $aVals['time']);
					if(count($bCheck) > 2)
					{
						$bool_test = 1;
						Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
					}
					$bCheck = explode("h", $aVals['time']);
					if(count($bCheck) > 2)
					{
						$bool_test = 1;
						Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
					}
					foreach($atime as $time)
					{
						$b = substr($time, strlen($time)-1, 1);
						$a = substr($time, 0, strlen($time)-1);
						if($b != "m" && $b != "w" && $b != "d" && $b != "h")
						{
							$bool_test = 1;
							Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
							break;
						}
						else if(preg_match($regexp, $a) == 0)
						{
							$bool_test = 1;
							Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
							break;
						}
						else
						{
							$a = (int)$a;
							if($a > 999)
							{
								$bool_test = 1;
								Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
								break;
							}
							else
							{
								$strTime .= (int)$a."".$b." ";
							}
						}
					}
				}
			}

			if($bool_test==0)
			{
				$aVals['time'] = $strTime;
				$link="admincp/gettingstarted/editscheduledmail/id_".$aVals["scheduledmail_id"];
				Phpfox::getService("gettingstarted")->updateScheduledMail($aVals);
				$this->url()->send('admincp.gettingstarted.managemail', null, _p('gettingstarted.edit_scheduled_mail_successfully'));
			}
			

		}

		$scheduled_mail=phpfox::getService('gettingstarted')->getScheduledMailId($id);
		$scheduled_category=phpfox::getService('gettingstarted')->getAllCategoryMail();
		$boolean_id=0;
		if(count($scheduled_mail)==0)
		{
			Phpfox_Error::set(_p('gettingstarted.id_not_found'));
			$boolean_id=1;
		}
		else
		$this->template()->assign(array('aForms' => array('message' => $scheduled_mail['message'])));

		$this->template()->assign(array(
               'scheduled_category' => $scheduled_category,
               'boolean_id' => $boolean_id,
               'scheduled_mail' => $scheduled_mail,
		));
		$this->template()->setBreadCrumb(_p('gettingstarted.edit_schedule_mail'), $this->url()->makeUrl('admincp.gettingstarted.editscheduledmail.id_'.$id));
		$this->template()->setEditor(array(
                'load' => 'simple',

		));

	}
}
?>
