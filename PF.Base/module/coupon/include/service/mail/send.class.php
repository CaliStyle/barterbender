<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 */

class Coupon_Service_Mail_Send extends Phpfox_Service 
{

	 /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('coupon_email_queue');
    }
	
	public function sendEmailsInQueue($iPage = 0, $iPageSize = 500, $iCouponId = 0)
	{
		$sCond = 'is_sent = 0 ';
		if($iCouponId)
		{
			$sCond .= 'AND coupon_id = ' . $iCouponId;
		}

		$aEmails = $this->database()->select('*')
				->from($this->_sTable)
				->limit($iPage, $iPageSize)
				->where($sCond)
				->execute('getSlaveRows');

		foreach($aEmails as $aEmail)
		{
			$bIsSent = $this->send($aEmail['email_subject'], $aEmail['email_message'], unserialize($aEmail['receivers']));
			if($bIsSent)
			{
				$this->database()->update($this->_sTable, array('is_sent' => 1), 'id = ' . $aEmail['id']);
			}
		}
	}


	public function send($sSubject, $sMessage, $aReceivers)
	{
		if(!Phpfox::getService('coupon.mail.phpfoxmail')->to($aReceivers)		
				->subject($sSubject)
				->message($sMessage)
				->send())
		{
			return false;
		}

		return true;
	}

}

?>