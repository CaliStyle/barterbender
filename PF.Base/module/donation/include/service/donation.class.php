<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: Donation.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class Donation_Service_Donation extends Phpfox_Service {

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('donation_pages');
    }

    public function getCurrentCurrencies($sGateways = 'paypal')
    {
        $aRow = $this->database()
                ->select('data')
                ->from(Phpfox::getT('donation_gateway_setting'))
                ->where('type_id = \'paypal_currency\'')
                ->execute('getRow');

        if (isset($aRow['data']) && $aRow['data'] != '')
        {
            $aCurrencies = unserialize($aRow['data']);
            return $aCurrencies ? $aCurrencies : array('USD');
        }
        else
        {
            // in case of no currency, we use default currency is US dollar
            return array('USD');
        }
    }

    public function getPageIdFromUrl()
    {
        $sReq1 = $this->request()->get('req1');
        $iPageId = 0;
        if ($sReq1 === 'pages')
        {
            $iPageId = (int) $this->request()->get('req2');
        }
        else
        {
            //in case of vanity URL
            $aRow = $this->database()
                    ->select('page_id')
                    ->from(Phpfox::getT('pages_url'))
                    ->where('vanity_url = \'' . $sReq1 . '\'')
                    ->execute('getSlaveRow');
            $iPageId = isset($aRow['page_id']) ? (int) $aRow['page_id'] : 0;
        }

        return $iPageId;
    }

    public function getInvoice($iId)
    {
        $aRow = $this->database()
                ->select('*')
                ->from(Phpfox::getT('donation_invoice'))
                ->where('invoice_id = ' . $iId)
                ->execute('getRow');
        $aInvoice = array();
        if (Phpfox::getLib('parse.format')->isSerialized($aRow['invoice_content']))
        {
            $aInvoice = unserialize($aRow['invoice_content']);
        }
        return $aInvoice;
    }

    public function checkGuestName($sName)
    {
        $guestnameRegex = "/.*/";
        preg_match($guestnameRegex, $sName, $aMatchs);
        if ($aMatchs)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getDonationButtonDefaultImagePath()
    {
        $aRow = $this->database()
                ->select('*')
                ->from(Phpfox::getT('donation_image'))
                ->where('type = \'default_button\'')
                ->execute('getRow');
        return Phpfox::getParam('core.path') . $aRow['image_path'];
    }

    public function getDonationButtonAdminImagePath()
    {
        $aRow = $this->database()
                ->select('*')
                ->from(Phpfox::getT('donation_image'))
                ->where('type = \'admin_button\'')
                ->execute('getRow');
        if ($aRow && !empty($aRow['image_path']))
        {
            return Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aRow['server_id'],
                        'path' => 'core.path_file',
                        'file' => $aRow['image_path'],
                        'suffix' => '_medium',
                        'return_url' => true
                        )
            );
        }
        return false;
    }

    // is using default button image
    public function isUsingDefaultButtonImage()
    {
        $aRow = $this->database()
                ->select('*')
                ->from(Phpfox::getT('donation_image'))
                ->where('type = \'admin_button\'')
                ->execute('getRow');
        if ($aRow)
        {
            // because we base on the admin button to determine this
            return $aRow['is_default'] ? 0 : 1;
        }
        else
        {
            return false;
        }
    }

    public function getDonationButtonImagePath()
    {
        $aRow = $this->database()
                ->select('*')
                ->from(Phpfox::getT('donation_image'))
                ->where('type = \'admin_button\'')
                ->execute('getRow');
        if ($aRow && $aRow['is_default'] && $aRow['image_path'] != '')
        {
            return Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aRow['server_id'],
                        'path' => 'core.path_file',
                        'file' => $aRow['image_path'],
                        'suffix' => '_medium',
                        'return_url' => true
                        )
            );
        }
        // we have a built in button in default and in error case
        return $this->getDonationButtonDefaultImagePath();
    }

    public function checkPermissions($aPermissionTypes, $aVals = null)
    {
        if (!is_array($aPermissionTypes))
        {
            $aPermissionTypes = array($aPermissionTypes);
        }
        foreach ($aPermissionTypes as $sPermissionType)
        {
            switch ($sPermissionType) {
                case 'can_add_donation_on_own_page':
                    if ($aVals)
                    {
                        if ($this->isPageOwner($aVals['iPageId'], Phpfox::getUserId()))
                        {
                            if (!Phpfox::getUserParam('donation.can_add_donation_on_own_page'))
                            {
                                return false;
                            }
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                    break;
                case 'can_donate':

                    if (!Phpfox::getUserParam('donation.can_donate'))
                    {
                        return false;
                    }
                    else
                    {
                        // in case the page owner doesn't have right to add donation on his page
                        if ($aVals !== null && isset($aVals['iPageId']))
                        {
                            $iPageId = $aVals['iPageId'];
                            $aRow = Phpfox::getService('pages')->getPage($iPageId);
                            //var_dump($aRow);
                            $bResult = Phpfox::getService('donation')->getUserParamFromUserId($aRow['user_id'], 'donation.can_add_donation_on_own_page');
                            if (!$bResult)
                            {
                                return false;
                            }
                        }
                    }
                    break;
                default :
                    break;
            }
        }
        return true;
    }

    public function getUserParamFromUserId($iUserId, $sName)
    {
        $aRow = $this->database()
                ->select('user_group_id')
                ->from(Phpfox::getT('user'))
                ->where('user_id = ' . $iUserId)
                ->execute('getRow');
        if (isset($aRow['user_group_id']))
        {
            return Phpfox::getService('user.group.setting')->getGroupParam($aRow['user_group_id'], $sName);
        }
        return false;
    }

    public function isPageOwner($iPageId, $iUserId)
    {
        if ($iPageId == -1)
        {
            if (Phpfox::isAdmin())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        $aPage = Phpfox::getService('pages')->getPage($iPageId);
        if (((int) $aPage['user_id']) === $iUserId)
        {
            return true;
        }
        return false;
    }

    public function getLastDonateTime($iUserId)
    {
        return $this->database()
                        ->select('time_stamp')
                        ->from($this->_sTable)
                        ->where('user_id = ' . (int) $iUserId)
                        ->order('time_stamp DESC')
                        ->execute('getField');
    }

    public function getUser($iUserId)
    {
        $aUser = $this->database()
                ->select(Phpfox::getUserField())
                ->from(Phpfox::getT('user'), 'u')
                ->where('u.user_id = ' . (int) $iUserId)
                ->execute('getRow');
        return $aUser;
    }

    public function getTotalDonor($iPageId)
    {
        $aGuest = $this->database()
                ->select('COUNT(*) total')
                ->from(Phpfox::getT('donation_guest_donation'))
                ->where('page_id = ' . $iPageId)
                ->execute('getRow');
        $aUser = $this->database()
                ->select('COUNT(*) total')
                ->from(Phpfox::getT('donation_pages'))
                ->where('page_id = ' . $iPageId)
                ->execute('getRow');
        $iTotalGuest = isset($aGuest['total']) ? $aGuest['total'] : 0;
        $iTotalUser = isset($aUser['total']) ? $aUser['total'] : 0;
        return ($iTotalGuest + $iTotalUser);
    }

    public function getDonorList($iOffSet, $iLimit, $iPageId)
    {
        $aRows = null;
        if ($iLimit > 0)
        {
            $aRows = $this->database()->select('und.*, und.user_id as temp_id, ' . Phpfox::getUserField())
                    ->from('(
                                            (SELECT d.user_id,  d.is_guest,d.quanlity, d.time_stamp, d.page_id, d.not_show_name, d.not_show_money, d.donation_id, d.currency
                                                    FROM ' . Phpfox::getT('donation_pages') . ' d WHERE d.page_id = ' . $iPageId . ')
                                            union
                                            (SELECT g.guest_name, g.is_guest, g.quanlity, g.time_stamp, g.page_id, g.not_show_name, g.not_show_money, g.donation_id , g.currency
                                                    FROM ' . Phpfox::getT('donation_guest_donation') . ' g WHERE g.page_id = ' . $iPageId . ') 
                                            )', 'und')
                    ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = und.user_id ')
                    ->order('time_stamp DESC')
                    ->limit($iOffSet, $iLimit)
                    ->execute('getSlaveRows');
        }
        return $aRows;
    }

    public function getDonatedUserBlock($aCond, $sSort = 'friend.time_stamp DESC', $iPage = '', $sLimit = '', $bCount = true, $bAddDetails = false, $bIsOnline = false, $iUserId = null, $bIncludeList = false, $iListId = 0, $iPageId = 0)
    {
        $sSort = '';
        $bIsListView = ((Phpfox::getLib('request')->get('view') == 'list' || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getLib('request')->getInt('list'))) ? true : false);
        $iCnt = ($bCount ? 0 : 1);
        $aRows = array();
        if ($sPlugin = Phpfox_Plugin::get('friend.service_friend_get'))
        {
            eval($sPlugin);
        }
        if ($bCount === true)
        {
            if ($bIsOnline === true)
            {
                $this->database()->join(Phpfox::getT('log_session'), 'ls', 'ls.user_id = friend.friend_user_id AND ls.im_hide = 0');
            }
            if ($iUserId !== null)
            {
                $this->database()->innerJoin('(SELECT friend_user_id FROM ' . Phpfox::getT('friend') . ' WHERE is_page = 0 AND user_id = ' . $iUserId . ')', 'sf', 'sf.friend_user_id = friend.friend_user_id');
            }
            if ($bIsListView)
            {
                $this->database()->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.friend_user_id = friend.friend_user_id');
            }
            if ((int) $iListId > 0)
            {
                $this->database()->innerJoin(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = ' . (int) $iListId . ' AND fld.friend_user_id = friend.friend_user_id');
            }
            $iCnt = $this->database()
                    ->select('COUNT(DISTINCT u.user_id)')
                    ->from($this->_sTable, 'friend')
                    ->join(Phpfox::getT('user'), 'u', 'u.status_id = 0')
                    ->where($aCond)
                    ->execute('getSlaveField');
        }
        if ($iCnt)
        {
            if ($bAddDetails === true)
            {
                $this->database()->select('u.status, u.user_id, u.birthday, u.gender, u.country_iso AS location, ');
            }
            if ($bIsOnline === true)
            {
                $this->database()->select('ls.last_activity, ')->join(Phpfox::getT('log_session'), 'ls', 'ls.user_id = friend.friend_user_id AND ls.im_hide = 0');
            }
            if ($iUserId !== null)
            {
                $this->database()->innerJoin('(SELECT friend_user_id FROM ' . Phpfox::getT('friend') . ' WHERE is_page = 0 AND user_id = ' . $iUserId . ')', 'sf', 'sf.friend_user_id = friend.friend_user_id');
            }
            if ($bIsListView)
            {
                $this->database()->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.friend_user_id = friend.friend_user_id');
            }
            if ((int) $iListId > 0)
            {
                $this->database()->innerJoin(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = ' . (int) $iListId . ' AND fld.friend_user_id = friend.friend_user_id');
            }
            $aRows = $this->database()->select('und.*, und.user_id as temp_id, ' . Phpfox::getUserField())
                    ->from('(
					(SELECT d.user_id,  d.is_guest,d.quanlity, d.time_stamp, d.page_id, d.not_show_name, d.not_show_money, d.donation_id
						FROM ' . Phpfox::getT('donation_pages') . ' d WHERE d.page_id = ' . $iPageId . ')
					union
					(SELECT g.guest_name, g.is_guest, g.quanlity, g.time_stamp, g.page_id, g.not_show_name, g.not_show_money, g.donation_id 
						FROM ' . Phpfox::getT('donation_guest_donation') . ' g WHERE g.page_id = ' . $iPageId . ') 
					)', 'und')
                    ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = und.user_id ')
                    ->order('time_stamp DESC')
                    ->execute('getSlaveRows');
            if ($bIsOnline === true)
            {
                foreach ($aRows as $iKey => $aRow)
                {
                    if ($aRow['last_activity'] < (PHPFOX_TIME - (Phpfox::getParam('log.active_session') * 60)))
                    {
                        $iCnt--;
                        unset($aRows[$iKey]);
                    }
                }
            }
            if ($bAddDetails === true)
            {
                $oUser = Phpfox::getService('user');
                $oCoreCountry = Phpfox::getService('core.country');
                foreach ($aRows as $iKey => $aRow)
                {
                    $aBirthDay = $oUser->getAgeArray($aRow['birthday']);
                    $aRows[$iKey]['month'] = Phpfox::getLib('date')->getMonth($aBirthDay['month']);
                    $aRows[$iKey]['day'] = $aBirthDay['day'];
                    $aRows[$iKey]['year'] = $aBirthDay['year'];
                    $aRows[$iKey]['gender_phrase'] = $oUser->gender($aRow['gender']);
                    $aRows[$iKey]['birthday'] = $oUser->age($aRow['birthday']);
                    $aRows[$iKey]['location'] = $oCoreCountry->getCountry($aRow['location']);
                }
            }
            if ($bIncludeList)
            {
                foreach ($aRows as $iKey => $aRow)
                {
                    $aRows[$iKey]['lists'] = Phpfox::getService('friend.list')->getListForUser($aRow['friend_user_id']);
                }
            }
        }
        if ($bCount === false)
        {
            return $aRows;
        }
        return array($iCnt, $aRows);
    }

    /*
     * enable/disable donation on page
     */
    public function updateDonationOnPage($iPageId, $iActive)
    {
        Phpfox::getService('donation.cache')->removeAll('skey' . $iPageId);
        $iUserId = Phpfox::getUserId();
        if ($iActive == 1)
        { 
            //active donation on this page; remove item is donation_config table
            $this->database()->delete(Phpfox::getT('donation_config'), 'page_id = ' . (int) $iPageId . ' AND user_id = ' . (int) $iUserId);
        }
        else
        {
            //active = 0; insert page id of current user to table
            $aRow = $this->database()
                    ->select('user_id')
                    ->from(Phpfox::getT('donation_config'))
                    ->where('page_id=' . (int) $iPageId . ' AND user_id = ' . (int) $iUserId)
                    ->execute('getRow');
            if (empty($aRow))
            {
                $aInsert = array(
                    'page_id' => $iPageId,
                    'user_id' => $iUserId,
                    'time_stamp' => PHPFOX_TIME
                );
                $this->database()->insert(Phpfox::getT('donation_config'), $aInsert);
            }
        }
        return true;
    }

    /*
     * return
     * 1: ok
     * 0: email exists
     */
    public function updateEmail($iUserId, $iPageId, $sEmail)
    {
        $oDonation = Phpfox::getService('donation');
        $sEmail = Phpfox::getLib('parse.input')->clean($sEmail, 1024);
        $sUserPaypalEmail = $oDonation->getEmailPaypalByPageId($iPageId);
        $iUserPaypalUserId = (int) $oDonation->getUserIdPaypalByEmail($sEmail);
        //has no email in paypal table; check in user
        $aUser = $oDonation->getUserBy('email', $sEmail);
        if ($iUserPaypalUserId == 0)
        {
            ////email may be used
            // //email has been used by another user
            if (!empty($aUser) && $aUser['user_id'] != $iUserId)
                return 0;
            //update email to this user
            $aInsert = array(
                'user_id' => $iUserId,
                'page_id' => $iPageId,
                'email' => $sEmail
            );
            if ($sUserPaypalEmail == null)
            {
                $this->database()->insert(Phpfox::getT('donation_config'), $aInsert);
            }
            else
            {
                $this->database()->update(Phpfox::getT('donation_config'), array('email' => $sEmail), 'page_id = ' . (int) $iPageId);
            }
            return 1;
        }
        if ($iUserPaypalUserId > 0 && $iUserPaypalUserId != $iUserId)
        {
            //email has been used by another user
            return 0;
        }
        return 1;
    }

    public function getDonatedUser($iPageId)
    {
        return $this->database()
                        ->select('user_id')
                        ->from(Phpfox::getT('donation_pages'))
                        ->where('page_id=' . (int) $iPageId)
                        ->group('user_id')
                        ->execute('getRows');
    }

    /*
     * get user by field
     */
    public function getUserBy($sField, $sValue)
    {
        return $this->database()
                ->select('*')
                ->from(Phpfox::getT('user'))
                ->where($sField . ' = "' . $sValue . '" AND user_name != ""')
                ->execute('getRow');
    }

    public function getUserIdPaypalByEmail($sEmail)
    {
        $sEmail = trim($sEmail);
        return $this->database()
                ->select('user_id')
                ->from(Phpfox::getT('donation_config'))
                ->where('email = "' . $sEmail . '"')
                ->execute('getField');
    }

    public function getEmailPaypalByUserId($iUserId)
    {
        return $this->database()
                ->select('email')
                ->from(Phpfox::getT('donation_config'))
                ->where('user_id = ' . (int) $iUserId)
                ->execute('getField');
    }

    public function getEmailPaypalByPageId($iPageId)
    {
        return $this->database()
                ->select('email')
                ->from(Phpfox::getT('donation_config'))
                ->where('page_id = ' . (int) $iPageId)
                ->execute('getField');
    }

    public function getUserEmail($iUserId)
    {
        return $this->database()
                ->select('email')
                ->from(Phpfox::getT('user'))
                ->where('user_id = ' . (int) $iUserId)
                ->execute('getField');
    }

    public function getDonationOfPage($iPageId)
    {
        if ($iPageId == -1)
        {
            $aRow = $this->database()
                    ->select('*')
                    ->from(Phpfox::getT('donation_config'))
                    ->where('page_id=' . -1)
                    ->execute('getRow');
        }
        else
        {
            $iUserId = Phpfox::getService('donation')->getUserIdOfPage($iPageId);
            $aRow = $this->database()
                    ->select('*')
                    ->from(Phpfox::getT('donation_config'))
                    ->where('page_id=' . (int) $iPageId . ' AND user_id = ' . (int) $iUserId)
                    ->execute('getRow');
        }
        return $aRow;
    }

    public function getUserIdOfPage($iPageId)
    {
        if ($iPageId == -1)
        {
            return 1;
        }
        else
        {
            return (int) ($this->database()
                    ->select('user_id')
                    ->from(Phpfox::getT('pages'))
                    ->where('page_id=' . (int) $iPageId)
                    ->execute('getField'));
        }
    }

    public function isEnableDonation($iPageId)
    {
        $iUserId = Phpfox::getService('donation')->getUserIdOfPage($iPageId);
        $aRow = $this->database()
                ->select('is_active')
                ->from(Phpfox::getT('donation_config'))
                ->where('page_id=' . (int) $iPageId . ' AND user_id = ' . (int) $iUserId)
                ->execute('getField');
        if (empty($aRow))
            return false;
        if (isset($aRow['is_active']) && $aRow['is_active'] == 0)
        {
            return false;
        }
        return true;
    }

    public function getPageDetail($iPageId)
    {
        if ($iPageId == -1)
        {
            return _p('donation.page_donation_title_homepage');
        }
        else
        {
            $aRow =  $this->database()
                    ->select('p.title')
                    ->from(Phpfox::getT('pages'), 'p')
                    ->where('p.page_id = ' . (int) $iPageId)
                    ->execute('getField');
			//$aRow = mysqli_real_escape_string($aRow);
			return $aRow;
        }
		
    }

    /**
     * tai part
     */
    public function updateConfig($iUserId, $iPageId, $sEmail, $sContent, $sTermOfService, $sSubject, $sEmailContent, $bIsActive)
    {
        $oInput = Phpfox::getLib('parse.input');
        $sEmail = $oInput->clean($sEmail, 1024);
        $sContent = $oInput->clean($sContent);
        $sContent_parsed = $oInput->prepare($sContent);
        $sTermOfService = $oInput->clean($sTermOfService);
        $sTermOfService_parsed = $oInput->prepare($sTermOfService);
        $sSubject = $this->preParse()->clean($sSubject);
        $aConfig = $this->getDonationOfPage($iPageId);
        //has no email in paypal table; check in user
        if (empty($aConfig))
        {
            //this is the first time use this function
            //insert config into database
            $aInsert = array(
                'user_id' => $iUserId,
                'page_id' => $iPageId,
                'email' => $sEmail,
                'content' => $sContent,
                'content_parsed' => $sContent_parsed,
                'is_active' => $bIsActive,
                'term_of_service' => $sTermOfService,
                'term_of_service_parsed' => $sTermOfService_parsed,
                'time_stamp' => PHPFOX_TIME,
            );
            $iId = $this->database()->insert(Phpfox::getT('donation_config'), $aInsert);
            Phpfox::getService('donation.process')->setEmailOfDonation($iId, $sSubject, $sEmailContent);
            return $iId;
        }
        else
        {  //update information of config
            $aUpdate = array(
                'email' => $sEmail,
                'content' => $sContent,
                'content_parsed' => $sContent_parsed,
                'term_of_service' => $sTermOfService,
                'term_of_service_parsed' => $sTermOfService_parsed,
                'is_active' => $bIsActive,
            );
            $this->database()->update(Phpfox::getT('donation_config'), $aUpdate, 'page_id = ' . (int) $iPageId);
            Phpfox::getService('donation.process')->setEmailOfDonation($aConfig['donation_id'], $sSubject, $sEmailContent);
            return true;
        }
        return false;
    }

    /** 
     * get information from donation config
     */
    public function getDonationConfig($iPageId, $iUserId)
    {
        $aRow = array();
        if ($iPageId == -1)
        {
            if (($iPageId ) && $iUserId)
            {
                $aRow = $this->database()
                        ->select('dc.*, de.subject, de.content as email_content')
                        ->from(Phpfox::getT('donation_config'), 'dc')
                        ->leftjoin(phpfox::getT('donation_email'), 'de', 'de.donation_id = dc.donation_id')
                        ->where('page_id = -1')
                        ->execute('getRow');
            }
        }
        else
        {
            if (($iPageId ) && $iUserId)
            {
                $aRow = $this->database()
                        ->select('dc.*, de.subject, de.content as email_content')
                        ->from(Phpfox::getT('donation_config'), 'dc')
                        ->leftjoin(phpfox::getT('donation_email'), 'de', 'de.donation_id = dc.donation_id')
                        ->where('page_id=' . (int) $iPageId . ' AND user_id = ' . (int) $iUserId)
                        ->execute('getRow');
            }
        }
        return $aRow;
    }

    /**
     * get id from alias url
     */
    public function getPageId($sUrl)
    {
        $aPage = $this->database()
                ->select('*')
                ->from(Phpfox::getT('pages_url'))
                ->where('vanity_url = \'' . $this->database()->escape($sUrl) . '\'')
                ->execute('getSlaveRow');
        if (empty($aPage))
            return false;
        else
            return (int) $aPage['page_id'];
    }

    public function sendEmailToUser($iUserId, $iPageId)
    {
        $aEmail = $this->database()
                ->select('de.*, dc.email, dc.user_id')
                ->from(phpfox::getT('donation_email'), 'de')
                ->join(phpfox::getT('donation_config'), 'dc', 'dc.donation_id = de.donation_id')
                ->where('dc.page_id = ' . $iPageId)
                ->execute('getSlaveRow');
        if (!empty($aEmail))
        {
            if (!$aEmail['content'] && !$aEmail['subject'])
            {
                return false;
            }
            $aUser = $this->database()
                    ->select('u.email,' . phpfox::getUserField())
                    ->from(phpfox::getT('user'), 'u')
                    ->where('u.user_id = ' . $iUserId)
                    ->execute('getSlaveRow');
            $aOwner = $this->database()
                    ->select(phpfox::getUserField())
                    ->from(phpfox::getT('user'), 'u')
                    ->where('u.user_id = ' . $aEmail['user_id'])
                    ->execute('getSlaveRow');
            $aSearch = array('{FULL_NAME}', '{USER_NAME}', '{SITE_NAME}');
            $aReplace = array($aUser['full_name'], $aUser['user_name'], Phpfox::getParam('core.site_title'));
            $aEmail['subject'] = str_ireplace($aSearch, $aReplace, $aEmail['subject']);
            $aEmail['content'] = str_ireplace($aSearch, $aReplace, $aEmail['content']);
            $aEmail['content'] = Phpfox::getLib('parse.input')->prepare($aEmail['content'], true);
            $quote_style = ENT_COMPAT;
            $charset = 'UTF-8';
            $aEmail['subject'] = html_entity_decode($aEmail['subject'], $quote_style, $charset);
            Phpfox::getService('donation.mail')->to($aUser['email'])
                    ->subject($aEmail['subject'])
                    ->message($aEmail['content'])
                    ->fromName($aOwner['full_name'])
                    ->send();
            return true;
        }
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('donation.service_donation__call'))
        {
            eval($sPlugin);
            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>
