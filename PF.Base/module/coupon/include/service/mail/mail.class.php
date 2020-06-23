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
class Coupon_Service_Mail_Mail extends Phpfox_Service
{

        /**
         * key is value will be replaced, value is the field in aCampiagn array
         * remember every entry having a corresponding phrase with prefix keywordsub_
         * @var array
         */
        private $_aReplace = array(
                '[social_network_site]' => 'social_network_site'
                , '[owner_name]' => 'owner_name'
                , '[coupon_name]' => 'coupon_name'
                , '[site_name]' => 'site_name'
                , '[coupon_link]' => 'coupon_link'
                , '[start_date]' => 'start_date'
                , '[end_date]' => 'end_date'
                , '[expired_date]' => 'expired_date'
                , '[claimer_name]' => 'claimer_name'
                , '[claimer_email]' => 'claimer_email'
                , '[coupon_code]' => 'coupon_code'
                , '[coupon_address]' => 'coupon_address'
        );
        // match with [admin_reason] keyword in email template
        private $_types = array(
                'createcouponsuccessful_owner' => 1,
                'couponapproved_owner' => 2,
                'couponfeatured_owner' => 3,
                'startrunningcoupon_owner' => 4,
                'couponclosed_owner' => 5,
                'couponclaimed_owner' => 6,
                'couponclaimed_claimer' => 7
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

        public function getAllTypes()
        {
            return $this->_types;
		}

        public function getEmailTemplate($iTemplateType)
        {
                $aRow = $this->database()->select('*')
                        ->from(Phpfox::getT('coupon_email_template'))
                        ->where('type = ' . $iTemplateType)
                        ->execute('getSlaveRow');

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
         * @param type $name purpose
         * @return
         */
        public function getEmailMessageFromTemplate($iTemplateType, $iCouponId, $iInviterId = 0, $iClaimerId = 0)
        {
                $aTemplate = $this->getEmailTemplate($iTemplateType);

                $aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId);

                $sMessage = $this->parseTemplate($aTemplate['email_template_parsed'], $aCoupon, $iInviterId, $iClaimerId);

                $sSubject = $this->parseTemplate($aTemplate['email_subject'], $aCoupon, $iInviterId, $iClaimerId);

                return array(
                    'message' => $sMessage,
                    'subject' => $sSubject
                );
        }

        /**
         * parse text for showing on form based on the coupon
         * it will replace some predefined symbol by the corresponding text
         * @author TienNPL
         * @param string $sToBeParsedText the text to be parsed 
         * @param array $aCoupon the corresponding coupon
         * @return
         */
        public function parseTemplate($sToBeParsedText, $aCoupon, $iInviterId = 0, $iClaimerId = 0)
        {
                //if a id of campaign is passed
                if (!is_array($aCoupon))
                {
                    $aCoupon = Phpfox::getService('coupon')->getCouponById($aCoupon);
                }

                $aCoupon['site_name'] = Phpfox::getParam('core.site_title');
				$aCoupon['social_network_site'] = Phpfox::getParam('core.site_title');
				
                if ($iInviterId)
                {
                    $aUser = Phpfox::getService('user')->getUser($iInviterId);
                    $aCoupon['inviter_name'] = $aUser['full_name'];
                }
			
				if ($iClaimerId)
				{
					$aUser = Phpfox::getService('user')->getUser($iClaimerId);
					$aCoupon['claimer_name'] = $aUser['full_name'];
					$aCoupon['claimer_email'] = $aUser['email'];
				}
				
                $sDateFormat = "d F, Y";
                $aLink = Phpfox::getLib('url')->permalink('coupon.detail', $aCoupon['coupon_id'], $aCoupon['title']);
                $sLink = '<a href="' . $aLink . '" title = "' . $aCoupon['title'] . '" target="_blank">' . $aLink . '</a>';
                $aCoupon['coupon_link'] = $sLink;
				$aCoupon['owner_name'] = $aCoupon['full_name'];
				$aCoupon['coupon_name'] = $aCoupon['title'];
                $aCoupon['start_date'] = Phpfox::getTime($sDateFormat, $aCoupon['start_time'],false);
                $aCoupon['end_date'] = Phpfox::getTime($sDateFormat, $aCoupon['end_time'],false);
				$aCoupon['coupon_code'] = $aCoupon['code'];
				$aCoupon['coupon_address'] = $aCoupon['location_venue'];
				
                if (!$aCoupon['expire_time'])
                {
                    $aCoupon['expire_time'] = _p('unlimited_time_upper');
                } else
                {
                    $aCoupon['expire_time'] = Phpfox::getTime($sDateFormat, $aCoupon['expire_time'],false);
                }
				$aCoupon['expired_date'] =  $aCoupon['expire_time'];
				
                $aBeReplaced = array();
                $aReplace = array();

                //setup replace and be replaced array
                foreach ($this->_aReplace as $sBeReplaced => $sReplace)
                {
                    if (isset($aCoupon[$sReplace]))
                    {
                        $aBeReplaced[] = $sBeReplaced;
                        $aReplace[] = $aCoupon[$sReplace];
                    }
                }
                $sParsedText = str_replace($aBeReplaced, $aReplace, $sToBeParsedText);
                return $sParsedText;
        }

}

?>
