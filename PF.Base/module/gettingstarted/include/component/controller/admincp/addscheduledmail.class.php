<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_gettingstarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

class gettingstarted_component_controller_admincp_addscheduledmail extends Phpfox_Component{
	public function process()
	{
		$aVals = array();
		$aVals['time'] = "";
		$aVals['message']="";
		$aVals['active']="1";
		$aVals['name']="";
		$aVals['scheduledmail_id']=0;
		$aVals['description']="";
		$bool_test=0;
		if(isset($_POST['submit_addscheduledmail']) == true)
		{
			$aVals = $this->request()->get('val');
			if(!isset($aVals['unsubscribe']) || $aVals['unsubscribe'] == null)
			{
				$aVals['unsubscribe'] = 0;
			}
			else
			{
				$aVals['unsubscribe'] = 1;
			}
			if(!isset($aVals['active']) || $aVals['active'] == null)
			{
				$aVals['active'] = 0;
			}
			else
			{
				$aVals['active'] = 1;
			}
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
				$atime=explode(" ", $aVals['time']);
				if(preg_match($regexp, $aVals['time']) == 1)
				{
					$aVals['time'] = (int)$aVals['time'];
					if($aVals['time'] > 999)
					{
						$bool_test = 1;
						Phpfox_Error::set(_p('gettingstarted.time_is_invalid'));
					}
					else
					{
						$strTime = $aVals['time']."h";
					}

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
			if($bool_test == 0)
			{
				$aVals['time'] = $strTime;
				$issuccess = phpfox::getService("gettingstarted")->addScheduledMail($aVals);
				if($issuccess == false)
				{
					Phpfox_Error::set(_p('gettingstarted.add_scheduled_mail_false_because_time_that_you_add_it_has_existed_before'));
				}
				else
				{
					$this->url()->send('current',null,_p('gettingstarted.add_scheduled_mail_successfully'));
				}
			}

		}

		$scheduled_category = phpfox::getService('gettingstarted')->getAllCategoryMail();
		if(count($scheduled_category) >0)
		{
				
			if($aVals['scheduledmail_id'] == 0)
			{
				$aVals['description'] = _p($scheduled_category[0]['description']);
			}
			else
			{
				foreach($scheduled_category as $iKey=>$category)
				{
					if($category['scheduledmail_id'] == $aVals['scheduledmail_id'])
					{
						$aVals['description'] = _p($category['description']);
						break;
					}
				}
			}

		}

		$this->template()->assign(array(
               'scheduled_category' => $scheduled_category,
                'aScheduledMail' => $aVals,
		));
		$this->template()->setBreadCrumb(_p('gettingstarted.add_email'), $this->url()->makeUrl('admincp.gettingstarted.addscheduledmail'));
		$this->template()->assign(array('aForms' => array('message' => $aVals['message'])));
		$this->template()->setEditor(array(
                'load' => 'simple',

		));
			
	}
}
?>
