<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Mail_Mail extends Phpfox_Service
{

        /**
         * key is value will be replaced, value is the field in aCampiagn array
         * remember every entry having a corresponding phrase with prefix keywordsub_
         * @var array
         */
        private $_aReplace = array(
                  '[site_name]' => 'site_name'
                , '[receiver_name]' => 'receiver_name'
                , '[user_name]' => 'user_name'
                , '[product_name]' => 'product_name'
                , '[symbol_currency]' => 'symbol_currency'
                , '[amount]' => 'amount'
                , '[date_time]' => 'date_time'
                , '[url]' => 'url'
        );
        // match with [admin_reason] keyword in email template
        private $_types = array(
                'someone_start_bidding_on_your_auction' => 1,
                'you_have_been_outbid_bid_again_now' => 2,
        );

        public function getAllReplaces()
        {
            return $this->_aReplace;
        }

        public function getTypesCode($sType)
        {
            if (isset($this->_types[$sType]))
            {
                    return $this->_types[$sType];
            } else
            {
                    return false;
            }
        }

        public function getAllEmailTypes()
        {
            return $this->_types;
		}

        public function getEmailTemplate($iTemplateType,$iLanguageId)
        {
                $aRow = $this->database()->select('*')
                        ->from(Phpfox::getT('auction_email_template_data'))
                        ->where('email_template_id = ' . $iTemplateType.' AND language_id = "' . $iLanguageId.'"')
                        ->execute('getSlaveRow');

                if(count($aRow) == 0){
                    $aRow = $this->database()->select('*')
                        ->from(Phpfox::getT('auction_email_template_data'))
                        ->where('email_template_id = ' . $iTemplateType.' AND language_id = "en"')
                        ->execute('getSlaveRow');
                }

                if (!isset($aRow['email_subject']))
                        $aRow['email_subject'] = "";
				if (!isset($aRow['email_template']))
                        $aRow['email_template'] = "";
                if (!isset($aRow['email_template_parsed']))
                        $aRow['email_template_parsed'] = "";

                return $aRow;
        }

        /**
         * get email template and generate message based on campaign_id
         * @TODO: static cache email template here , write test
         */
        public function getEmailMessageFromTemplate($sTemplateType, $iLanguageId,$iInviterId,$iProductId,$aExtraData = array())
        {
                $aTypeEmailTemplates = Phpfox::getService('auction.mail')->getAllEmailTypes();

                $aTemplate = $this->getEmailTemplate($aTypeEmailTemplates[$sTemplateType],$iLanguageId);

                $aProduct = Phpfox::getService('auction')->getQuickAuctionByProductId($iProductId);

                $sMessage = $this->parseTemplate($aTemplate['email_template_parsed'], $sTemplateType, $iInviterId ,$aProduct,$aExtraData);

                $sSubject = $this->parseTemplate($aTemplate['email_subject'], $sTemplateType, $iInviterId, $aProduct, $aExtraData);

                return array(
                    'message' => $sMessage,
                    'subject' => $sSubject
                );
        }

        /**
         * parse text for showing on form based on the business
         * it will replace some predefined symbol by the corresponding text
         * @author TriLM
         * @param string $sToBeParsedText the text to be parsed 
         * @param int $iTypeInviter  ( 1:admin ,2 :owner ,3 :register)
         * @return
         */
        public function parseTemplate($sToBeParsedText, $sTemplateType ,$iInviterId = 0, $aProductInfo, $aExtraData)
        {

            $aProduct = array();
                //if a id of campaign is passed
                /*if (count($aProduct) == 1)
                {
                    $aDirectory = $aBusiness[0];
                    $aDirectory['business_name'] = $aBusiness[0]['name'];
                    $aLink = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness[0]['business_id'], $aBusiness[0]['name']);
                    $sLink = '<a href="' . $aLink . '" title = "' . $aBusiness[0]['name'] . '" target="_blank">' . $aLink . '</a>';
                    $aDirectory['business_link'] = $sLink;
   
                }
                else{

                    $i = 1;
                    
                    foreach ($aBusiness as $key => $iBusiness) {
                        $aLink = Phpfox::getLib('url')->permalink('directory.detail', $iBusiness['business_id'], $iBusiness['name']);
                        $sLink = '<a href="' . $aLink . '" title = "' . $iBusiness['name'] . '" target="_blank">' . $iBusiness['name'] . '</a>';
                        $aDirectory['business_name_'.$i] = $sLink;
                        $i++;
                    }      
                }*/


            $aProduct['site_name'] = Phpfox::getParam('core.site_title');

            /*info recever*/
            $aReceiver = Phpfox::getService('user')->getUser($iInviterId);
            $aProduct['receiver_name'] = $aReceiver['full_name'];
            
            /*info product*/
            $aLink = Phpfox::getLib('url')->permalink('auction.detail', $aProductInfo['product_id'], $aProductInfo['name']);
            $aProduct['product_name'] = '<a href="' . $aLink . '" title = "' . $aProductInfo['name'] . '" target="_blank">' . $aProductInfo['name'] . '</a>';
                    
            switch ($sTemplateType) {
                case 'someone_start_bidding_on_your_auction':
                    /*info bidder name*/

                     $aBidder = Phpfox::getService('user')->getUser($aExtraData['bidder_id']);

                     $aProduct['user_name'] = $aBidder['full_name'];
                     
                     $aProduct['symbol_currency'] = $aExtraData['symbol_currency'];
                     $aProduct['amount'] = $aExtraData['amount'];
                     $aProduct['date_time'] = $aExtraData['date_time'];
                     
                     /*url bid history*/
                     $aProduct['url'] = $aExtraData['url'];
                    break;
                
                default:
                    # code...
                    break;
            }
            /*$aUser = Phpfox::getService('user')->getUser($iInviterId);
            
            $aDirectory['user_name'] =  $aUser['full_name'];
                 
            $aLink = Phpfox::getLib('url')->permalink('directory', null, null) . 'view_mybusinesses/'; 
            $aDirectory['my_business_link'] =  $sLink = '<a href="' . $aLink . '" title = "" target="_blank">' . $aLink . '</a>';
            $aDirectory['owner_name'] = $aUser['full_name'];
            */

            $aBeReplaced = array();
            $aReplace = array();

            //setup replace and be replaced array
            foreach ($this->_aReplace as $sBeReplaced => $sReplace)
            {
                if (isset($aProduct[$sReplace]))
                {
                    $aBeReplaced[] = $sBeReplaced;
                    $aReplace[] = $aProduct[$sReplace];
                }
            }


            /*echo '<pre>';
            print_r($_aReplace);
            die;*/
            $sParsedText = str_replace($aBeReplaced, $aReplace, $sToBeParsedText);

            return $sParsedText;
        }

}

?>
