<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: callback.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class Donation_Service_Callback extends Phpfox_Service {

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('donation');
    }

    public function canShareItemOnFeed()
    {
    }

    public function paymentApiCallback($aParam)
    {
        if ($aParam['status'] == 'completed')
        {
            $iInvoiceId = $aParam['custom'];
            $aInvoice = Phpfox::getService('donation')->getInvoice($iInvoiceId);
            $iPageId = $aInvoice['page_id'];
            $iUserId = $aInvoice['user_id'];
            $bNotShowName = $aInvoice['not_show_name'];
            $bNotShowFeed = $aInvoice['not_show_feed'];
            $bNotShowMoney = $aInvoice['not_show_money'];
            $bIsGuest = $aInvoice['is_guest'];
            $fQuantity = (float) $aParam['total_paid'];
            $sCurrency = isset($aParam['currency']) ? $aParam['currency'] : $aInvoice['currency'];
            Phpfox::getService('donation.process')->addToDonationLists($iUserId, $iPageId, $fQuantity, $bNotShowName, $bNotShowFeed, $bNotShowMoney, $bIsGuest, $sCurrency);
            (($sPlugin = Phpfox_Plugin::get('donation.service_callback_payment__end')) ? eval($sPlugin) : false);
        }
    }

    public function getTotalItemCount($iUserId)
    {
        return array(
            'field' => 'total_donation',
            'total' => $this->database()
                    ->select('COUNT(*)')
                    ->from(Phpfox::getT('donation_pages'))
                    ->where('user_id = ' . (int) $iUserId)
                    ->execute('getSlaveField')
        );
    }

    public function getActivityPointField()
    {
        return array(
            _p('donation.donations') => 'activity_donation'
        );
    }

    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);
        return array(
            _p('donation.donations') => $aUser['activity_donation']
        );
    }

    public function getActivityFeed($aRow)
    {
        $oCurrency = Phpfox::getService('core.currency');
        $sDefaultCurrency = $oCurrency->getDefault();
        $sDefaultSymbol = $oCurrency->getSymbol($sDefaultCurrency);
        $iPageId = db()->select('page_id')->from(':donation_pages')->where(['donation_id' => $aRow['item_id']])->executeField();

        if ($iPageId == -1) {
            $aDonation = $this->database()
                ->select('*')
                ->from(phpfox::getT('donation_pages'))
                ->where('page_id = -1 AND donation_id = ' . $aRow['item_id'])
                ->execute('getSlaveRow');
            if (empty($aDonation)) {
                return false;
            }
            $aReturn = array(
                'feed_title' => '',
                'feed_info' => _p('donation.fullname_donated_to_our_website'),
                'feed_content' => (!$aDonation['not_show_money']) ? _p('donation.donated_show_money',
                    ['money' => $aDonation['quanlity'] . $sDefaultSymbol]) : _p('donation.donated_not_show_money'),
                'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                    'theme' => 'misc/comment.png',
                    'return_url' => true
                )),
                'time_stamp' => $aRow['time_stamp'],
                'enable_like' => false,
                'feed_link' => 'javascript:void(0);',
            );
        } else {
            $aPage = $this->database()
                ->select('p.page_id, p.title, dc.not_show_money, dc.quanlity')
                ->from(phpfox::getT('donation_pages'), 'dc')
                ->join(phpfox::getT('pages'), 'p', 'p.page_id = dc.page_id')
                ->where('dc.donation_id = ' . $aRow['item_id'])
                ->execute('getRow');
            if (empty($aPage)) {
                return false;
            }
            $sPageLink = Phpfox::getLib('url')->makeUrl('pages.' . $aPage['page_id']);
            $aReturn = array(
                'feed_title' => '',
                'feed_info' => _p('donation.fullname_donates_a_page',
                    array('link' => $sPageLink, 'page_name' => $aPage['title'])),
                'feed_content' => (!$aPage['not_show_money']) ? _p('donation.donated_show_money',
                    ['money' => $aPage['quanlity'] . $sDefaultSymbol]) : _p('donation.donated_not_show_money'),
                'feed_link' => $sPageLink,
                'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                    'theme' => 'misc/comment.png',
                    'return_url' => true
                )),
                'time_stamp' => $aRow['time_stamp'],
                'enable_like' => false
            );
        }

        return array_merge($aReturn, $aRow);
    }

    public function onDeleteUser($iUser)
    {
        $aDonationIds = $this->database()
                ->select('dp.donation_id')
                ->from(phpfox::getT('donation_pages'), 'dp')
                ->where('dp.user_id = ' . $iUser)
                ->execute('getRows');
        if (!empty($aDonationIds))
        {
            foreach ($aDonationIds as $aId)
            {
                $this->database()->delete(phpfox::getT('donation_pages'), 'donation_id = ' . $aId['donation_id']);
            }
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
        if ($sPlugin = Phpfox_Plugin::get('donation.service_callback__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>
