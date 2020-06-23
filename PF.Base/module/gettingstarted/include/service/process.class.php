<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_ScheduledMail
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');
if(!defined("TIME_DEV"))
{
	define("TIME_DEV",60*60);
}

class Gettingstarted_service_process extends Phpfox_Service{

	public function InsertMailToQueue($is_testing = false)
	{
		
		$aSettings = phpfox::getService("gettingstarted.settings")->getSettings(0);
		
		
		if($aSettings['active_email_remainder'] == '1')
		{
			$aScheduledmail = Phpfox::getService('gettingstarted')->getScheduledMail();

			$aUsers = phpfox::getService("gettingstarted")->getUsers();

			foreach($aUsers as $iKey => $Users)
			{
				$aVals=array();
				
				$aVals['to'] = $Users['user_id'];
				$aVals['email'] = $Users['email'];
				 

				$alogin=Phpfox::getService("gettingstarted")->getLastestTypeId($Users['user_id'],"login");
				$alogout=Phpfox::getService("gettingstarted")->getLastestTypeId($Users['user_id'],"logout");
				$asession_login = Phpfox::getService('gettingstarted')->getLastestTypeId($Users['user_id'],"session_login");
				
				
                if(!$asession_login)
                {
                    $asession_login = Phpfox::getService('gettingstarted')->getLastestTypeId($Users['user_id'],"login");
                }

				$aUser_Ip=Phpfox::getService('gettingstarted')->getUsersIp_type($Users['user_id'],"register");

				// $aScheduledmail get all from table "gettingstarted"
				foreach($aScheduledmail as $Scheduledmail)
				{

					$Scheduledmail['time']=(int)$this->CalTime($Scheduledmail['time']);
					$aVals['message'] = $Scheduledmail['message'];
					$aVals['subject'] = $Scheduledmail['name'];
					$aVals['subject'] = str_replace("[full_name]", $Users['full_name'], $aVals['subject']);
					$aVals['subject'] = str_replace("[user_name]", $Users['user_name'], $aVals['subject']);
					$aVals['message'] = str_replace("[full_name]", $Users['full_name'], $aVals['message']);
					$aVals['message'] = str_replace("[user_name]", $Users['user_name'], $aVals['message']);
					$aVals['user_id'] = $Users['user_id'];
					$current_time = PHPFOX_TIME;
					$aVals['time_stamp'] = $current_time;
					
                 	$time_action = isset($aUser_Ip['time_stamp']) ? $aUser_Ip['time_stamp'] : 0;
                    $time = (isset($aUser_Ip['time_stamp']) ? $aUser_Ip['time_stamp'] : 0)+ (TIME_DEV*$Scheduledmail['time']);
					$is_date=$current_time-$time;
					$aVals['scheduledmail_id']=$Scheduledmail['scheduledmail_id'];
					$aVals['is_sent']=1;
					$aVals['time']=$Scheduledmail['time'];
					
					if($Scheduledmail['active'] == 1)
					{
						$scheduledmail_name = $Scheduledmail['scheduledmail_name'];
						switch ($scheduledmail_name)
						{
							case 'register':
								if(( $is_date>=0 || $is_testing == true )&& ($time_action > $Scheduledmail['time_stamp']) )
								{
									$aVals['time_stamp'] = PHPFOX_TIME;
									//if($is_date<86400)
									{
										if(count(Phpfox::getService("gettingstarted")->get_historysend($aVals))==0)
										{
											Phpfox::getService("gettingstarted")->add_historysend($aVals);
											Phpfox::getService("gettingstarted")->addQueryLetter($aVals);
                                    		Phpfox_error::log('register: time_stamp of us: ' . $time . ' system: ' . $current_time . 'data: ' . serialize($aVals), '', 1); 
										}
									}
								}
								break;
								
							case 'logout':
								
								if(!isset($alogin['time_stamp']))
								{
									$alogin['time_stamp'] = 0;
								}

								if(!isset($alogout['time_stamp']))
								{
									$alogout['time_stamp'] = 0;

								}

								if($alogin['time_stamp'] > $alogout['time_stamp'])
								{
									$session_timeout = phpfox::getParam('log.active_session')*60;
									$iTimeSendMail = $asession_login['time_stamp'] +  $session_timeout + (TIME_DEV * $Scheduledmail['time']);
									
									if($iTimeSendMail <= $current_time)
									{
										Phpfox::getService("gettingstarted")->add_historysend($aVals);
										Phpfox::getService("gettingstarted")->addQueryLetter($aVals);
									}
								}
								else
								{
									$iTimeSendMail = $alogout['time_stamp'] + (TIME_DEV * $Scheduledmail['time']);
									if($iTimeSendMail <= $current_time)
									{
										
										Phpfox::getService("gettingstarted")->add_historysend($aVals);
										Phpfox::getService("gettingstarted")->addQueryLetter($aVals);
									}
									Phpfox::getService("gettingstarted")->delete_historysend($aVals);
								}
								
								break;
								
								
							case 'blog':
							case 'video':
							case 'photo':
							case 'poll':
							case 'event':
								
								$aRow_module=Phpfox::getService("gettingstarted")->getModule($Users['user_id'],$Scheduledmail['scheduledmail_name']);
								
								
								
								if(count($aRow_module)==0)
								{

								}
								else
								{
									$time = $aRow_module['time_stamp']+(TIME_DEV*$Scheduledmail['time']);
									if($time <= $current_time)
									{
										Phpfox::getService("gettingstarted")->add_historysend($aVals);
										Phpfox::getService("gettingstarted")->addQueryLetter($aVals);
									}
								}
								break;
						}
						
					}

				}
			}
		}
	}

