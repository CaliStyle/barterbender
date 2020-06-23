<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Auction extends Phpfox_Service {

    private $_aCallback = null;

    public function __construct()
    {

    }

    public function getGlobalSetting()
    {
        $aRow = $this->database()
            ->select('eags.*')
            ->from(Phpfox::getT('ecommerce_auction_global_setting'), 'eags')
            ->execute("getSlaveRow");

        if ($aRow)
        {
            $aRow['default_setting'] = (array) json_decode($aRow['default_setting']);
            $aRow['actual_setting'] = (array) json_decode($aRow['actual_setting']);
        }

        return $aRow;
    }

    public function isHaveChildCategory($iProductId, $iParentCategory)
    {
        $aChildCategory = $this->database()
            ->select('ec.*')
            ->from(Phpfox::getT('ecommerce_category'), 'ec')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.category_id = ec.category_id AND ecd.product_type = \'auction\'')
            ->where('ecd.product_id = ' . (int) $iProductId . ' AND ec.parent_id = ' . (int) $iParentCategory)
            ->execute('getSlaveRow');

        return $aChildCategory;
    }

    public function getQuickAuctionById($iProductId)
    {
        $aRow = $this->database()
            ->select('ep.*')
            ->from(Phpfox::getT("ecommerce_product"), 'ep')
            ->where('ep.product_id = ' . (int) $iProductId)
            ->execute("getSlaveRow");

        return $aRow;
    }

    public function getTodaysLiveAuctions($iLimit = 10)
    {
        $iBeginOfDay = strtotime("midnight", PHPFOX_TIME);

        $iEndOfDay = strtotime("tomorrow", $iBeginOfDay) - 1;

        $aConds = array(
            'AND ep.start_time >= ' . $iBeginOfDay,
            'AND ep.start_time <= ' . $iEndOfDay,
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.privacy = 0'
        );

        $iCnt = $this->database()
            ->select('COUNT(ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->where($aConds)
            ->order('RAND()')
            ->execute('getSlaveField');

        $aRows = array();

        if($iCnt){
            $aRows = $this->database()
                ->select('ep.*, ept.description_parsed AS description')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->where($aConds)
                ->limit($iLimit)
                ->execute('getRows');
        }


        return array($iCnt,$aRows);
    }

    public function getAuctionsEndingToday($iLimit = 10)
    {
        $iBeginOfDay = strtotime("midnight", PHPFOX_TIME);

        $iEndOfDay = strtotime("tomorrow", $iBeginOfDay) - 1;

        $aConds = array(
            'AND ep.end_time >= ' . $iBeginOfDay,
            'AND ep.end_time <= ' . $iEndOfDay,
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.privacy = 0'
        );

        $iCnt =  $this->database()
            ->select('COUNT(ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->where($aConds)
            ->execute('getSlaveField');

        $aRows = array();
        if($iCnt){
            $aRows = $this->database()
                ->select('ep.*, ept.description_parsed AS description')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->where($aConds)
                ->limit($iLimit)
                ->execute('getRows');
        }


        return array($iCnt,$aRows);
    }

    public function getUpcomingAuctions($iLimit = 10)
    {
        $aConds = array(
            'AND ep.start_time > ' . PHPFOX_TIME,
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "approved" || ep.product_status = "running" || ep.product_status = "bidden")',
            'AND ep.privacy = 0'
        );

        $iCnt = $this->database()
            ->select('COUNT(ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->where($aConds)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('ep.*, ept.description_parsed AS description')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->where($aConds)
                ->order('ep.product_id DESC')
                ->limit($iLimit)
                ->execute('getRows');
        }

        return array($iCnt, $aRows);
    }

    public function getFeaturedAuctions($iLimit = 10)
    {
        $aConds = array(
            'AND ep.end_time > ' . PHPFOX_TIME,
            'AND ep.feature_start_time <= ' . PHPFOX_TIME,
            'AND ep.feature_end_time >= ' . PHPFOX_TIME,
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.privacy = 0'
        );

        $aRows = $this->database()
            ->select('ep.*, ept.description_parsed AS description, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price,u.full_name, u.user_name')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->leftJoin(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->where($aConds)
            ->order('RAND()')
            ->limit($iLimit)
            ->execute('getRows');

        foreach ($aRows as $iKey => $aRow)
        {
            $aRows[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aRow['creating_item_currency']);
        }

        return $aRows;
    }

    public function getWeeklyHotAuctions($iLimit = 9)
    {
        $aConds = array(
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "bidden")',
            'AND ep.privacy = 0',
            'AND ep.end_time > ' . PHPFOX_TIME,
            'AND ep.start_time < ' . PHPFOX_TIME,
        );

        $aRows = $this->database()
            ->select('ep.*, ept.description_parsed AS description, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, u.full_name, u.user_name, ec.category_id, ec.title AS category_title')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->where($aConds)
            ->order('epa.auction_total_bid DESC')
            ->limit($iLimit)
            ->execute('getRows');

        foreach ($aRows as $key => $aProduct)
        {
            $aRows[$key] = $this->retrieveInfoFromAuction($aProduct);
        }


        return $aRows;
    }

    public function getNewAuctions($sCondition,$iLimit = 9,$iPage = 0)
    {
        $aConds = array(
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.privacy = 0'
        );

        $iCount = $this->database()
            ->select('COUNT(*) as count')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->where($aConds)
            ->execute('getSlaveField');

        $aRows = array();
        if($iCount){

            $aRows = $this->database()
                ->select('distinct ecd.product_id,ep.*, ept.description_parsed AS description, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, u.full_name, u.user_name, ec.category_id, ec.title AS category_title')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->where($aConds)
                ->limit($iPage,$iLimit,$iCount)
                ->order('ep.product_creation_datetime DESC')
                ->execute('getRows');

            foreach ($aRows as $key => $aProduct)
            {
                $aRows[$key] = $this->retrieveInfoFromAuction($aProduct);
            }

        }

        return array($iCount,$aRows);
    }

    public function retrieveInfoFromAuction($aProduct,$sService = false){

        if($aProduct['start_time'] > PHPFOX_TIME){

            $iSeconds = $aProduct['start_time'] - PHPFOX_TIME;
            if($iSeconds > 0){

                $iDays = floor($iSeconds / 86400);
                $iSeconds %= 86400;

                $iHours = floor($iSeconds / 3600);

                $aProduct['remaining_time'] = _p('start_in').' '.($iDays > 0 ? ('<span class=\'ynauction-info-left-time\'>'.$iDays . '</span> ' . ($iDays > 1 ? _p('days') : _p('day'))) : '') . ' ' .($iHours > 0 ? ('<span class=\'ynauction-info-left-time\'>'.$iHours . '</span> ' . ($iHours > 1 ? _p('hours') : _p('hour'))) : '');

                //$aProduct['remaining_time']  =  preg_replace('/(\d+)/mi', '<span class=\'ynauction-info-left-time\'>$1</span>', $aProduct['remaining_time']);

            }
            else{
                $aProduct['remaining_time'] = '';
            }

        }
        else{

            $iSeconds = $aProduct['end_time'] - PHPFOX_TIME;
            if($iSeconds > 0){

                $iDays = floor($iSeconds / 86400);
                $iSeconds %= 86400;

                $iHours = floor($iSeconds / 3600);

                if($sService == 'my-bids'){
                    $aProduct['remaining_time'] = ($iDays > 0 ? ('<span class=\'ynauction-info-left-time\'>'.$iDays . '</span> ' . ($iDays > 1 ? _p('days') : _p('day'))) : '') . ' ' . ($iHours > 0 ? ('<span class=\'ynauction-info-left-time\'>'.$iHours .'</span> ' . ($iHours > 1 ? _p('hours') : _p('hour'))) : '');
                }
                else{
                    $aProduct['remaining_time'] = ($iDays > 0 ? ('<span class=\'ynauction-info-left-time\'>'.$iDays .'</span> ' . ($iDays > 1 ? _p('days') : _p('day'))) : '') . ' ' . ($iHours > 0 ? ('<span class=\'ynauction-info-left-time\'>'.$iHours .'</span> ' . ($iHours > 1 ? _p('hours_left') : _p('hour_left'))) : '');
                }

                //$aProduct['remaining_time']  =  preg_replace('/(\d+)/mi', '<span class=\'ynauction-info-left-time\'>$1</span>', $aProduct['remaining_time']);

            }
            else{
                $aProduct['remaining_time'] = '';
            }

        }
        $aProduct['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aProduct['creating_item_currency']);

        $aProduct['link'] = Phpfox::getLib('url')->permalink('auction.detail', $aProduct['product_id'], $aProduct['name']);
        $aProduct['featured'] = ($aProduct['feature_start_time'] <= PHPFOX_TIME && $aProduct['feature_end_time'] >= PHPFOX_TIME && ($aProduct['product_status'] == 'bidden' || $aProduct['product_status'] == 'approved' || $aProduct['product_status'] == 'running'));

        return $aProduct;
    }
    public function getBiddenByMyFriendsAuctions($iLimit = 10)
    {
        $aConds = array(
            'AND ep.end_time > ' . PHPFOX_TIME,
            'AND ep.start_time <= ' . PHPFOX_TIME,
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "bidden" || ep.product_status = "running" || ep.product_status = "approved")',
            'AND ep.privacy = 0',
            'AND ep.user_id != '.(int)Phpfox::getUserId()
        );

        $sQuery = $this->database()
            ->select('COUNT(ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')

            ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'ep.product_id = eab.auctionbid_product_id')
            ->join(Phpfox::getT('user'), 'filter_u', 'filter_u.user_id = eab.auctionbid_user_id')
            ->join(Phpfox::getT('friend'), 'filter_f', 'filter_f.user_id = eab.auctionbid_user_id AND filter_f.friend_user_id = ' . Phpfox::getUserId())

            ->join(Phpfox::getT('user'), 'u2', 'u2.user_id = ep.user_id')

            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')

            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')

            ->where($aConds)
            ->group('eab.auctionbid_product_id')
            ->execute('');

        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');

        $aRows = array();

        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('ep.*, ept.description_parsed AS description, epa.auction_total_bid, epa.auction_item_reserve_price,epa.auction_latest_bid_price , u2.user_name, u2.full_name, GROUP_CONCAT(eab.auctionbid_user_id) as list_friend_id')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')

                ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'ep.product_id = eab.auctionbid_product_id')
                ->join(Phpfox::getT('user'), 'filter_u', 'filter_u.user_id = eab.auctionbid_user_id')
                ->join(Phpfox::getT('friend'), 'filter_f', 'filter_f.user_id = eab.auctionbid_user_id AND filter_f.friend_user_id = ' . Phpfox::getUserId())

                ->join(Phpfox::getT('user'), 'u2', 'u2.user_id = ep.user_id')

                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')

                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')

                ->where($aConds)
                ->group('eab.auctionbid_product_id')
                ->order('ep.product_creation_datetime DESC')
                ->limit($iLimit)
                ->execute('getRows');

            $aListFriendId = array();
            foreach ($aRows as $key => $aProduct)
            {
                $aRows[$key] = $this->retrieveInfoFromAuction($aProduct);
                $aRows[$key]['aBiddenFriendsUserId'] = array_unique(explode(',', $aProduct['list_friend_id']));
                $aListFriendId = array_merge($aRows[$key]['aBiddenFriendsUserId'], $aListFriendId);
                $usergroupId = db()->select('user_group_id')
                    ->from(Phpfox::getT('user'))
                    ->where('user_id =' .(int)$aRows[$key]['user_id'])
                    ->executeField();
                $aRows[$key]['usergroupId'] = $usergroupId;
            }


            $aListFriendId = array_unique($aListFriendId);

            $aListFriends = $this->getBiddenFriendsByListFriendId($aListFriendId);

            foreach ($aRows as $iKey => $aRow)
            {
                $aRows[$iKey]['aBiddenFriends'] = array();
                foreach($aRow['aBiddenFriendsUserId'] as $iUserId)
                {
                    if (isset($aListFriends[$iUserId]))
                    {
                        $aRows[$iKey]['aBiddenFriends'][] = $aListFriends[$iUserId];
                    }
                }
            }
        }
        return array($iCnt, $aRows);
    }

    public function getBiddenFriendsByListFriendId($aListFriendId)
    {
        $aRows = $this->database()
            ->select('u.*')
            ->from(Phpfox::getT('user'), 'u')
            ->where('u.user_id IN (' . implode(',', $aListFriendId) . ')')
            ->execute('getRows');

        $aResult = array();

        foreach ($aRows as $aUser)
        {
            $aResult[$aUser['user_id']] = $aUser;
        }

        return $aResult;
    }

    public function getMostLikedAuctions($iLimit = 10)
    {
        $aConds = array(
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.privacy = 0',
        );

        $aCondLikes = array(
            'AND ep.end_time > ' . PHPFOX_TIME,
            'AND ep.start_time <= ' . PHPFOX_TIME,
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.privacy = 0',
            'AND ep.total_like != 0',
        );

        $iCnt = $this->database()
            ->select('COUNT(ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->where($aConds)
            ->execute('getSlaveField');

        $iCntLikes = $this->database()
            ->select('COUNT(ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->where($aCondLikes)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCnt)
        {
            $this->database()
                ->select('ep.*, ept.description_parsed AS description, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, u.full_name, u.user_name, ec.category_id, ec.title AS category_title')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->where($aConds);
            if($iCntLikes ==0){
                $aRows =  $this->database()->order('RAND()')->limit($iLimit)->execute('getRows');
            }
            else{
                $aRows =  $this->database()->order('ep.total_like DESC')->limit($iLimit)->execute('getRows');
            }

            foreach ($aRows as $key => $aProduct)
            {
                $aRows[$key] = $this->retrieveInfoFromAuction($aProduct);
            }
        }

        return array($iCnt, $aRows);
    }

    public function getAuctionForEdit($iProductId, $bForce = false)
    {
        $aItem = $this->database()->select('ep.*, epa.*,ept.description,ept.shipping')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
            ->where('epa.product_id = ' . (int) $iProductId.' AND ep.product_creating_type = \'auction\'')
            ->execute('getSlaveRow');

        if ($aItem)
        {
            $aItem['start_time_month'] = Phpfox::getTime('n', $aItem['start_time'], false);
            $aItem['start_time_day'] = Phpfox::getTime('j', $aItem['start_time'], false);
            $aItem['start_time_year'] = Phpfox::getTime('Y', $aItem['start_time'], false);
            $aItem['start_time_hour'] = Phpfox::getTime('H', $aItem['start_time'], false);
            $aItem['start_time_minute'] = Phpfox::getTime('i', $aItem['start_time'], false);

            $aItem['end_time_month'] = Phpfox::getTime('n', $aItem['end_time'], false);
            $aItem['end_time_day'] = Phpfox::getTime('j', $aItem['end_time'], false);
            $aItem['end_time_year'] = Phpfox::getTime('Y', $aItem['end_time'], false);
            $aItem['end_time_hour'] = Phpfox::getTime('H', $aItem['end_time'], false);
            $aItem['end_time_minute'] = Phpfox::getTime('i', $aItem['end_time'], false);

            $aItem['categories'] = Phpfox::getService('ecommerce.category')->getCategoryIds($aItem['product_id']);
            $aItem['all_customfield_user']   = Phpfox::getService('ecommerce')->getAdditionInfo($aItem['product_id']);
        }

        return $aItem;
    }

    public function getQuickAuctionByAuctionId($iAuctionId){
        $aItem = $this->database()->select('ep.*,epa.*')
            ->from(Phpfox::getT('ecommerce_product_auction'), 'epa')
            ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = epa.product_id')
            ->where('epa.auction_id = ' . (int) $iAuctionId)
            ->execute('getSlaveRow');

        return $aItem;
    }

    public function getQuickAuctionByProductId($iProductId){
        $aItem = $this->database()->select('ep.*,epa.*')
            ->from(Phpfox::getT('ecommerce_product_auction'), 'epa')
            ->join(Phpfox::getT('ecommerce_product'), 'ep', 'ep.product_id = epa.product_id')
            ->where('epa.product_id = ' . (int) $iProductId)
            ->execute('getSlaveRow');

        return $aItem;
    }



    public function get($aConds, $sSort = 'ep.name ASC', $iPage = '', $iLimit = '')
    {
        $aConds[] = 'AND ep.product_status != "deleted" AND ep.module_id = \'auction\'';

        $iCnt = $this->database()
            ->select('COUNT(DISTINCT ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->where($aConds)
            ->order($sSort)
            ->execute('getSlaveField');

        $aItems = array();

        if ($iCnt)
        {
            $aItems = $this->database()
                ->select('ep.*, ept.description_parsed AS description, ec.category_id, ec.title AS category_title, u2.user_name AS latest_bidder_user_name, u2.full_name AS latest_bidder_full_name, u3.user_name AS won_bid_user_name, u3.full_name AS won_bid_full_name,epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')

                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = epa.auction_latest_bidder')
                ->leftJoin(Phpfox::getT('user'), 'u3', 'u3.user_id = epa.auction_won_bidder_user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->group('ep.product_id')
                ->execute('getSlaveRows');

            foreach ($aItems as $key => $aProduct)
            {
                $aItems[$key] = $this->retrieveInfoFromAuction($aProduct);
            }
        }

        return array($iCnt, $aItems);
    }

    public function getAuctionOfSeller($aConds, $sSort = 'ep.name ASC', $iPage = '', $iLimit = '')
    {
        $aConds[] = 'AND ep.product_status != "deleted"';
        $aConds[] = 'AND ep.module_id = "auction"';

        $iCnt = $this->database()
            ->select('COUNT(ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->where($aConds)
            ->order($sSort)
            ->execute('getSlaveField');

        $aItems = array();

        if ($iCnt)
        {
            $aItems = $this->database()
                ->select('ep.*, ept.description_parsed AS description, ec.title AS category_title, u2.user_name AS latest_bidder_user_name, u2.full_name AS latest_bidder_full_name, u3.user_name AS won_bid_user_name, u3.full_name AS won_bid_full_name, epa.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = epa.auction_latest_bidder')
                ->leftJoin(Phpfox::getT('user'), 'u3', 'u3.user_id = epa.auction_won_bidder_user_id')
                ->where($aConds)
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aItems as $iKey => $aItem)
            {
                $aItems[$iKey]['featured'] = ($aItem['feature_start_time'] <= PHPFOX_TIME && $aItem['feature_end_time'] >= PHPFOX_TIME && ($aItem['product_status'] == 'approved' || $aItem['product_status'] == 'running' || $aItem['product_status'] == 'bidden'));
                $aItems[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aItem['creating_item_currency']);
            }
        }

        return array($iCnt, $aItems);
    }

    public function callback($aCallback)
    {
        $this->_aCallback = $aCallback;

        return $this;
    }

    public function getAuctionById($iProductId)
    {
        if (!$iProductId)
        {
            return false;
        }

        //check auction have category
        $bAuctionHasCategory = $this->database()
            ->select('ecd.category_id')
            ->from(Phpfox::getT('ecommerce_category_data'), 'ecd')
            ->where('ecd.product_id = '.(int)$iProductId)
            ->execute('getRow');

        if (Phpfox::isModule('like'))
        {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'auction\' AND lik.item_id = ep.product_id AND lik.user_id = ' . Phpfox::getUserId());
        }

        if (Phpfox::isModule('friend'))
        {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = ep.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }
        else
        {
            $this->database()->select('0 as is_friend, ');
        }


        if (!empty($bAuctionHasCategory['category_id'])) {
            $aRow = $this->database()
                ->select('ep.*, ' . (Phpfox::getParam('core.allow_html') ? 'ept.description_parsed' : 'ept.description') . ' AS description, ' . (Phpfox::getParam('core.allow_html') ? 'ept.shipping_parsed' : 'ept.shipping') . ' AS shipping, '. Phpfox::getUserField() . ', er.review_id as has_rated, u.country_iso, uf.city_location, uf.country_child_id, uf.total_auction, epa.*, ec.category_id, ec.title AS category_title')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->join(Phpfox::getT('user_field'), 'uf', 'u.user_id = uf.user_id')
                ->leftjoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->leftjoin(Phpfox::getT('ecommerce_review'), 'er', 'er.product_id = ep.product_id')
                ->leftjoin(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')

                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')

                ->where('ep.product_id = ' . (int) $iProductId)
                ->execute('getRow');
        }else {
            $aRow = $this->database()
                ->select('ep.*, ' . (Phpfox::getParam('core.allow_html') ? 'ept.description_parsed' : 'ept.description') . ' AS description, ' . (Phpfox::getParam('core.allow_html') ? 'ept.shipping_parsed' : 'ept.shipping') . ' AS shipping, '. Phpfox::getUserField() . ', er.review_id as has_rated, u.country_iso, uf.city_location, uf.country_child_id, uf.total_auction, epa.*')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->join(Phpfox::getT('user_field'), 'uf', 'u.user_id = uf.user_id')
                ->leftjoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->leftjoin(Phpfox::getT('ecommerce_review'), 'er', 'er.product_id = ep.product_id')
                ->leftjoin(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->where('ep.product_id = ' . (int) $iProductId)
                ->execute('getRow');
        }

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $aRow['location'] = Phpfox::getPhraseT(Phpfox::getService('core.country')->getCountry($aRow['country_iso']), 'country');
        if (isset($aRow['country_child_id']) && $aRow['country_child_id'] > 0)
        {
            $aRow['location_child'] = Phpfox::getService('core.country')->getChild($aRow['country_child_id']);
        }

        $aRow['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aRow['creating_item_currency']);

        return $aRow;
    }

    public function getOtherAuctionsFromThisSeller($iAuctionId,$iSellerId)
    {
        $aConds = array(
            'AND ep.start_time <= ' . PHPFOX_TIME . ' AND ep.end_time >= ' . PHPFOX_TIME,
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.privacy = 0',
            'AND ep.product_id != ' . (int) $iAuctionId,
            'AND ep.user_id = ' . (int)$iSellerId,
        );

        $aAuctions = $this->database()
            ->select(Phpfox::getUserField() . ', ep.*,epa.auction_latest_bid_price,epa.auction_item_reserve_price, ec.title as category_title')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->where($aConds)
            ->limit(Phpfox::getParam('auction.max_items_block_other_auctions_from_this_seller'))
            ->execute('getSlaveRows');

        foreach ($aAuctions as $key => $aProduct)
        {
            $aAuctions[$key] = $this->retrieveInfoFromAuction($aProduct);
        }
        return $aAuctions;
    }

    public function getCountReviewOfAuction($iAuctionId)
    {
        $iCount = $this->database()
            ->select('COUNT(er.review_id)')
            ->from(Phpfox::getT('ecommerce_review'), 'er')
            ->where('er.product_id = ' . (int) $iAuctionId)
            ->execute('getSlaveField');

        return $iCount;
    }

    public function getAuctionMainCategory($iAuctionId)
    {
        $aAuctionMainCategory = $this->database()
            ->select('ecd.category_id')
            ->from(Phpfox::getT('ecommerce_category_data'), 'ecd')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->where('ecd.product_id = ' . (int) $iAuctionId)
            ->execute('getRow');

        return $aAuctionMainCategory;
    }

    public function getCustomFieldByCategoryId($iCategoryId)
    {
        $aFields = $this->database()
            ->select('ecf.*')
            ->from(Phpfox::getT('ecommerce_category_customgroup_data'), 'eccd')
            ->join(Phpfox::getT('ecommerce_custom_group'), 'ecg', 'ecg.group_id = eccd.group_id AND ecg.is_active = 1')
            ->join(Phpfox::getT('ecommerce_custom_field'), 'ecf', 'ecf.group_id = ecg.group_id')
            ->where('eccd.category_id = ' . (int) $iCategoryId)
            ->order('ecg.group_id ASC, ecf.ordering ASC, ecf.field_id ASC')
            ->execute('getSlaveRows');

        $aHasOption = Phpfox::getService('ecommerce.custom')->getHasOption();

        if (is_array($aFields) && count($aFields))
        {
            foreach ($aFields as $k => $aField)
            {
                if (in_array($aField['var_type'], $aHasOption))
                {
                    $aOptions = $this->database()
                        ->select('*')
                        ->from(Phpfox::getT('ecommerce_custom_option'))
                        ->where('field_id = ' . $aField['field_id'])
                        ->order('option_id ASC')
                        ->execute('getSlaveRows');

                    if (is_array($aOptions) && count($aOptions))
                    {
                        foreach ($aOptions as $k2 => $aOption)
                        {
                            $aFields[$k]['option'][$aOption['option_id']] = $aOption['phrase_var_name'];
                        }
                    }
                }
            }
        }

        return $aFields;
    }

    public function getAuctionAdditionInfo($iAuctionId)
    {
        $aAuctionInfo = $this->database()
            ->select('epu.usercustomfield_title, epu.usercustomfield_content')
            ->from(Phpfox::getT('ecommerce_product_usercustomfield'), 'epu')
            ->where('epu.product_id = ' . (int) $iAuctionId)
            ->execute('getRows');

        return $aAuctionInfo;
    }

    public function getNumberOfItemInAuction($iAuctionId, $sType)
    {
        $iCount = 0;
        $aConds = array(' 1=1 ');
        $aExtra = array();
        $getData = false;

        switch ($sType) {
            case 'photos':
                if (Phpfox::getService('auction.helper')->isPhoto())
                {
                    $iCount = $this->getPhotoByAuctionId($iAuctionId, $aConds, $aExtra, $getData);
                }
                break;
            case 'videos':
                if (Phpfox::getService('auction.helper')->isVideo())
                {
                    $iCount = $this->getVideoByAuctionId($iAuctionId, $aConds, $aExtra, $getData);
                }
                break;
        }

        return $iCount;
    }

    public function getPhotoByAuctionId($iAuctionId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $sTable = Phpfox::getT('photo');
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()
            ->select('COUNT(*)')
            ->from($sTable, 'photo')
            ->where('photo.view_id = 0 AND photo.module_id = \'auction\' AND photo.group_id = ' . (int) $iAuctionId . ' AND photo.privacy IN(0)' . ' AND ' . $sCond)
            ->execute('getSlaveField');

        if ($getData)
        {
            if ($iCount)
            {
                if ($aExtra && isset($aExtra['limit']))
                {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order']))
                {
                    $this->database()->order($aExtra['order']);
                }

                if (Phpfox::isModule('like'))
                {
                    $this->database()->select('l.like_id AS is_liked, ')
                        ->leftJoin(Phpfox::getT('like'), 'l', ' (l.type_id = "photo" AND l.item_id = photo.photo_id AND l.user_id = ' . Phpfox::getUserId() . ') ');
                    $this->database()->select('adisliked.action_id as is_disliked, ')
                        ->leftJoin(Phpfox::getT('action'), 'adisliked', ' (adisliked.action_type_id = 2 AND adisliked.item_id = photo.photo_id AND adisliked.user_id = ' . Phpfox::getUserId() . ') ');
                }

                $aRows = $this->database()
                    ->select("pa.name AS album_name, pa.profile_id AS album_profile_id, ppc.name as category_name, ppc.category_id, photo.*, " . Phpfox::getUserField())
                    ->from($sTable, 'photo')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = photo.user_id')
                    ->leftJoin(Phpfox::getT('photo_album'), 'pa', ' (pa.album_id = photo.album_id) ')
                    ->leftJoin(Phpfox::getT('photo_category_data'), 'ppcd', ' (ppcd.photo_id = photo.photo_id) ')
                    ->leftJoin(Phpfox::getT('photo_category'), 'ppc', ' (ppc.category_id = ppcd.category_id) ')
                    ->where('photo.view_id = 0 AND photo.module_id = \'auction\' AND photo.group_id = ' . (int) $iAuctionId . ' AND photo.privacy IN(0) AND ' . $sCond)
                    ->group('photo.photo_id')
                    ->execute('getSlaveRows');
            }
        }
        else
        {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getVideoByAuctionId($iAuctionId, $aConds = array(), $aExtra = array(), $getData = true)
    {
        $sTable = Phpfox::getT('video');
        if (Phpfox::getService('auction.helper')->isAdvVideo())
        {
            $sTable = Phpfox::getT('channel_video');
        }
        $aRows = array();
        $sCond = implode(' AND ', $aConds);

        $iCount = $this->database()->select('COUNT(*)')
            ->from($sTable, 'm')
            ->where('m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'auction\' AND m.item_id = ' . (int) $iAuctionId . ' AND m.privacy IN(0)' . ' AND ' . $sCond)
            ->execute('getSlaveField');

        if ($getData)
        {
            if ($iCount)
            {
                if ($aExtra && isset($aExtra['limit']))
                {
                    $this->database()->limit($aExtra['page'], $aExtra['limit']);
                }

                if ($aExtra && isset($aExtra['order']))
                {
                    $this->database()->order($aExtra['order']);
                }

                $aRows = $this->database()->select("m.*, " . Phpfox::getUserField())
                    ->from($sTable, 'm')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                    ->where('m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'auction\' AND m.privacy IN(0) AND m.item_id = ' . (int) $iAuctionId . ' AND ' . $sCond)
                    ->execute('getSlaveRows');
            }
        }
        else
        {
            return $iCount;
        }

        return array($aRows, $iCount);
    }

    public function getDefaultModulesInAuction(){
        $list = array(
            '1' => 'overview',
            '2' => 'shipping',
            '3' => 'bidhistory',
            '4' => 'offerhistory',
            '5' => 'chart',
            '6' => 'activities',
            '7' => 'photos',
            '8' => 'videos'
        );
        $listIgnore = array();

        return array($list, $listIgnore);
    }

    public function getPageModuleForManage($iAuctionId, $aAuction = null)
    {
        if (null == $aAuction)
        {
            $aAuction = $this->getAuctionForEdit($iAuctionId, true);
        }
        if (isset($aAuction['product_id']) == false)
        {
            return array();
        }

        list($list, $listIgnore) = $this->getDefaultModulesInAuction();

        return array($list, '');
    }

    public function getBuyersAlsoViewedAuctions($iLimit = 5)
    {
        $sViewedAuctionId = Phpfox::getCookie('ynauction_viewed_auction_id');

        if (!$sViewedAuctionId || empty($sViewedAuctionId))
        {
            return array(0, array());
        }

        $aConds = array(
            'AND ep.product_creating_type = "auction"',
            'AND ep.module_id = "auction"',
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.privacy = 0',
            'AND ep.product_id IN (' . $sViewedAuctionId . ')'
        );

        $iCnt = $this->database()
            ->select('COUNT(ep.product_id)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->where($aConds)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('ep.*, ept.description_parsed AS description, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price,u.full_name, u.user_name')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->where($aConds)
                ->order('ep.product_id DESC')
                ->limit($iLimit)
                ->execute('getRows');

            foreach ($aRows as $key => $aProduct)
            {
                $aRows[$key] = $this->retrieveInfoFromAuction($aProduct);
            }
        }

        return array($iCnt, $aRows);
    }

    public function getAuctionsYouMayLike($iProductId)
    {
        $sAuctionId = Phpfox::getCookie('ynauction_auctions_you_may_like');

        if (!$sAuctionId || empty($sAuctionId))
        {
            return array();
        }

        $aAuctionId = explode(',', $sAuctionId);

        $aConds = array(
            'AND (ep.product_status = "running" || ep.product_status = "approved" || ep.product_status = "bidden")',
            'AND ep.product_id IN (' . $sAuctionId . ')',
            'AND ep.product_id != ' . $iProductId . '',
        );

        $aRows = $this->database()
            ->select('ep.*, ept.description_parsed AS description, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, u.full_name, u.user_name')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->where($aConds)
            ->order('ep.product_id DESC')
            ->execute('getRows');

        foreach ($aRows as $key => $aProduct)
        {
            $aRows[$key] = $this->retrieveInfoFromAuction($aProduct);
        }
        return $aRows;
    }

    public function getImages($iAuctionId)
    {
        $aConds = array('AND epi.product_id = ' . (int) $iAuctionId);

        $aRows = $this->database()
            ->select('epi.*')
            ->from(Phpfox::getT('ecommerce_product_image'), 'epi')
            ->where($aConds)
            ->order('epi.title ASC')
            ->execute('getRows');

        return $aRows;
    }

    public function getOfferListOfAuction($iProductId, $aSort, $iPage = '', $iLimit = ''){

        $aConds[] = 'AND ep.product_status != "deleted"';
        $aConds[] = 'AND eao.auctionoffer_product_id ='.(int)$iProductId;

        $iCnt = $this->database()
            ->select('COUNT(eao.auctionoffer_id)')
            ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
            ->join(Phpfox::getT('ecommerce_product'),'ep','ep.product_id = eao.auctionoffer_product_id')
            ->join(Phpfox::getT('user'), 'u','u.user_id = eao.auctionoffer_user_id')
            ->where($aConds)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('eao.*,'.Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
                ->join(Phpfox::getT('ecommerce_product'),'ep','ep.product_id = eao.auctionoffer_product_id')
                ->join(Phpfox::getT('user'), 'u','u.user_id = eao.auctionoffer_user_id')
                ->where($aConds)
                ->limit($iPage, $iLimit, $iCnt)
                ->order($aSort)
                ->execute('getRows');
            foreach ($aRows as $iKey => $aItem)
            {
                $aRows[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aItem['auctionoffer_currency']);
                $aRows[$iKey]['auctionoffer_creation_datetime'] = Phpfox::getTime('F-d-Y h:m:i',$aRows[$iKey]['auctionoffer_creation_datetime']);

                switch ($aRows[$iKey]['auctionoffer_status']) {
                    case '0'://pending
                        $aRows[$iKey]['auctionoffer_status_text'] = _p('pending');
                        break;
                    case '1'://approved
                        $aRows[$iKey]['auctionoffer_status_text'] = _p('approved');
                        break;
                    case '2'://deny
                        $aRows[$iKey]['auctionoffer_status_text'] = _p('denied');
                        break;
                    case '3'://expired
                        $aRows[$iKey]['auctionoffer_status_text'] = _p('expired');
                        break;
                    case '4'://success
                        $aRows[$iKey]['auctionoffer_status_text'] = _p('successful');
                        break;
                    default:
                        $aRows[$iKey]['auctionoffer_status_text'] = _p('expired');
                        break;
                }

                /*if offer is approved,caculate day left*/
                if($aRows[$iKey]['auctionoffer_status'] == '1'){
                    $aAuction = Phpfox::getService('auction')->getAuctionById($iProductId);
                    $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->getSellerSettings(array($aAuction['user_id']));
                    $day_left = isset($aSellerSettings[$aAuction['user_id']]['time_complete_transaction'])?$aSellerSettings[$aAuction['user_id']]['time_complete_transaction']:7;//day #note#
                    $day_spend = round((PHPFOX_TIME -$aRows[$iKey]['auctionoffer_approved_datetime'])/(60*60*24));
                    $aRows[$iKey]['auctionoffer_day_left'] = ($day_left > $day_spend ) ? ($day_left - $day_spend) : _p('expired') ;
                }
                else{
                    $aRows[$iKey]['auctionoffer_day_left'] =    _p('none');
                }
            }

        }
        return array($iCnt, $aRows);

    }

    public function getBidHistoryOfAuction($iProductId,$aSort,$iPage = '', $iLimit = ''){

        $aConds[] = 'AND ep.product_status != "deleted"';
        $aConds[] = 'AND eab.auctionbid_product_id ='.(int)$iProductId;

        $iCnt = $this->database()
            ->select('COUNT(eab.auctionbid_id)')
            ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
            ->join(Phpfox::getT('ecommerce_product'),'ep','ep.product_id = eab.auctionbid_product_id')
            ->join(Phpfox::getT('user'), 'u','u.user_id = eab.auctionbid_user_id')
            ->where($aConds)
            ->execute('getSlaveField');

        $aRows = array();

        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('eab.*,'.Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                ->join(Phpfox::getT('ecommerce_product'),'ep','ep.product_id = eab.auctionbid_product_id')
                ->join(Phpfox::getT('user'), 'u','u.user_id = eab.auctionbid_user_id')
                ->where($aConds)
                ->limit($iPage, $iLimit, $iCnt)
                ->order($aSort)
                ->execute('getRows');

            foreach ($aRows as $iKey => $aItem)
            {
                $aRows[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aItem['auctionbid_currency']);
                $aRows[$iKey]['auctionbid_creation_datetime'] = Phpfox::getTime('F-d-Y h:m:i',$aRows[$iKey]['auctionbid_creation_datetime']);
            }

        }
        return array($iCnt, $aRows);

    }

    public function getCountBidderOfAuction($iProductId){

        $aConds[] = 'AND ep.product_status != "deleted"';
        $aConds[] = 'AND eab.auctionbid_product_id ='.(int)$iProductId;

        $iCntBidder = $this->database()
            ->select('COUNT(DISTINCT eab.auctionbid_user_id)')
            ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
            ->join(Phpfox::getT('ecommerce_product'),'ep','ep.product_id = eab.auctionbid_product_id')
            ->join(Phpfox::getT('user'), 'u','u.user_id = eab.auctionbid_user_id')
            ->where($aConds)
            ->execute('getSlaveField');

        return $iCntBidder;
    }

    public function getChartData($iProductId,$extraData = array(),$iFrontEnd= false){

        //determine start end
        $start_time = 0;
        $end_time = 0;
        $period = array();

        $js_end__datepicker = $extraData['js_end__datepicker'];
        $js_end__datepicker = explode("/", $js_end__datepicker);
        $js_start__datepicker = $extraData['js_start__datepicker'];
        $js_start__datepicker = explode("/", $js_start__datepicker);

        $formatDatePicker = str_split(Phpfox::getParam('core.date_field_order'));
        $aFormatIntial = array();
        foreach ($formatDatePicker as $key => $value) {

            if($formatDatePicker[$key] != 'Y'){
                $formatIntial = strtolower($formatDatePicker[$key]);
            }
            else{
                $formatIntial = $formatDatePicker[$key];
            }

            $aFormatIntial[] = $formatIntial;
        }

        if(!empty($aFormatIntial)){
            foreach ($aFormatIntial as $key => $aItem) {
                $js_start__datepicker[$aItem] = $js_start__datepicker[$key];
                $js_end__datepicker[$aItem] = $js_end__datepicker[$key];
            }
        }

        $period['start'] = mktime(0, 0,0, $js_start__datepicker['m'], $js_start__datepicker['d'], $js_start__datepicker['Y']);
        $period['end'] = mktime(23, 59, 59, $js_end__datepicker['m'], $js_end__datepicker['d'], $js_end__datepicker['Y']);


        $start_time = $period['start'];
        $end_time = $period['end'];

        $aConds[] = 'AND ep.product_status != "deleted"';
        $aConds[] = 'AND eab.auctionbid_product_id ='.(int)$iProductId;
        $aConds[] = 'AND eab.auctionbid_creation_datetime  > '.$period['start'] .' AND eab.auctionbid_creation_datetime  < '.$period['end'];

        $data = $this->database()
            ->select('eab.*,'.Phpfox::getUserField())
            ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
            ->join(Phpfox::getT('ecommerce_product'),'ep','ep.product_id = eab.auctionbid_product_id')
            ->join(Phpfox::getT('user'), 'u','u.user_id = eab.auctionbid_user_id')
            ->where($aConds)
            ->order('eab.auctionbid_creation_datetime ASC')
            ->execute('getRows');


        $start_time = $period['start'];
        $end_time = $period['end'];

        $aAuction = $this->getQuickAuctionByProductId($iProductId);
        $aChartData = array();
        if(count($data)){
            foreach ($data as $key => $item) {
                $key_chart =  Phpfox::getTime('F-d-Y h:m:i',$item['auctionbid_creation_datetime']).'<br>'.$item['full_name'];
                $label     =  Phpfox::getTime('F-d-Y h:m:i',$item['auctionbid_creation_datetime']).'<br>'.$item['full_name'];


                $aChartData[$key_chart] = array($item['auctionbid_price'],$label);
            }
        }

        if($iFrontEnd && $aAuction['is_hide_reserve_price']){

        }
        else{
            $aChartData = array( _p('start_price') => array($aAuction['auction_item_reserve_price'],_p('start_price')) ) + $aChartData;
        }

        return $aChartData;

    }

    public function getTotalLikes()
    {
        return $this->database()
            ->select('SUM(ep.total_like)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->where('ep.user_id = ' . Phpfox::getUserId() . ' AND ep.product_status != "deleted"'.' AND ep.product_creating_type = \'auction\'')
            ->execute('getSlaveField');
    }

    public function getTotalViews()
    {
        return $this->database()
            ->select('SUM(ep.total_view)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->where('ep.user_id = ' . Phpfox::getUserId() . ' AND ep.product_status != "deleted"'.' AND ep.product_creating_type = \'auction\'')
            ->execute('getSlaveField');
    }

    public function getTotalDidntWinBid()
    {
        $sQuery = $this->database()
            ->select('eab.auctionbid_product_id')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', '(ep.product_status = "completed") && epa.product_id = ep.product_id && epa.auction_won_bidder_user_id IS NOT NULL && epa.auction_won_bidder_user_id != ' . Phpfox::getUserId())
            ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_user_id = ' . Phpfox::getUserId() . ' && ep.product_id = eab.auctionbid_product_id')
            ->group('eab.auctionbid_product_id')
            ->execute('');
        $iTotal = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');

        return $iTotal;
    }

    public function getTotalMyWonBid()
    {
        $aConds = array(
            'AND ep.end_time < '.PHPFOX_TIME,
            'AND ep.product_status = "completed"',
            'AND epa.auction_won_bidder_user_id = '.(int)Phpfox::getUserId(),
            'AND ep.end_time < '.PHPFOX_TIME,
        );

        $aConds[] = 'AND ep.product_status = "completed" and (eap.cartproduct_payment_status is NULL) and eab.auctionbid_status = 0';

        $sQuery = $this->database()
            ->select('ep.product_id')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_product_id = ep.product_id AND eab.auctionbid_user_id = ' . Phpfox::getUserId())

            ->leftJoin(Phpfox::getT('ecommerce_cart_product'), 'eap', 'eap.cartproduct_product_id = ep.product_id AND eap.cartproduct_type = "bid"')
            ->leftJoin(Phpfox::getT('ecommerce_cart'), 'cart', 'cart.cart_id = eap.cartproduct_cart_id AND cart.cart_user_id = ' . Phpfox::getUserId())

            ->where($aConds)
            ->group('eab.auctionbid_product_id')
            ->execute('');


        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');

        return $iCnt;
    }

    public function getMyBidsAuctions($aConds, $sSort = 'ep.name ASC', $iPage = '', $iLimit = '')
    {
        $aConds[] = 'AND ep.product_status != "deleted"';

        $sQuery = $this->database()
            ->select('ep.product_id')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_product_id = ep.product_id AND eab.auctionbid_user_id = ' . Phpfox::getUserId())
            ->where($aConds)
            ->group('eab.auctionbid_product_id')
            ->execute('');

        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');

        $aItems = array();

        if ($iCnt)
        {
            $aItems = $this->database()
                ->select('ep.*, ept.description_parsed AS description, ec.category_id, ec.title AS category_title, u2.user_name AS latest_bidder_user_name, u2.full_name AS latest_bidder_full_name, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, epa.auction_latest_bidder, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = epa.auction_latest_bidder')
                ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_product_id = ep.product_id AND eab.auctionbid_user_id = ' . Phpfox::getUserId())
                ->where($aConds)
                ->group('eab.auctionbid_product_id')
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aItems as $key => $aProduct)
            {
                $aItems[$key] = $this->retrieveInfoFromAuction($aProduct,'my-bids');
            }


        }

        return array($iCnt, $aItems);
    }

    public function getProductOfferInMyCart(){


        $aProductIdInCartQuery = $this->database()
            ->select('eap.cartproduct_product_id')
            ->from(Phpfox::getT('ecommerce_cart'), 'ec')
            ->join(Phpfox::getT('ecommerce_cart_product'), 'eap', 'ec.cart_id = eap.cartproduct_cart_id')
            ->where('ec.cart_user_id = ' . Phpfox::getUserId().' AND eap.cartproduct_type = "offer" AND eap.cartproduct_payment_status = "init"')
            ->execute('getSlaveRows');

        $aProductIdInCart = array();
        $sProductIdInCart = '';
        if(count($aProductIdInCartQuery)){
            foreach ($aProductIdInCartQuery as $key => $aProduct) {
                $aProductIdInCart[] =  $aProduct['cartproduct_product_id'];
            }
            $sProductIdInCart = implode(",", $aProductIdInCart);
        }

        return $sProductIdInCart;
    }
    public function getMyOffersAuctions($aConds, $sSort = 'ep.name ASC', $iPage = '', $iLimit = '')
    {

        $sProductIdInCart = $this->getProductOfferInMyCart();
        $aConds[] = 'AND ep.product_status != "deleted"';
        if($sProductIdInCart != ''){
            $aConds[] = "AND eao.auctionoffer_product_id NOT IN (".$sProductIdInCart.")";
        }
        $sQuery = $this->database()
            ->select('ep.product_id')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->join(Phpfox::getT('ecommerce_auction_offer'), 'eao', 'eao.auctionoffer_product_id = ep.product_id AND eao.auctionoffer_user_id = ' . Phpfox::getUserId().' AND eao.auctionoffer_status != 3 AND eao.auctionoffer_status != 4')
            ->where($aConds)
            ->group('eao.auctionoffer_product_id')
            ->execute('');


        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');

        $aItems = array();

        if ($iCnt)
        {
            $aItems = $this->database()
                ->select('ep.*, ept.description_parsed AS description, ec.category_id, ec.title AS category_title, u2.user_name AS latest_bidder_user_name, u2.full_name AS latest_bidder_full_name, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = epa.auction_latest_bidder')
                ->join(Phpfox::getT('ecommerce_auction_offer'), 'eao', 'eao.auctionoffer_product_id = ep.product_id AND eao.auctionoffer_user_id = ' . Phpfox::getUserId().' AND eao.auctionoffer_status != 3 AND eao.auctionoffer_status != 4')
                ->where($aConds)
                ->group('eao.auctionoffer_product_id')
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getRows');


            if(count($aItems)){
                foreach ($aItems as $key => $aProduct)
                {
                    $aItems[$key] = $this->retrieveInfoFromAuction($aProduct);

                }
            }

        }

        return array($iCnt, $aItems);
    }

    public function getDidntWinAuctions($aConds, $sSort = 'ep.name ASC', $iPage = '', $iLimit = '')
    {
        $aConds[] = 'AND ep.product_status != "deleted"';

        $sQuery = $this->database()
            ->select('ep.product_id')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_product_id = ep.product_id AND eab.auctionbid_user_id = ' . Phpfox::getUserId())
            ->where($aConds)
            ->group('eab.auctionbid_product_id')
            ->execute('');

        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');

        $aItems = array();

        if ($iCnt)
        {
            $aItems = $this->database()
                ->select('ep.*, ept.description_parsed AS description, ec.category_id, ec.title AS category_title, u2.user_name AS latest_bidder_user_name, u2.full_name AS latest_bidder_full_name, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = epa.auction_latest_bidder')
                ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_product_id = ep.product_id AND eab.auctionbid_user_id = ' . Phpfox::getUserId())
                ->where($aConds)
                ->group('eab.auctionbid_product_id')
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aItems as $iKey => $aItem)
            {
                $aItems[$iKey]['link'] = Phpfox::getLib('url')->permalink('auction.detail', $aItem['product_id'], $aItem['name']);
                $aItems[$iKey]['featured'] = false;
                $aItems[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aItem['creating_item_currency']);
            }
        }

        return array($iCnt, $aItems);
    }

    public function getMyWonBidsAucitons($aConds, $sSort = 'ep.name ASC', $iPage = '', $iLimit = '')
    {
        $aConds[] = 'AND ep.product_status != "deleted" and (eap.cartproduct_payment_status is NULL) and eab.auctionbid_status = 0';

        $sQuery = $this->database()
            ->select('ep.product_id')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
            ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_product_id = ep.product_id AND eab.auctionbid_user_id = ' . Phpfox::getUserId())

            ->leftJoin(Phpfox::getT('ecommerce_cart_product'), 'eap', 'eap.cartproduct_product_id = ep.product_id AND eap.cartproduct_type = "bid"')
            ->leftJoin(Phpfox::getT('ecommerce_cart'), 'cart', 'cart.cart_id = eap.cartproduct_cart_id AND cart.cart_user_id = ' . Phpfox::getUserId())

            ->where($aConds)
            ->group('eab.auctionbid_product_id')
            ->execute('');

        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');

        $aItems = array();

        if ($iCnt)
        {
            $aItems = $this->database()
                ->select('ep.*, ept.description_parsed AS description, ec.category_id, ec.title AS category_title, u2.user_name AS latest_bidder_user_name, u2.full_name AS latest_bidder_full_name, epa.auction_total_bid, epa.auction_item_reserve_price, epa.auction_latest_bid_price, epa.auction_won_bid_price, epa.auction_number_transfer, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->leftJoin(Phpfox::getT('user'), 'u2', 'u2.user_id = epa.auction_won_bidder_user_id')
                ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'eab.auctionbid_product_id = ep.product_id AND eab.auctionbid_user_id = ' . Phpfox::getUserId())

                ->leftJoin(Phpfox::getT('ecommerce_cart_product'), 'eap', 'eap.cartproduct_product_id = ep.product_id AND eap.cartproduct_type = "bid"')
                ->leftJoin(Phpfox::getT('ecommerce_cart'), 'cart', 'cart.cart_id = eap.cartproduct_cart_id AND cart.cart_user_id = ' . Phpfox::getUserId())

                ->where($aConds)
                ->group('eab.auctionbid_product_id')
                ->order($sSort)
                ->execute('getSlaveRows');

            foreach ($aItems as $iKey => $aItem)
            {
                $aItems[$iKey]['link'] = Phpfox::getLib('url')->permalink('auction.detail', $aItem['product_id'], $aItem['name']);
                $aItems[$iKey]['featured'] = false;
                $aItems[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aItem['creating_item_currency']);
            }
        }

        return array($iCnt, $aItems);
    }

    public function getUserDidntWinAuction($iProductId, $iWonUserId){
        $aUserDidntWinAuction = array();

        if((int)$iWonUserId){

            $aConds[] = 'AND ep.product_status != "deleted"';
            $aConds[] = 'AND eab.auctionbid_product_id ='.(int)$iProductId;
            $aConds[] = 'AND eab.auctionbid_user_id != '.(int)$iWonUserId;

            $aUserDidntWinAuction = $this->database()
                ->select('DISTINCT eab.auctionbid_user_id')
                ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
                ->join(Phpfox::getT('ecommerce_product'),'ep','ep.product_id = eab.auctionbid_product_id')
                ->join(Phpfox::getT('user'), 'u','u.user_id = eab.auctionbid_user_id')
                ->where($aConds)
                ->execute('getSlaveRows');
        }


        return $aUserDidntWinAuction;
    }

    public function getUserBidThisAuction($iProductId){

        $aConds[] = 'AND ep.product_status != "deleted"';
        $aConds[] = 'AND eab.auctionbid_product_id ='.(int)$iProductId;

        $aUserDidntWinAuction = $this->database()
            ->select('DISTINCT eab.auctionbid_user_id')
            ->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
            ->join(Phpfox::getT('ecommerce_product'),'ep','ep.product_id = eab.auctionbid_product_id')
            ->join(Phpfox::getT('user'), 'u','u.user_id = eab.auctionbid_user_id')
            ->where($aConds)
            ->execute('getSlaveRows');


        return $aUserDidntWinAuction;
    }

    public function getTotalMyAuction($iUserId){

        if(Phpfox::getUserId() == $iUserId  || Phpfox::isAdmin()){
            $aConds = array(
                'AND ep.product_creating_type = "auction"',
                'AND ep.module_id = "auction"',
                'AND ep.user_id = ' . $iUserId,
                'AND ep.product_status != "deleted"'
            );

        }
        else{

            $aConds = array(
                'AND ep.product_creating_type = "auction"',
                'AND ep.module_id = "auction"',
                'AND ep.user_id = ' . $iUserId,
                'AND (ep.product_status = "approved" || ep.product_status = "bidden" || ep.product_status = "running")'
            );
        }

        $iCnt = $this->database()
            ->select('COUNT(*)')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa','epa.product_id = ep.product_id')
            ->where($aConds)
            ->execute('getSlaveField');

        return $iCnt;


    }

    public function getTotalPending(){

        if(Phpfox::getUserParam('auction.can_approve_auction'))
        {
            $aConds = array(
                'AND ep.product_creating_type = "auction"',
                'AND ep.module_id = "auction"',
                'AND ep.product_status = "pending"'
            );

            $iCnt = $this->database()
                ->select('COUNT(*)')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa','epa.product_id = ep.product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id AND u.user_group_id != 5')
                ->where($aConds)
                ->execute('getSlaveField');
        }
        else
            $iCnt = 0;

        return $iCnt;
    }

    public function getBiddenByMyFriendsAuctionsList($aConds, $sSort = 'ep.name ASC', $iPage = '', $iLimit = '')
    {
        $this->database()
            ->select('ep.product_id')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')

            ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'ep.product_id = eab.auctionbid_product_id')
            ->join(Phpfox::getT('user'), 'filter_u', 'filter_u.user_id = eab.auctionbid_user_id')
            ->join(Phpfox::getT('friend'), 'filter_f', 'filter_f.user_id = eab.auctionbid_user_id AND filter_f.friend_user_id = ' . Phpfox::getUserId())

            ->join(Phpfox::getT('user'), 'u2', 'u2.user_id = ep.user_id')

            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')

            ->where($aConds)
            ->group('eab.auctionbid_product_id');

        if (Phpfox::getLib('request')->get('req2') == 'category')
        {
            $this->database()
                ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.category_id = ' . Phpfox::getLib('request')->getInt('req3'));
        }
        else
        {
            $this->database()
                ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0');
        }

        if (Phpfox::getLib('request')->get('req2') == 'tag')
        {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = ep.product_id AND tag.category_id = \'auction\'');
        }


        $sQuery = $this->database()->execute('');

        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');


        $aRows = array();

        if ($iCnt)
        {
            $this->database()
                ->select('ep.*, ec.category_id, ec.title AS category_title, ept.description_parsed AS description, epa.auction_total_bid, epa.auction_item_reserve_price,epa.auction_latest_bid_price, u2.user_name, u2.full_name')
                ->from(Phpfox::getT('ecommerce_product'), 'ep')
                ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id')

                ->join(Phpfox::getT('ecommerce_auction_bid'), 'eab', 'ep.product_id = eab.auctionbid_product_id')
                ->join(Phpfox::getT('user'), 'filter_u', 'filter_u.user_id = eab.auctionbid_user_id')
                ->join(Phpfox::getT('friend'), 'filter_f', 'filter_f.user_id = eab.auctionbid_user_id AND filter_f.friend_user_id = ' . Phpfox::getUserId())

                ->join(Phpfox::getT('user'), 'u2', 'u2.user_id = ep.user_id')

                ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')

                ->where($aConds)
                ->group('eab.auctionbid_product_id')
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt);

            if (Phpfox::getLib('request')->get('req2') == 'category')
            {
                $this->database()
                    ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                    ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.category_id = ' . Phpfox::getLib('request')->getInt('req3'));
            }
            else
            {
                $this->database()
                    ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd',  'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                    ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0');
            }

            if (Phpfox::getLib('request')->get('req2') == 'tag')
            {
                $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = ep.product_id AND tag.category_id = \'auction\'');
            }


            $aRows = $this->database()->execute('getRows');
            foreach ($aRows as $key => $aProduct)
            {
                $aRows[$key] = $this->retrieveInfoFromAuction($aProduct);
                $usergroupId = db()->select('user_group_id')
                    ->from(Phpfox::getT('user'))
                    ->where('user_id =' .(int)$aRows[$key]['user_id'])
                    ->executeField();
                $aRows[$key]['usergroupId'] = $usergroupId;
            }
        }

        return array($iCnt, $aRows);
    }

    public function getLastChildCategoryIdOfAuction($iProductId){

        $aCat = $this->database()->select('ect.category_id AS `parent_category_id`, ect.title AS `parent_title`')
            ->from(Phpfox::getT('ecommerce_category_data'), 'ecd')
            ->join(Phpfox::getT('ecommerce_category'), 'ect', 'ect.category_id = ecd.category_id')
            ->where('ecd.product_id = ' . (int) $iProductId.' AND ect.parent_id = 0')
            ->order('ect.parent_id DESC')
            ->limit(1)
            ->execute('getSlaveRows');

        if(isset($aCat[0]['parent_category_id'])){
            return array(
                'category_id' => $aCat[0]['parent_category_id'],
                'title' => Phpfox::getLib('locale')->convert($aCat[0]['parent_title']),
            );
        } else {
            return false;
        }
    }

    public function  getUserWatchListFromProductId($iProductId){
        $aRows = $this->database()
            ->select('ew.user_id')
            ->from(Phpfox::getT('ecommerce_watch'), 'ew')
            ->where('ew.product_id = '.(int)$iProductId)
            ->execute('getSlaveRows');

        $aUserWathList= array();
        if(count($aRows)){
            foreach($aRows as $keyUser => $aUser){
                $aUserWathList[$aUser['user_id']] = $aUser['user_id'];
            }
        }
        return $aUserWathList;
    }

    public function doComparisonField($aFields){
        $aFieldStatus = array();
        foreach ($aFields as $keyaFields => $valueaFields) {
            switch ($valueaFields['comparison_id']) {
                case '1':
                    $aFieldStatus['name'] = false;
                    if($valueaFields['is_active']){
                        $aFieldStatus['name'] = true;
                    }
                    break;
                case '2':
                    $aFieldStatus['reserve_price'] = false;
                    if($valueaFields['is_active']){
                        $aFieldStatus['reserve_price'] = true;
                    }
                    break;
                case '3':
                    $aFieldStatus['total_bids'] = false;
                    if($valueaFields['is_active']){
                        $aFieldStatus['total_bids'] = true;
                    }
                    break;
                case '4':
                    $aFieldStatus['total_orders'] = false;
                    if($valueaFields['is_active']){
                        $aFieldStatus['total_orders'] = true;
                    }
                    break;
                case '5':
                    $aFieldStatus['total_view'] = false;
                    if($valueaFields['is_active']){
                        $aFieldStatus['total_view'] = true;
                    }
                    break;
                case '6':
                    $aFieldStatus['seller'] = false;
                    if($valueaFields['is_active']){
                        $aFieldStatus['seller'] = true;
                    }
                    break;
                case '7':
                    $aFieldStatus['custom_field'] = false;
                    if($valueaFields['is_active']){
                        $aFieldStatus['custom_field'] = true;
                    }
                    break;
                case '8':
                    $aFieldStatus['description'] = false;
                    if($valueaFields['is_active']){
                        $aFieldStatus['description'] = true;
                    }
                    break;
            }
        }

        return $aFieldStatus;
    }
    public function changePageWhenAccessingAuctionDetail($page){
        switch ($page) {
            case 'add-comment':
                $page = 'activities';
                break;
        }

        return $page;
    }


}

?>