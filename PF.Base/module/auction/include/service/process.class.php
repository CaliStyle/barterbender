<?php
defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Process extends Phpfox_Service
{
    private $_aAuctionCategories = array();

    private $_bIsPublished = false;

    public function __construct()
    {

    }

    public function deleteImage($iImageId)
    {
        $aSuffix = array('', '_100', '_120', '_200', '_400');

        $aImage = $this->database()->select('di.image_id, di.image_path, di.server_id')
            ->from(Phpfox::getT('ecommerce_product_image'), 'di')
            ->where('di.image_id = ' . $iImageId)
            ->execute('getSlaveRow');

        if (!$aImage) {
            return Phpfox_Error::set(_p('unable_to_find_the_image'));
        }

        $iFileSizes = 0;
        foreach ($aSuffix as $sSize) {
            $sImage = Phpfox::getParam('core.dir_pic') . 'ynecommerce/' . sprintf($aImage['image_path'], $sSize);
            if (file_exists($sImage)) {
                $iFileSizes += filesize($sImage);
                @unlink($sImage);
            }
        }
        return $this->database()->delete(Phpfox::getT('ecommerce_product_image'), 'image_id = ' . $aImage['image_id']);
    }

    public function deleteGlobalSetting()
    {
        $this->database()->delete(Phpfox::getT('ecommerce_auction_global_setting'), 'TRUE');
    }

    public function addGlobalSetting($aDefaultSetting, $aActualSetting)
    {
        $sDefaultSetting = json_encode($aDefaultSetting);
        $sActualSetting = json_encode($aActualSetting);

        $id = $this->database()->insert(Phpfox::getT('ecommerce_auction_global_setting'), array(
            "default_setting" => $sDefaultSetting,
            "actual_setting" => $sActualSetting
        ));

        return $id;
    }

    public function delete($iProductId)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('product_status' => 'deleted'), 'product_id = ' . (int)$iProductId);
    }

    public function saveLastingSearch($data)
    {
        if (count($data)) {
            $aSearch = $this->database()
                ->select('epsh.user_id')
                ->from(Phpfox::getT('ecommerce_product_searchhistory'), 'epsh')
                ->where('epsh.user_id = ' . (int)Phpfox::getUserId())
                ->execute('getSlaveRow');

            if (count($aSearch)) {
                $this->database()->update(Phpfox::getT('ecommerce_product_searchhistory'), array('data' => json_encode($data)), 'user_id = ' . Phpfox::getUserId());
            } else {
                $aSearch = array(
                    'user_id' => Phpfox::getUserId(),
                    'data' => json_encode($data),
                );

                $this->database()->insert(Phpfox::getT('ecommerce_product_searchhistory'), $aSearch);
            }
        }
    }

    public function getCategoriesFromForm($aVals)
    {
        if (isset($aVals['category']) && count($aVals['category'])) {
            if (empty($aVals['category'][0])) {
                return false;
            } else if (!is_array($aVals['category'])) {
                $this->_aAuctionCategories[] = $aVals['category'];
            } else {
                foreach ($aVals['category'] as $aCategory) {

                    foreach ($aCategory as $iCategory) {
                        if (empty($iCategory)) {
                            continue;
                        }

                        if (!is_numeric($iCategory)) {
                            continue;
                        }

                        $this->_aAuctionCategories[] = $iCategory;
                    }
                }
            }
            return true;
        }
    }

    public function add($aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');
        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name']);

        $aUOMs = Phpfox::getService('ecommerce.uom')->getAll();

        if (!count($aUOMs)) {
            return Phpfox_Error::set(_p('please_input_uom_unit_in_admincp'));
        }

        if (!$this->getCategoriesFromForm($aVals)) {
            return Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
        }

        $aVals['categories'] = $this->_aAuctionCategories;

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        if (!isset($aVals['privacy_photo'])) {
            $aVals['privacy_photo'] = 0;
        }

        if (!isset($aVals['privacy_video'])) {
            $aVals['privacy_video'] = 0;
        }

        $aVals['name'] = $oFilter->clean(strip_tags($aVals['name']), 255);

        $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_time_hour'], $aVals['start_time_minute'], 0, $aVals['start_time_month'], $aVals['start_time_day'], $aVals['start_time_year']);
        $iEndTime = Phpfox::getLib('date')->mktime($aVals['end_time_hour'], $aVals['end_time_minute'], 0, $aVals['end_time_month'], $aVals['end_time_day'], $aVals['end_time_year']);

        if ($iEndTime < $iStartTime) {
            return Phpfox_Error::set(_p('please_edit_auction_end_date_after_start_date'));
        }
        $aVals['start_time'] = $iStartTime;
        $aVals['end_time'] = $iEndTime;

        if (isset($aVals['quantity'])) {
            if (!is_numeric($aVals['quantity'])) {
                return Phpfox_Error::set(_p('please_enter_number_for_quantity'));
            } elseif ($aVals['quantity'] < 0) {
                return Phpfox_Error::set(_p('please_edit_quantity_more_than_zero'));
            }
        }
        if(isset($aVals['reserve_price']) && (int)$aVals['reserve_price'] == 0)
        {
            return Phpfox_Error::set(_p('please_enter_reserve_price_more_than_zero'));
        }
        if(isset($aVals['buynow_price']) && (int)$aVals['buynow_price'] == 0)
        {
            return Phpfox_Error::set(_p('please_enter_buy_now_price_more_than_zero'));
        }
        /*insert into product ecommerce table*/
        $iProductId = Phpfox::getService('ecommerce.process')->add($aVals, 'auction');

        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        /*insert into product auction*/
        $aInsert = array(
            'product_id' => $iProductId,
            'auction_item_reserve_price' => (isset($aVals['reserve_price']) ? $aVals['reserve_price'] : '0'),
            'auction_item_buy_now_price' => ((isset($aVals['buynow_price']) && (int)$aVals['buynow_price']) ? $aVals['buynow_price'] : '0'),
            'allow_contact_if_end' => (isset($aVals['allow_contact_end']) ? $aVals['allow_contact_end'] : '0'),
            'is_hide_reserve_price' => (isset($aVals['hide_reserve_price']) ? '1' : '0'),
            'receive_notification_someone_bid' => (isset($aVals['is_receive_notification']) ? '1' : '0'),
            'auction_latest_bid_price' => 0
        );

        $iAuctionId = $this->database()->insert(Phpfox::getT('ecommerce_product_auction'), $aInsert);
        return $iAuctionId;
    }

    public function update($iAuctionId, $aVals)
    {
        $aEditedAuction = Phpfox::getService('auction')->getQuickAuctionByAuctionId($iAuctionId);
        if (!count($aEditedAuction)) {
            return false;
        }

        if (empty($aVals['name'])) {
            return Phpfox_Error::set(_p('fill_a_title_for_you_auction'));
        }

        $aUOMs = Phpfox::getService('ecommerce.uom')->getAll();
        if (!count($aUOMs)) {
            return Phpfox_Error::set(_p('please_input_uom_unit_in_admincp'));
        }
        $aEditedProduct = Phpfox::getService('ecommerce')->getQuickProductById($aEditedAuction['product_id']);
        $oFilter = Phpfox::getLib('parse.input');

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name']);

        if (!$this->getCategoriesFromForm($aVals)) {
            return Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
        }

        $aVals['categories'] = $this->_aAuctionCategories;

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        if (!isset($aVals['privacy_photo'])) {
            $aVals['privacy_photo'] = 0;
        }

        if (!isset($aVals['privacy_video'])) {
            $aVals['privacy_video'] = 0;
        }

        $aVals['name'] = $oFilter->clean(strip_tags($aVals['name']), 255);

        if(!empty($aVals['start_time_hour'])) {
            $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_time_hour'], $aVals['start_time_minute'], 0, $aVals['start_time_month'], $aVals['start_time_day'], $aVals['start_time_year']);
            $iEndTime = Phpfox::getLib('date')->mktime($aVals['end_time_hour'], $aVals['end_time_minute'], 0, $aVals['end_time_month'], $aVals['end_time_day'], $aVals['end_time_year']);

            if ($iEndTime < $iStartTime) {
                return Phpfox_Error::set(_p('please_edit_auction_end_date_after_start_date'));
            }

            if ($aEditedProduct['product_status'] != 'running' && $aEditedProduct['product_status'] != 'bidden') {
                $aVals['start_time'] = $iStartTime;
                $aVals['end_time'] = $iEndTime;

            } else {
                $aVals['start_time'] = $aEditedAuction['start_time'];
                $aVals['end_time'] = $aEditedAuction['end_time'];
            }
        }
        else
        {
            $aVals['start_time'] = $aEditedAuction['start_time'];
            $aVals['end_time'] = $aEditedAuction['end_time'];
        }
        if (isset($aVals['quantity'])) {
            if (!is_numeric($aVals['quantity'])) {
                return Phpfox_Error::set(_p('please_enter_number_for_quantity'));
            } elseif ($aVals['quantity'] < 0) {
                return Phpfox_Error::set(_p('please_edit_quantity_more_than_zero'));
            }
        }

        /*insert into product ecommerce table*/
        Phpfox::getService('ecommerce.process')->update($aVals, $aEditedAuction['product_id'], 'auction');

        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        /*insert into product auction*/
        $aUpdate = array(
            'allow_contact_if_end' => (isset($aVals['allow_contact_end']) ? $aVals['allow_contact_end'] : '0'),
            'is_hide_reserve_price' => (isset($aVals['hide_reserve_price']) ? '1' : '0'),
            'receive_notification_someone_bid' => (isset($aVals['is_receive_notification']) ? '1' : '0'),
        );

        if ($aEditedProduct['product_status'] != 'running' && $aEditedProduct['product_status'] != 'bidden') {
            $aUpdate['auction_item_reserve_price'] = (isset($aVals['reserve_price']) ? $aVals['reserve_price'] : '0');
            $aUpdate['auction_item_buy_now_price'] = (isset($aVals['buynow_price']) ? $aVals['buynow_price'] : '0');
        }

        $iAuctionId = $this->database()->update(Phpfox::getT('ecommerce_product_auction'), $aUpdate, ' auction_id = ' . (int)$iAuctionId);

        return $iAuctionId;
    }

    public function deleteMultiple($aDeleteIds)
    {
        $aTemp = array();
        foreach ($aDeleteIds as $iDeleteId) {
            if (is_numeric($iDeleteId)) {
                $aTemp[] = $iDeleteId;
            }
        }

        if (!$aTemp) {
            return;
        }

        $sDeletedIds = implode(',', $aTemp);

        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('product_status' => 'deleted'), 'product_id IN (' . $sDeletedIds . ')');
    }

    public function featureAuctionBackEnd($iProductId, $bFeatured)
    {
        if ($bFeatured) {
            $this->database()->update(Phpfox::getT('ecommerce_product'), array('feature_start_time' => PHPFOX_TIME, 'feature_end_time' => 4294967295), 'product_id = ' . (int)$iProductId);
        } else {
            $this->database()->update(Phpfox::getT('ecommerce_product'), array('feature_start_time' => 0, 'feature_end_time' => 0, 'feature_day' => 0), 'product_id = ' . (int)$iProductId);
        }
    }

    /**
     * This function is only used for seller. ???
     */
    public function publish($iProductId, $aProduct)
    {
        if (!isset($aProduct['product_id'])) {
            return false;
        }

        $sStatus = 'pending';

        /*
        if ($aProduct['start_time'] > PHPFOX_TIME)
        {
            $sStatus = 'approved';
        }
        elseif ($aProduct['start_time'] <= PHPFOX_TIME && $aProduct['end_time'] > PHPFOX_TIME)
        {
            $bHasBidder = Phpfox::getService('auction.bid')->hasBidder($iProductId);
            if ($bHasBidder)
            {
                $sStatus = 'bidden';
            }
            else
            {
                $sStatus = 'running';
            }
        }
        elseif ($aProduct['end_time'] <= PHPFOX_TIME)
        {
            $sStatus = 'completed';
        }
        */
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('product_status' => $sStatus), 'product_id = ' . (int)$iProductId);
    }


    public function updateAuctionStatus($iAuctionId, $sStatus)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('product_status' => $sStatus), 'product_id = ' . (int)$iAuctionId);
    }

    public function updateTotalView($iAuctionId)
    {
        $this->database()->updateCounter('ecommerce_product', 'total_view', 'product_id', $iAuctionId);
    }

    public function updateTotalAuctions($iUserId)
    {
        $iTotal = Phpfox::getService('auction')->getTotalMyAuction($iUserId);
        $this->database()->update(Phpfox::getT('user_field'), array('total_auction' => $iTotal), 'user_id = ' . (int)$iUserId);
    }

    public function updateLastBid($iAuctionId, $iLatestBidderId, $fLatestBidPrice)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_product_auction'), array('auction_latest_bidder' => $iLatestBidderId, 'auction_latest_bid_price' => $fLatestBidPrice), 'product_id = ' . (int)$iAuctionId);
    }

    public function autoUpdateStatusAuctions()
    {
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('product_status' => 'completed'), '(product_status = "approved" OR product_status = "running"  OR product_status = "bidden") AND product_creating_type = "auction" AND end_time < ' . PHPFOX_TIME);
    }

    public function checkAndUpdateStatus($aAuction)
    {
        switch ($aAuction['product_status']) {
            case 'approved':
                if ($aAuction['start_time'] <= PHPFOX_TIME && $aAuction['end_time'] >= PHPFOX_TIME) {
                    Phpfox::getService('auction.process')->updateAuctionStatus($aAuction['product_id'], 'running');

                }

                if ($aAuction['product_quantity'] == 0) {
                    Phpfox::getService('ecommerce.process')->close($aAuction['product_id']);
                }

                break;

            case 'running':
                if ($aAuction['start_time'] >= PHPFOX_TIME) {
                    /*this code look stupid*/
                    Phpfox::getService('auction.process')->updateAuctionStatus($aAuction['product_id'], 'approved');
                }

                $iCountBidder = Phpfox::getService('auction')->getCountBidderOfAuction($aAuction['product_id']);

                if ($iCountBidder) {
                    Phpfox::getService('auction.process')->updateAuctionStatus($aAuction['product_id'], 'bidden');
                }


                if ($aAuction['end_time'] <= PHPFOX_TIME) {
                    Phpfox::getService('auction.process')->updateAuctionStatus($aAuction['product_id'], 'completed');

                }
                if ($aAuction['product_quantity'] == 0) {
                    Phpfox::getService('ecommerce.process')->close($aAuction['product_id']);
                }
                break;

            case 'bidden':
                $iProductId = $aAuction['product_id'];
                $aAuction = array();
                $aAuction = Phpfox::getService('auction')->getAuctionById($iProductId);

                if ($aAuction['end_time'] <= PHPFOX_TIME) {
                    if (Phpfox::getUserId()) {
                        return false;
                    }
                    Phpfox::getService('auction.process')->updateAuctionStatus($aAuction['product_id'], 'completed');

                    if ((int)$aAuction['auction_latest_bidder']) {

                        /*if has winner*/
                        if ($aAuction['auction_latest_bid_price'] > $aAuction['auction_item_reserve_price'] && $aAuction['product_quantity'] > 0) {

                            $aUpdateWinnerAuction = array(
                                'auction_won_bidder_user_id' => $aAuction['auction_latest_bidder'],
                                'auction_won_bid_price' => $aAuction['auction_latest_bid_price'],
                            );

                            //when someone win
                            $this->database()->update(Phpfox::getT('ecommerce_product_auction'), $aUpdateWinnerAuction, 'product_id = ' . (int)$aAuction['product_id']);

                            $aAuction = Phpfox::getService('auction')->getAuctionById($aAuction['product_id']);

                            /*send email and notification to won user id*/
                            if ((int)$aAuction['auction_won_bidder_user_id']) {

                                $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);

                                $aSeller = Phpfox::getService('user')->get($aAuction['user_id']);
                                $sLinkSellerUser = Phpfox::getLib('url')->makeUrl('profile', array($aSeller['user_name']));

                                $iHighestBid = $aAuction['auction_won_bid_price'];
                                $iNumberBids = $aAuction['auction_total_bid'];
                                $sMessageWonUser = _p('auction_name_title_by_seller_highest_bid_symbol_currency_amount_number_bids',
                                    array(
                                        'title' => "<a href='" . $sLink . "'>" . $aAuction['name'] . "</a>",
                                        'seller' => "<a href='" . $sLinkSellerUser . "'>" . $aSeller['full_name'] . "</a>",
                                        'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']),
                                        'amount' => $iHighestBid,
                                        'number' => $iNumberBids,
                                    )
                                );
                                $iReceiveId = $aAuction['auction_won_bidder_user_id'];
                                $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                                $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                                $email = $aUser['email'];
                                $aExtraData = array();
                                $aExtraData['seller_id'] = $aAuction['user_id'];

                                $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']);
                                $aExtraData['amount'] = $iHighestBid;
                                $aExtraData['number_bids'] = $iNumberBids;

                                $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction', 'congratulations_you_won', $language_id, $iReceiveId, $iProductId, $aExtraData);
                                Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);


                                Phpfox::getService('notification.process')->add('auction_wonbid', $aAuction['product_id'], $aAuction['auction_won_bidder_user_id'], $aAuction['auction_won_bidder_user_id']);

                            }

                            /*send email and notification to seller*/
                            if ((int)$aAuction['user_id']) {

                                $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);

                                $iHighestBid = $aAuction['auction_won_bid_price'];

                                $sMessageEndAuction = _p('auction_name_title_highest_bid_symbol_currency_amount_ended_on_date_time',
                                    array(
                                        'title' => "<a href='" . $sLink . "'>" . $aAuction['name'] . "</a>",
                                        'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']),
                                        'amount' => $iHighestBid,
                                        'date_time' => Phpfox::getTime('F-d-Y h:m:i', PHPFOX_TIME),
                                    )
                                );

                                $iReceiveId = $aAuction['user_id'];
                                $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                                $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                                $email = $aUser['email'];
                                $aExtraData = array();

                                $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']);
                                $aExtraData['amount'] = $iHighestBid;
                                $aExtraData['date_time'] = Phpfox::getTime('F-d-Y h:m:i', PHPFOX_TIME);

                                $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction', 'your_auction_has_ended', $language_id, $iReceiveId, $iProductId, $aExtraData);
                                Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);


                                Phpfox::getService('notification.process')->add('auction_wonbid', $aAuction['product_id'], $aAuction['user_id'], $aAuction['user_id']);

                            }


                            /*send email and notification to didnt won user id*/

                            $aUserWatchList = Phpfox::getService('auction')->getUserWatchListFromProductId($aAuction['product_id']);

                            $aUserDidntWinBids = Phpfox::getService('auction')->getUserDidntWinAuction($aAuction['product_id'], $aAuction['auction_won_bidder_user_id']);

                            if (count($aUserDidntWinBids)) {
                                foreach ($aUserDidntWinBids as $keyUserDidntWin => $iUserDidntWin) {

                                    if (isset($aUserWatchList[$iUserDidntWin['auctionbid_user_id']])) {
                                        unset($aUserWatchList[$iUserDidntWin['auctionbid_user_id']]);
                                    }

                                    $aUserDidntWinBid = Phpfox::getService('user')->get($iUserDidntWin['auctionbid_user_id']);
                                    $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);
                                    $sLinkDidntWin = Phpfox::permalink('auction.didnt-win', null, null);
                                    $aSeller = Phpfox::getService('user')->get($aAuction['user_id']);
                                    $sLinkSellerUser = Phpfox::getLib('url')->makeUrl('profile', array($aSeller['user_name']));

                                    $iHighestBid = $aAuction['auction_won_bid_price'];
                                    $iNumberBids = $aAuction['auction_total_bid'];

                                    $sMessageDidntWin = _p('auction_name_title_by_seller_highest_bid_symbol_currency_amount_number_bids',
                                        array(
                                            'title' => "<a href='" . $sLink . "'>" . $aAuction['name'] . "</a>",
                                            'seller' => "<a href='" . $sLinkSellerUser . "'>" . $aSeller['full_name'] . "</a>",
                                            'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']),
                                            'amount' => $iHighestBid,
                                            'number' => $iNumberBids,
                                        )
                                    );

                                    $iReceiveId = $aUserDidntWinBid['user_id'];
                                    $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                                    $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                                    $email = $aUser['email'];
                                    $iProductId = $aAuction['product_id'];
                                    $aExtraData = array();
                                    $aExtraData['seller_id'] = $aAuction['user_id'];

                                    $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']);
                                    $aExtraData['amount'] = $iHighestBid;

                                    $aExtraData['number_bids'] = $iNumberBids;

                                    $aExtraData['url'] = $sLinkDidntWin;

                                    $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction', 'bidding_has_ended', $language_id, $iReceiveId, $iProductId, $aExtraData);
                                    Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);


                                    Phpfox::getService('notification.process')->add('auction_losebid', $aAuction['product_id'], $aUserDidntWinBid['user_id'], $aUserDidntWinBid['user_id']);

                                }
                            }

                            if (count($aUserWatchList)) {
                                foreach ($aUserWatchList as $iUserWatchList) {
                                    Phpfox::getService('notification.process')->add('auction_endbid', $aAuction['product_id'], $iUserWatchList, $iUserWatchList);
                                }
                            }
                            /*create feed for won user id*/
                            Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('auction_wonbid', $aAuction['product_id'], $aAuction['privacy'], (isset($aAuction['privacy_comment']) ? (int)$aAuction['privacy_comment'] : 0), (isset($aAuction['item_id']) ? (int)$aAuction['item_id'] : 0), $aAuction['auction_won_bidder_user_id']) : 0;

                        } else {
                            //if no one win this bid.

                            /*send email and notification to seller*/
                            if ((int)$aAuction['user_id']) {

                                $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);

                                $iHighestBid = $aAuction['auction_latest_bid_price'];

                                $sMessageEndAuction = _p('auction_name_title_highest_bid_symbol_currency_amount_ended_on_date_time',
                                    array(
                                        'title' => "<a href='" . $sLink . "'>" . $aAuction['name'] . "</a>",
                                        'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']),
                                        'amount' => $iHighestBid,
                                        'date_time' => Phpfox::getTime('F-d-Y h:m:i', PHPFOX_TIME),
                                    )
                                );

                                $iReceiveId = $aAuction['user_id'];
                                $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                                $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                                $email = $aUser['email'];
                                $aExtraData = array();

                                $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']);
                                $aExtraData['amount'] = $iHighestBid;
                                $aExtraData['date_time'] = Phpfox::getTime('F-d-Y h:m:i', PHPFOX_TIME);


                                $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction', 'your_auction_has_ended', $language_id, $iReceiveId, $iProductId, $aExtraData);
                                Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);


                                Phpfox::getService('notification.process')->add('auction_endbid', $aAuction['product_id'], $aAuction['user_id'], $aAuction['user_id']);
                            }

                            /*send email and notification to user bid*/
                            $aUserBids = Phpfox::getService('auction')->getUserBidThisAuction($aAuction['product_id']);

                            $aUserWatchList = Phpfox::getService('auction')->getUserWatchListFromProductId($aAuction['product_id']);

                            if (count($aUserBids)) {
                                foreach ($aUserBids as $keyUserBid => $iUserBid) {

                                    if (isset($aUserWatchList[$iUserBid['auctionbid_user_id']])) {
                                        unset($aUserWatchList[$iUserBid['auctionbid_user_id']]);
                                    }

                                    $aUserBid = Phpfox::getService('user')->get($iUserBid['auctionbid_user_id']);

                                    $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);

                                    $sLinkDidntWin = Phpfox::permalink('auction.didnt-win', null, null);

                                    $aSeller = Phpfox::getService('user')->get($aAuction['user_id']);
                                    $sLinkSellerUser = Phpfox::getLib('url')->makeUrl('profile', array($aSeller['user_name']));

                                    $iHighestBid = $aAuction['auction_latest_bid_price'];
                                    $iNumberBids = $aAuction['auction_total_bid'];

                                    $sMessageDidntWin = _p('auction_name_title_by_seller_highest_bid_symbol_currency_amount_number_bids',
                                        array(
                                            'title' => "<a href='" . $sLink . "'>" . $aAuction['name'] . "</a>",
                                            'seller' => "<a href='" . $sLinkSellerUser . "'>" . $aSeller['full_name'] . "</a>",
                                            'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']),
                                            'amount' => $iHighestBid,
                                            'number' => $iNumberBids,
                                        )
                                    );

                                    $iReceiveId = $aUserBid['user_id'];
                                    $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                                    $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                                    $email = $aUser['email'];
                                    $iProductId = $aAuction['product_id'];
                                    $aExtraData = array();
                                    $aExtraData['seller_id'] = $aAuction['user_id'];

                                    $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']);
                                    $aExtraData['amount'] = $iHighestBid;

                                    $aExtraData['number_bids'] = $iNumberBids;

                                    $aExtraData['url'] = $sLinkDidntWin;

                                    $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction', 'bidding_has_ended', $language_id, $iReceiveId, $iProductId, $aExtraData);
                                    Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);

                                    Phpfox::getService('notification.process')->add('auction_endbid', $aAuction['product_id'], $aUserBid['user_id'], $aUserBid['user_id']);
                                }
                            }

                            if (count($aUserWatchList)) {
                                foreach ($aUserWatchList as $iUserWatchList) {
                                    Phpfox::getService('notification.process')->add('auction_endbid', $aAuction['product_id'], $iUserWatchList, $iUserWatchList);
                                }
                            }

                        }


                    }
                }
                if ($aAuction['product_quantity'] == 0) {
                    Phpfox::getService('ecommerce.process')->close($aAuction['product_id']);
                }
                break;
            case 'completed':
                if (Phpfox::getUserId()) {
                    return false;
                }
                /*check transfer auction to other person*/
                $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->get($aAuction['user_id']);
                /*echo '<pre>';
                print_r($aSellerSettings);
                die;*/
                $iMaxNumberTransfer = (isset($aSellerSettings['number_of_transfers']) && $aSellerSettings['number_of_transfers'] != NULL) ? $aSellerSettings['number_of_transfers'] : 2;
                $iMaxTimeCompleteTransfer = (isset($aSellerSettings['time_complete_transaction']) && $aSellerSettings['time_complete_transaction'] != NULL) ? $aSellerSettings['time_complete_transaction'] : 7;
                $iMaxTimeCompleteTransfer = $iMaxTimeCompleteTransfer * 24 * 3600;
                //$iMaxTimeCompleteTransfer = 10;
                if ($iMaxNumberTransfer > 0) {
                    if (isset($aAuction['auction_won_bidder_user_id']) && $aAuction['auction_won_bidder_user_id'] != NULL && (int)$aAuction['auction_number_transfer'] < $iMaxNumberTransfer) {

                        $iOrderPersionTransfer = (int)$aAuction['auction_number_transfer'] + 1;

                        if ($aAuction['end_time'] + $iOrderPersionTransfer * $iMaxTimeCompleteTransfer < PHPFOX_TIME) {
                            /*transfer to new person*/
                            $aBids = Phpfox::getService('auction.bid')->getNextPersionBid($aAuction['product_id']);

                            $iNextUserId = 0;
                            $iWonBidPrice = 0;
                            if (count($aBids)) {
                                foreach ($aBids as $keyBidUserId => $aBidValue) {
                                    if ($keyBidUserId == $iOrderPersionTransfer) {
                                        $iNextUserId = $aBidValue['auctionbid_user_id'];
                                        $iWonBidPrice = $aBidValue['auctionbid_price'];
                                    }
                                }
                            }

                            if ($iNextUserId != 0 && $iWonBidPrice != 0) {
                                $aUpdateWinnerAuction = array(
                                    'auction_won_bidder_user_id' => $iNextUserId,
                                    'auction_won_bid_price' => $iWonBidPrice,
                                    'auction_number_transfer' => $iOrderPersionTransfer
                                );

                                $iOldWonUserId = $aAuction['auction_won_bidder_user_id'];
                                $iOldWonPrice = $aAuction['auction_won_bid_price'];
                                $this->database()->update(Phpfox::getT('ecommerce_product_auction'), $aUpdateWinnerAuction, 'product_id = ' . (int)$aAuction['product_id']);

                                $aAuction = Phpfox::getService('auction')->getAuctionById($aAuction['product_id']);

                                /*send notification and email to new winner*/
                                if ((int)$aAuction['auction_won_bidder_user_id']) {

                                    $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);

                                    $aSeller = Phpfox::getService('user')->get($aAuction['user_id']);
                                    $sLinkSellerUser = Phpfox::getLib('url')->makeUrl('profile', array($aSeller['user_name']));

                                    $iHighestBid = $aAuction['auction_won_bid_price'];
                                    $iNumberBids = $aAuction['auction_total_bid'];
                                    $sMessageWonUser = _p('auction_name_title_by_seller_highest_bid_symbol_currency_amount_number_bids',
                                        array(
                                            'title' => "<a href='" . $sLink . "'>" . $aAuction['name'] . "</a>",
                                            'seller' => "<a href='" . $sLinkSellerUser . "'>" . $aSeller['full_name'] . "</a>",
                                            'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']),
                                            'amount' => $iHighestBid,
                                            'number' => $iNumberBids,
                                        )
                                    );
                                    $iReceiveId = $aAuction['auction_won_bidder_user_id'];
                                    $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                                    $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                                    $email = $aUser['email'];
                                    $iProductId = $aAuction['product_id'];
                                    $aExtraData = array();
                                    $aExtraData['seller_id'] = $aAuction['user_id'];

                                    $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']);
                                    $aExtraData['amount'] = $iHighestBid;
                                    $aExtraData['number_bids'] = $iNumberBids;

                                    $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction', 'congratulations_you_won', $language_id, $iReceiveId, $iProductId, $aExtraData);
                                    Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);


                                    Phpfox::getService('notification.process')->add('auction_wonbid', $aAuction['product_id'], $aAuction['auction_won_bidder_user_id'], $aAuction['auction_won_bidder_user_id']);

                                }

                                /*send notification and email to seller*/
                                if ((int)$aAuction['user_id']) {

                                    $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);

                                    $aNewWonUser = Phpfox::getService('user')->get($aAuction['auction_won_bidder_user_id']);
                                    $sLinkWonUser = Phpfox::getLib('url')->makeUrl('profile', array($aNewWonUser['user_name']));

                                    $iHighestBid = $aAuction['auction_won_bid_price'];

                                    $sMessageTransferredWinner = _p('auction_name_title_buyer_buyer_highest_bid_symbol_currency_amount_end_on_date_time',
                                        array(
                                            'title' => "<a href='" . $sLink . "'>" . $aAuction['name'] . "</a>",
                                            'buyer' => "<a href='" . $sLinkWonUser . "'>" . $aNewWonUser['full_name'] . "</a>",
                                            'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']),
                                            'amount' => $iHighestBid,
                                            'date_time' => Phpfox::getTime('F-d-Y h:m:i', $aAuction['end_time']),
                                        )
                                    );

                                    $iReceiveId = $aAuction['user_id'];
                                    $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                                    $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                                    $email = $aUser['email'];
                                    $iProductId = $aAuction['product_id'];
                                    $aExtraData = array();
                                    $aExtraData['buyer_id'] = $aAuction['auction_won_bidder_user_id'];

                                    $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']);
                                    $aExtraData['amount'] = $iHighestBid;
                                    $aExtraData['date_time'] = Phpfox::getTime('F-d-Y h:m:i', $aAuction['end_time']);

                                    $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction', 'auction_have_been_transferred_seller', $language_id, $iReceiveId, $iProductId, $aExtraData);
                                    Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);


                                    Phpfox::getService('notification.process')->add('auction_transferbid', $aAuction['product_id'], $aAuction['user_id'], $aAuction['user_id']);

                                }

                                /*send notification and email to old winner*/
                                if ((int)$iOldWonUserId) {

                                    $sLinkPage = Phpfox::permalink('auction.didnt-win', null, null);

                                    $sLink = Phpfox::permalink('auction.detail', $aAuction['product_id'], $aAuction['name']);

                                    $aSeller = Phpfox::getService('user')->get($aAuction['user_id']);
                                    $sLinkSellerUser = Phpfox::getLib('url')->makeUrl('profile', array($aSeller['user_name']));

                                    $iHighestBid = $aAuction['auction_won_bid_price'];

                                    $sMessageTransferredWinner = _p('auction_name_title_seller_name_seller_highest_bid_symbol_currency_amount_end_on_date_time',
                                        array(
                                            'title' => "<a href='" . $sLink . "'>" . $aAuction['name'] . "</a>",
                                            'seller' => "<a href='" . $sLinkSellerUser . "'>" . $aSeller['full_name'] . "</a>",
                                            'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']),
                                            'amount' => $iHighestBid,
                                            'date_time' => Phpfox::getTime('F-d-Y h:m:i', $aAuction['end_time']),
                                        )
                                    );

                                    $iReceiveId = $iOldWonUserId;
                                    $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                                    $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                                    $email = $aUser['email'];
                                    $iProductId = $aAuction['product_id'];
                                    $aExtraData = array();
                                    $aExtraData['seller_id'] = $aAuction['user_id'];

                                    $aExtraData['symbol_currency'] = Phpfox::getService('core.currency')->getSymbol($aAuction['creating_item_currency']);
                                    $aExtraData['amount'] = $iHighestBid;
                                    $aExtraData['date_time'] = Phpfox::getTime('F-d-Y h:m:i', $aAuction['end_time']);

                                    $aExtraData['url'] = $sLinkPage;

                                    $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate('auction', 'auction_have_been_transferred_old_winner', $language_id, $iReceiveId, $iProductId, $aExtraData);
                                    Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
                                    Phpfox::getService('notification.process')->add('auction_transferbid', $aAuction['product_id'], $iOldWonUserId, $iOldWonUserId);
                                }

                            }

                        }
                    }
                }

            default:

                break;
        }

        $sCategoryTextRelated = Phpfox::getService('ecommerce.category')->getCategoryIds($aAuction['product_id']);

        Phpfox::getService('ecommerce.category.process')->updateCountAuctionsForCategory($sCategoryTextRelated);
    }

    public function checkAllStatus()
    {
        $aConds = array(
            'ep.product_creating_type = "auction"',
            'AND (ep.product_status != "deleted")'
        );

        $aRows = $this->database()
            ->select('ep.*, epa.*')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->where($aConds)
            ->execute('getRows');
        if (count($aRows)) {
            foreach ($aRows as $aAuction) {
                $this->checkAndUpdateStatus($aAuction);
            }
        }
        return $aRows;
    }


    public function setMainProductPhoto($iProductId, $iPhotoId)
    {
        $aImage = $this->database()->select('epi.*')
            ->from(Phpfox::getT('ecommerce_product_image'),'epi')
            ->where('epi.product_id = '.$iProductId.' AND epi.image_id = '.$iPhotoId)
            ->execute('getRow');
        if (!empty($aImage)) {
            return $this->database()->update(Phpfox::getT('ecommerce_product'), array('logo_path' => $aImage['image_path'],'server_id' => $aImage['server_id']), 'product_id = '.$iProductId);
        } else {
            return false;
        }
    }

    public function removeMainPhoto($iProductId)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('logo_path' => NULL),'product_id ='.$iProductId);
    }

}

?>