	public function processsendmail($aVals)
	{
		$aVals['to'] = trim($aVals['to']);
		$aDetails = Phpfox::getService('user')->getUser($aVals['user_id']);
		if (!isset($aDetails['user_id']))
		{
			return false;
		}
		if ($aVals['to'] == Phpfox::getUserId() && !Phpfox::getUserParam('mail.can_message_self'))
		{
			return Phpfox_Error::set(_p('mail.you_cannot_message_yourself'));
		}

		$aVals = array_merge($aVals, $aDetails);
		$oFilter = Phpfox::getLib('parse.input');
		$oParseOutput = Phpfox::getLib('parse.output');

		$bHasAttachments = (Phpfox::getUserParam('mail.can_add_attachment_on_mail') && !empty($aVals['attachment']));

		if (isset($aVals['parent_id']))
		{
			$aMail = $this->database()->select('m.mail_id, m.owner_user_id, m.subject, u.email, u.language_id')
			->from($this->_sTable, 'm')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = m.owner_user_id')
			->where('m.mail_id = ' . (int) $aVals['parent_id'] . ' AND viewer_user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRow');

			if (!isset($aMail['mail_id']))
			{
				return Phpfox_Error::set(_p('mail.not_a_valid_message'));
			}

			$aVals['user_id'] = $aMail['owner_user_id'];
			$aVals['subject'] = $aMail['subject'];
			$aVals['email'] = $aMail['email'];
			$aVals['language_id'] = $aMail['language_id'];
		}

		$aVals['subject'] = (isset($aVals['subject']) ? $oFilter->clean($aVals['subject'], 255) : null);
		$aInsert = array(
                'parent_id' => (isset($aVals['parent_id']) ? $aVals['parent_id'] : 0),
                'subject' => $aVals['subject'],
                'preview' => $oFilter->clean(strip_tags(Phpfox::getLib('parse.bbcode')->cleanCode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $aVals['message']))), 255),
                'owner_user_id' => Phpfox::getUserId(),
                'viewer_user_id' => $aVals['user_id'],
                'viewer_is_new' => 1,
                'time_stamp' => PHPFOX_TIME,
                'time_updated' => PHPFOX_TIME,
                'total_attachment' => ($bHasAttachments ? Phpfox::getService('attachment')->getCount($aVals['attachment']) : 0),
		);

		$iId = $this->database()->insert(phpfox::getT('mail'), $aInsert);

		$this->database()->insert(Phpfox::getT('mail_text'), array(
                        'mail_id' => $iId,
                        'text' => $oFilter->clean($aVals['message']),
                        'text_parsed' => $oFilter->prepare($aVals['message'])
		)
		);

		// Send the user an email
		$sLink = Phpfox::getLib('url')->makeUrl('mail.view', array('id' => $iId));
		Phpfox::getLib('mail')->to($aVals['user_id'])
		->subject(array('mail.full_name_sent_you_a_message_on_site_title', array('full_name' => Phpfox::getUserBy('full_name'), 'site_title' => Phpfox::getParam('core.site_title')), false, null,$aVals['language_id']))
		->message(array('mail.full_name_sent_you_a_message_subject_subject', array(
                                        'full_name' => Phpfox::getUserBy('full_name'),
                                        'subject' => $aVals['subject'],
                                        'message' => $oFilter->clean(strip_tags(Phpfox::getLib('parse.bbcode')->cleanCode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $aVals['message'])))),
                                        'link' => $sLink
		)
		)
		)
		->notification('mail.new_message')
		->send();

		(Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('mail_send', $iId, $aVals['user_id']) : null);

		// If we uploaded any attachments make sure we update the 'item_id'
		if ($bHasAttachments)
		{
			Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
		}

		(($sPlugin = Phpfox_Plugin::get('mail.service_process_add')) ? eval($sPlugin) : false);

		if(count($iId) > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function SendMail($is_testing = false)
	{
		$aSettings=phpfox::getService("gettingstarted.settings")->getSettings(0);
		if($aSettings['active_email_remainder'] == '1')
		{
			if(isset($aSettings['number_of_letters'])==null)
			{
				$iLimit=100;
			}
			else
			{
				$iLimit=$aSettings['number_of_letters'];
			}
			$aRows_Mail = Phpfox::getService("gettingstarted")->getQueryLetter($iLimit);
			$current_time = PHPFOX_TIME;

			foreach($aRows_Mail as $iKey=>$Rows_Mail)
			{
				if((($Rows_Mail['time_stamp'] <= $current_time) || $is_testing  == true))
				{
					$bActive = $this->isActiveLetter($Rows_Mail['scheduledmail_letter_id']);

					if($bActive)
					{
						$aVals = array();
						$aVals['to'] = $Rows_Mail['user_id'];
						$aVals['subject'] = $Rows_Mail['subject'];
						$aVals['message'] = $Rows_Mail['message'];
						$aVals['user_id'] = $Rows_Mail['user_id'];
						$aVals['unsubscribe_email'] = $Rows_Mail['unsubscribe_email'];
						$aVals['scheduledmail_id'] = $Rows_Mail['scheduledmail_id'];

						$this->sendMailToUser($aVals);
						
						Phpfox::getService('gettingstarted')->updateSchedueLetter($current_time, $Rows_Mail['scheduledmail_id'], $Rows_Mail['user_id']);
					}
				}
			}
		}
	}

	public function isActiveLetter($scheduledmail_letter_id)
	{
		$aIsActiveMail = Phpfox::getLib('database')->select('l.scheduledmail_id, g.active')
		->from(Phpfox::getT('gettingstarted_letter'), 'l')
		->join(Phpfox::getT('gettingstarted'), 'g', 'g.scheduledmail_id = l.scheduledmail_id')
		->where('l.scheduledmail_letter_id = '.$scheduledmail_letter_id)
		->execute('getRow');
		if($aIsActiveMail['active'] == '1')
		{
			return true;
		}
		return  false;

	}
	public function sendMailToUser($aVals)
	{
		 
		
		if($aVals['to'])
		{
			$sLink = Phpfox::getLib('url')->makeUrl('');
			$oFilter = Phpfox::getLib('parse.input');
			if($aVals['unsubscribe_email'] == 0)
			{
				echo " Reminder Email sent to userID= ". $aVals['to']." ". (isset($aVals['email'])?$aVals['email']:'') ." : ".	$aVals['subject']."<br/>"; 
				
				Phpfox::getLib('mail')->to($aVals['to'])
				->subject($aVals['subject'])
				->message(array('gettingstarted.gettingstarted_full_name_sent_you_a_message_subject_without_unsubscribe', array(
												'full_name' => Phpfox::getUserBy('full_name'),
												'subject' => $aVals['subject'],
												'message' => $oFilter->clean(strip_tags(Phpfox::getLib('parse.bbcode')->cleanCode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $aVals['message'])))),
												'link' => $sLink)))
				->notification('mail.new_message')
				->send();
				
				
				return true;
			}
			else
			{
				$unsublink = Phpfox::getLib('url')->makeUrl('gettingstarted.unsubscribe.id_'.$aVals['to'].'type'.$aVals['scheduledmail_id']);
				//getLibClass
				echo $unsublink."<BR/>";
				
				echo " Reminder Email sent to userID= ". $aVals['to']." ". (isset($aVals['email'])?$aVals['email']:'') ." : ".	$aVals['subject']."<br/>"; 
				
				Phpfox::getLib('mail')
				->to($aVals['to'])
				->subject($aVals['subject'])
				->message(array('gettingstarted.gettingstarted_gettingstarted_full_name_sent_you_a_message_subject_with_unsubscribe', array(
												'full_name' => Phpfox::getUserBy('full_name'),
												'subject' => $aVals['subject'],
												'message' => $oFilter->clean(strip_tags(Phpfox::getLib('parse.bbcode')->cleanCode(str_replace(array('&lt;', '&gt;'), array('<', '>'), $aVals['message'])))),
												'link' => $sLink,
												'unsublink' => $unsublink)))
				->notification('mail.new_message')
				->send();
				
				 
				
				
				return true;
			}

		}
		return false;
	}
	public function updateActivity($iId, $iType)
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		$this->database()->update(phpfox::getT('gettingstarted'),array(
			   'active' => $iType,
		),'scheduledmail_id='.$iId);
	}

	public function deleteMultiple($aIds)
	{
		foreach ($aIds as $iId)
		{
			Phpfox::getService("gettingstarted")->delete_scheduledmail_issend($iId);
			Phpfox::getService('gettingstarted')->delete_schedulemail_letter($iId);
			Phpfox::getService("gettingstarted")->deleteScheduledMail($iId);
		}
		return true;
	}

        public function updateCounter($iId, $bMinus = false)
	{
		$this->database()->query("
			UPDATE " . $this->_sTable . "
			SET total_comment = total_comment " . ($bMinus ? "-" : "+") . " 1
			WHERE article_id = " . (int) $iId . "
		");
	}

        public function __construct()
	{
		$this->_sTable = Phpfox::getT('gettingstarted_article');
	}

	public function CalTime($value)
	{

		$value=trim($value);
		$atime=explode(" ", $value);

		$hour=0;
		if(is_numeric($value) == true)
		return $value;

		foreach($atime as $time)
		{
			$b=substr($time, strlen($time)-1, 1);
			$a=substr($time,0,strlen($time)-1);
			switch($b)
			{
				case "w":
					$hour+= $a*7*24;
					break;
				case "d":
					$hour+= $a*24;
					break;
				case "m";
				$hour+= $a*30*24;
				break;
				case "h";
				$hour += $a;
			}
		}
		return $hour;
	}

	public function updateView($iId)
	{
		$this->database()->query("
			UPDATE " . phpfox::getT('gettingstarted_article') . "
			SET total_view = total_view + 1
			WHERE article_id = " . (int) $iId . "
		");			

		return true;
	}
}
?>
