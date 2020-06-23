<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: process.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class Donation_Service_Process extends Phpfox_Service {

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('donation_pages');
    }

    public function updateCurrencies($sGateway = 'paypal')
    {
        $aVals = $this->request()->getArray('aVals');
        if (!array_key_exists('aCurrencies', $aVals)) {
            return false;
        }
        $aParam = $aVals['aCurrencies'];
        $aInsert = array(
            'data' => serialize($aParam),
            'time_stamp' => PHPFOX_TIME,
            'type_id' => 'paypal_currency'
        );
        $aRow = $this->database()
                ->select('setting_id')
                ->from(Phpfox::getT('donation_gateway_setting'))
                ->where('type_id = \'paypal_currency\'')
                ->execute('getSlaveRow');
        if (isset($aRow['setting_id']))
        {
            // this case we update setting
            $iId = $this->database()->update(Phpfox::getT('donation_gateway_setting'), $aInsert, ' type_id = \'paypal_currency\'');
        }
        else
        {
            // if setting doesn't exist we insert a new row
            $iId = $this->database()->insert(Phpfox::getT('donation_gateway_setting'), $aInsert);
        }

        return $iId;
    }

    public function createInvoice($aParam)
    {
        $aInsert = array(
            'invoice_content' => serialize($aParam),
            'time_stamp' => PHPFOX_TIME
        );
        return $this->database()->insert(Phpfox::getT('donation_invoice'), $aInsert);
    }

    public function updateDonationButton($bIsDefault)
    {
        if ($bIsDefault)
        {
            $this->database()->update(Phpfox::getT('donation_image'), array('is_default' => 1), ' type=\'admin_button\'');
        }
        else
        {
            $this->database()->update(Phpfox::getT('donation_image'), array('is_default' => 0), ' type=\'admin_button\'');
        }
        return $this->updateDonationButtonImage();
    }

    public function updateDonationButtonImage()
    {
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            $aSql = array();
            $aImage = Phpfox::getLib('file')->load('image', array(
                'jpg',
                'gif',
                'png'
                    ), (Phpfox::getParam('donation.max_size_for_donation_image_button') <= 0 ? null : (Phpfox::getParam('donation.max_size_for_donation_image_button') / 1024)));
            if ($aImage !== false)
            {
                $sDirPath = PHPFOX_DIR_FILE . 'pic/younetdonation/';
                $sFileName = 'donation_button%s';
                if (!is_dir($sDirPath))
                {
                    @mkdir($sDirPath, 0777);
                    @chmod($sDirPath, 0777);
                }
                $iFileSizes = 0;
                $oImage = Phpfox::getLib('image');
                $sNewFileName = Phpfox::getLib('file')->upload('image', $sDirPath, $sFileName, false, 0644, false);
                $iWidth = 130;
                $iHeight = 50;
                $oImage->createThumbnail($sDirPath . $sNewFileName, $sDirPath . sprintf($sNewFileName, '_' . 'medium'), $iWidth, $iHeight);
                @unlink($sDirPath . $sNewFileName);
                $aSql['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
                $aSql['image_path'] = 'file/pic/younetdonation/' . $sNewFileName;
                $aSql['time_stamp'] = PHPFOX_TIME;
                // Update donation image.
                $this->database()->update(Phpfox::getT('donation_image'), $aSql, ' type=\'admin_button\'');
            }
            else
            {
                return false;
            }
        }
        return true;
    }

    public function deleteUser($iPageId, $iUserId)
    {
        $iOwner = $this->database()
                ->select('dc.user_id')
                ->from(phpfox::getT('donation_config'), 'dc')
                ->where('dc.page_id = ' . $iPageId)
                ->execute('getSlaveField');
        if (($iOwner == phpfox::getUserId()) || ($iPageId == -1 && Phpfox::isAdmin()))
        {
            $aDonationIds = $this->database()
                    ->select('dp.donation_id')
                    ->from($this->_sTable, 'dp')
                    ->where('dp.user_id = ' . $iUserId . ' and dp.page_id = ' . $iPageId)
                    ->execute('getRows');
            $this->database()->delete($this->_sTable, 'page_id = ' . (int) $iPageId . ' AND user_id = ' . (int) $iUserId);
            if (!empty($aDonationIds))
            {
                foreach ($aDonationIds as $aId)
                {
                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('donation', (int) $aId['donation_id'], $iUserId) : null);
                    $this->database()->delete(phpfox::getT('pages_feed'), 'item_id = ' . (int) $aId['donation_id'] . ' AND user_id = ' . (int) $iUserId);
                }
            }
            Phpfox::getService('donation.cache')->removeAll('skey' . $iPageId);
        }
    }

    public function deleteDonation($iPageId, $iDonationId, $bIsGuest = 0)
    {
        $iOwner = $this->database()
                ->select('dc.user_id')
                ->from(phpfox::getT('donation_config'), 'dc')
                ->where('dc.page_id = ' . $iPageId)
                ->execute('getSlaveField');
        if (($iOwner == phpfox::getUserId()) || ($iPageId == -1 && Phpfox::isAdmin()))
        {
            if ($bIsGuest)
            {
                $this->database()->delete(Phpfox::getT('donation_guest_donation'), 'page_id = ' . (int) $iPageId . ' AND donation_id = ' . (int) $iDonationId);
                Phpfox::getService('donation.cache')->removeAll('skey' . $iPageId);
            }
            else
            {
                $this->database()->delete($this->_sTable, 'page_id = ' . (int) $iPageId . ' AND donation_id = ' . (int) $iDonationId);
                Phpfox::getService('donation.cache')->removeAll('skey' . $iPageId);
            }
        }
    }

    public function addToDonationLists($iUserId, $iPageId, $iQuanlity, $bNotShowName, $bNotShowFeed, $bNotShowMoney, $bIsGuest = false, $sCurrency = 'USD')
    {
        if ($bIsGuest)
        {
            $sGuestNameParsed = $this->preParse()->clean($iUserId, 255);
            $aInsert = array(
                'page_id' => $iPageId,
                'guest_name' => $sGuestNameParsed,
                'quanlity' => $iQuanlity,
                'purchased' => 1,
                'approve' => 1,
                'time_stamp' => PHPFOX_TIME,
                'module_id' => 'pages',
                'not_show_money' => $bNotShowMoney,
                'not_show_name' => $bNotShowName,
                'currency' => $sCurrency
            );
            $iId = $this->database()->insert(Phpfox::getT('donation_guest_donation'), $aInsert);
        }
        else
        {
            $aInsert = array(
                'page_id' => $iPageId,
                'user_id' => $iUserId,
                'quanlity' => $iQuanlity,
                'purchased' => 1,
                'approve' => 1,
                'time_stamp' => PHPFOX_TIME,
                'module_id' => 'pages',
                'not_show_money' => $bNotShowMoney,
                'not_show_name' => $bNotShowName,
                'currency' => $sCurrency
            );
            if (!($iId = $this->database()->insert(Phpfox::getT('donation_pages'), $aInsert)))
            {
                return false;
            }

            Phpfox::getService('donation')->sendEmailToUser($iUserId, $iPageId);

            $aCallback = $iPageId == -1 ?  null : [
                'module' => 'pages',
                'item_id' => $iPageId,
                'table_prefix' => 'pages_'
            ];

            if (Phpfox::isModule('feed') && !$bNotShowFeed)
            {
                    (Phpfox::isModule('feed') ? Phpfox::getService('donation.feedprocess')->callback($aCallback)->add('donation', $iId, 0, 0, ($iPageId == -1) ? null : $aInsert['page_id'], $aInsert['user_id']) : null);
                Phpfox::getService('user.activity')->update($iUserId, 'donation');
            }
        }
        Phpfox::getService('donation.cache')->removeAll('skey' . $iPageId);
    }

    public function setEmailOfDonation($iDonationId, $sSubject, $sContent)
    {
        if (empty($sSubject) || empty($sContent))
        {
            //return false;
        }
        $aEmail = $this->database()
                ->select('de.donation_id, de.subject')
                ->from(phpfox::getT('donation_email'), 'de')
                ->join(phpfox::getT('donation_config'), 'dc', 'dc.donation_id = de.donation_id')
                ->where('dc.donation_id = ' . $iDonationId)
                ->execute('getSlaveRows');
        if (!$aEmail)
        {
            $aInsert = array(
                'donation_id' => $iDonationId,
                'subject' => $sSubject,
                'content' => $sContent
            );
            return $this->database()->insert(phpfox::getT('donation_email'), $aInsert);
        }
        else
        {
            $aUpdate = array('subject' => $sSubject, 'content' => $sContent);
            return $this->database()->update(phpfox::getT('donation_email'), $aUpdate, 'donation_id = ' . $iDonationId);
        }
        return false;
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
        if ($sPlugin = Phpfox_Plugin::get('donation.service_donation__call'))
        {
            return eval($sPlugin);
        }

        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>