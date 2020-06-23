<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Mail_Process extends Phpfox_Service
{

        /**
         * @TODO: will split later update and insert
         * @param $aVals
         */
        public function addEmailTemplate($aVals)
        {

            $aRow = $this->database()->select('*')->from(Phpfox::getT('auction_email_template_data'))->where('email_template_id = ' . $aVals['email_template_id'] .' AND language_id = "'.$aVals['language_id'].'"')->execute('getSlaveRow');
             $oParseInput = Phpfox::getLib('parse.input');
            if ($aRow)
            {
                    $aUpdate = array(
                            'language_id'           => $aVals['language_id'],
                            'email_subject'         => $oParseInput->clean($aVals['email_subject']),
                            'email_template'        => $oParseInput->clean($aVals['email_template']),
                            'email_template_parsed' => $oParseInput->prepare($aVals['email_template']),
                    );
                    $this->database()->update(Phpfox::getT('auction_email_template_data'), $aUpdate, 'email_template_id = ' . $aVals['email_template_id'] .' AND language_id = "'.$aVals['language_id'].'"');
            } else
            {
                    $aInsert = array(
                            'language_id'           => $aVals['language_id'],                        
                            'email_template_id'     => $aVals['email_template_id'],
                            'email_subject'         => $oParseInput->clean($aVals['email_subject']),
                            'email_template'        => $oParseInput->clean($aVals['email_template']),
                            'email_template_parsed' => $oParseInput->prepare($aVals['email_template']),
                    );

                    $iId = $this->database()->insert(Phpfox::getT('auction_email_template_data'), $aInsert);
            }
        }

        /**
         * in case of sending email to user of this site, we only need user id to send them
         * @return true if sending successfully
         */
        public function sendEmailTo($iTemplateType = 0, $iProductId = 0, $aReceivers = array(), $aCustomEmail = array())
		{
                if (!$aReceivers || !$iProductId)
                {
                        return FALSE;
                }

                if (!is_array($aReceivers))
                {
                        $aReceivers = array($aReceivers);
                }

                $defaultLanguage = Phpfox::getService('auction.helper')->getDefaultLanguage();

                if (!$aCustomEmail)
                {
                   $aCustomEmail = Phpfox::getService('auction.mail')->getEmailMessageFromTemplate($iTemplateType , $defaultLanguage , $iProductId, $iReceiverId);
                }

                $aVal = array(
                        'email_message' => Phpfox::getLib('parse.input')->prepare($aCustomEmail['message']),
                        'email_subject' => $aCustomEmail['subject'],
                        'product_id' => $iProductId,
                        'receivers' => serialize($aReceivers),
                        'is_sent' => 0
                );


                Phpfox::getService('auction.mail.process')->saveEmailToQueue($aVal);

                Phpfox::getService('auction.mail.send')->sendEmailsInQueue();
				
              	return TRUE;
        }

        public function saveEmailToQueue($aVal)
        {
                $this->database()->insert(Phpfox::getT('auction_email_queue'), $aVal);
        }
}

?>