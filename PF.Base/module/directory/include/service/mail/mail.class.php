<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Service_Mail_Mail extends Phpfox_Service
{

        /**
         * key is value will be replaced, value is the field in aCampiagn array
         * remember every entry having a corresponding phrase with prefix keywordsub_
         * @var array
         */
        private $_aReplace = array(
                  '[site_name]' => 'site_name'
                , '[admin_name]' => 'admin_name'
                , '[business_name]' => 'business_name'
                , '[business_link]' => 'business_link'
                , '[owner_name]' => 'owner_name'
                , '[package_name]' => 'package_name'
                , '[user_name]' => 'user_name'
                , '[my_business_link]' => 'my_business_link'
                , '[business_name_1]' => 'business_name_1'
                , '[business_name_2]' => 'business_name_2'
                , '[business_name_3]' => 'business_name_3'
        );
        // match with [admin_reason] keyword in email template
        private $_types = array(
                'claim_business_successfully' => 1,
                'business_approved' => 2,
                'claim_request_approved' => 3,
                'create_business_successfully' => 4
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
                        ->from(Phpfox::getT('directory_email_template_data'))
                        ->where('email_template_id = ' . $iTemplateType.' AND language_id = "' . $iLanguageId.'"')
                        ->execute('getSlaveRow');

                if(count($aRow) == 0){
                    $aRow = $this->database()->select('*')
                        ->from(Phpfox::getT('directory_email_template_data'))
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
         * @by minhta
         * @param $iTemplateType, $iLanguageId, $BusinessId, $iInviterId
         * @return array
         */
        public function getEmailMessageFromTemplate($iTemplateType, $iLanguageId, $BusinessId, $iInviterId = 0)
        {
                $aTemplate = $this->getEmailTemplate($iTemplateType,$iLanguageId);

                $aBusiness = Phpfox::getService('directory')->getBusinessForEmail($BusinessId);

                $iTypeInviter = $this->getTypeInviterByEmailId($iTemplateType);

                if(count($iTypeInviter) > 0){
                    $iTypeInviter = $iTypeInviter['type'];
                }
                else{
                    $iTypeInviter = 'admin';
                }

                $sMessage = $this->parseTemplate($aTemplate['email_template_parsed'], $aBusiness, $iInviterId, $iTypeInviter);

                $sSubject = $this->parseTemplate($aTemplate['email_subject'], $aBusiness, $iInviterId, $iTypeInviter);

                return array(
                    'message' => $sMessage,
                    'subject' => $sSubject
                );
        }

        public function getTypeInviterByEmailId($iTemplateType){
                $aRow = $this -> database()
                            -> select('det.*')
                            -> from(Phpfox::getT("directory_email_template"), 'det')
                            -> where('det.email_template_id = '.$iTemplateType)
                            -> execute("getSlaveRow");

                return $aRow;
        }
        /**
         * parse text for showing on form based on the business
         * it will replace some predefined symbol by the corresponding text
         * @author TriLM
         * @param string $sToBeParsedText the text to be parsed 
         * @param int $iTypeInviter  ( 1:admin ,2 :owner ,3 :register)
         * @return
         */
        public function parseTemplate($sToBeParsedText, $aBusiness, $iInviterId = 0, $iTypeInviter = 'admin')
        {

                $aDirectory = array();
                //if a id of campaign is passed
                if (count($aBusiness) == 1)
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
                }


              $aDirectory['site_name'] = Phpfox::getParam('core.site_title');
              $aUser = Phpfox::getService('user')->getUser($iInviterId);
            
                switch ($iTypeInviter) {
                    case 'admin':
                        $aDirectory['admin_name'] = $aUser['full_name'];
                        break;
                    case 'owner':
                        $aDirectory['owner_name'] = $aUser['full_name'];
                        break;
                    case 'user':
                        $aDirectory['user_name'] =  $aUser['full_name'];
                        break;

                }
                $aLink = Phpfox::getLib('url')->permalink('directory', null, null) . 'view_mybusinesses/'; 
                $aDirectory['my_business_link'] =  $sLink = '<a href="' . $aLink . '" title = "" target="_blank">' . $aLink . '</a>';
                $aDirectory['owner_name'] = $aUser['full_name'];

                $aBeReplaced = array();
                $aReplace = array();

                //setup replace and be replaced array
                foreach ($this->_aReplace as $sBeReplaced => $sReplace)
                {
                    if (isset($aDirectory[$sReplace]))
                    {
                        $aBeReplaced[] = $sBeReplaced;
                        $aReplace[] = $aDirectory[$sReplace];
                    }
                }

                $sParsedText = str_replace($aBeReplaced, $aReplace, $sToBeParsedText);

                return $sParsedText;
        }

}

?>
