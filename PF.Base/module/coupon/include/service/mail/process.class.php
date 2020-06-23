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
class Coupon_Service_Mail_Process extends Phpfox_Service
{

        /**
         * @TODO: will split later update and insert
         * @param $aVals
         */
        public function addEmailTemplate($aVals)
        {

                $aRow = $this->database()->select('*')->from(Phpfox::getT('coupon_email_template'))->where('type=' . $aVals['type_id'])->execute('getSlaveRow');
				 $oParseInput = Phpfox::getLib('parse.input');
                if ($aRow)
                {
                        $aUpdate = array(
                                'email_subject' 		=> $oParseInput->clean($aVals['email_subject']),
                                'email_template' 		=> $oParseInput->clean($aVals['email_template']),
                                'email_template_parsed' => $oParseInput->prepare($aVals['email_template']),
                        );
                        $this->database()->update(Phpfox::getT('coupon_email_template'), $aUpdate, 'type=' . $aVals['type_id']);
                } else
                {
                        $aInsert = array(
                                'type' => $aVals['type_id'],
                                'email_subject' 		=> $oParseInput->clean($aVals['email_subject']),
                                'email_template' 		=> $oParseInput->clean($aVals['email_template']),
                                'email_template_parsed' => $oParseInput->prepare($aVals['email_template']),
                        );

                        $iId = $this->database()->insert(Phpfox::getT('coupon_email_template'), $aInsert);
                }
        }

        /**
         * in case of sending email to user of this site, we only need user id to send them
         * @by TienNPL
         * @param type $name purpose
         * @return true if sending successfully
         */
        public function sendEmailTo($iTemplateType = 0, $iCouponId = 0, $aReceivers = array(), $aCustomEmail = array())
		{
                if (!$aReceivers || !$iCouponId)
                {
                        return FALSE;
                }

                if (!is_array($aReceivers))
                {
                        $aReceivers = array($aReceivers);
                }

                if (!$aCustomEmail)
                {
                   $aCustomEmail = Phpfox::getService('coupon.mail')->getEmailMessageFromTemplate($iTemplateType, $iCouponId);
                }

                $aVal = array(
                        'email_message' => Phpfox::getLib('parse.input')->prepare($aCustomEmail['message']),
                        'email_subject' => $aCustomEmail['subject'],
                        'coupon_id' => $iCouponId,
                        'receivers' => serialize($aReceivers),
                        'is_sent' => 0,
                        'time_stamp' => PHPFOX_TIME,
                );
                Phpfox::getService('coupon.mail.process')->saveEmailToQueue($aVal);

                Phpfox::getService('coupon.mail.send')->sendEmailsInQueue();
				
              	return TRUE;
        }

        public function saveEmailToQueue($aVal)
        {
                $this->database()->insert(Phpfox::getT('coupon_email_queue'), $aVal);
        }
}

?>