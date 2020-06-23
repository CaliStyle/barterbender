<?php

namespace Apps\P_AdvMarketplace\Service;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class AdvancedMarketplace extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedmarketplace');
    }

    /**
     * @return array|bool
     */
    public function getPendingSponsorItems()
    {
        $sCacheId = $this->cache()->set('advancedmarketplace_pending_sponsor');
        if(false === ($aItems = $this->cache()->get($sCacheId)))
        {
            $aRows = db()->select('m.listing_id')
                ->from(Phpfox::getT('advancedmarketplace'),'m')
                ->join(Phpfox::getT('better_ads_sponsor'),'s', 's.item_id = m.listing_id')
                ->where('m.is_sponsor = 0 AND s.is_custom = 2 AND s.module_id = "advancedmarketplace"')
                ->execute('getSlaveRows');
            $aItems = array_column($aRows, 'listing_id');
            $this->cache()->save($sCacheId, $aItems);
        }
        return $aItems;
    }

    /**
     * @param $iItemId
     * @return bool
     */
    public function canPurchaseSponsorItem($iItemId)
    {
        $aIds = $this->getPendingSponsorItems();
        return !in_array($iItemId, $aIds);
    }

    /**
     * @param $aListing
     */
    public function getPermissions(&$aListing) {
        $aListing['canEdit'] = Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing') || ($aListing['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing'));
        $aListing['canFeature'] = Phpfox::getUserParam('advancedmarketplace.can_feature_listings') && $aListing['post_status'] != 2 && (empty($aListing['has_expiry']) || ($aListing['expiry_date'] > PHPFOX_TIME));
        $aListing['canSponsorAll'] = $aListing['canSponsorOwn'] = $aListing['canSponsorInFeed'] = false;
        if(Phpfox::isAppActive('Core_BetterAds') && $aListing['post_status'] != 2 && $aListing['view_id'] == 0) {
            $aListing['canSponsorAll'] = Phpfox::getUserParam('advancedmarketplace.can_sponsor_advancedmarketplace');

            $canPurchaseSponsor = $this->canPurchaseSponsorItem($aListing['listing_id']);
            $aListing['canSponsorOwn'] = $canPurchaseSponsor && Phpfox::getUserParam('advancedmarketplace.can_purchase_sponsor') && $aListing['user_id'] == Phpfox::getUserId();

            if(Phpfox::getService('advancedmarketplace')->hasSponsorInFeedFeature($aListing)) {
                $aListing['canSponsorInFeed'] = true;
                $aListing['sponsorInFeedId'] = Phpfox::getService('feed')->canSponsoredInFeed('advancedmarketplace', $aListing['listing_id']) === true;
            }
        }
        $aListing['canApprove'] = $aListing['view_id'] == 1 && Phpfox::getUserParam('advancedmarketplace.can_approve_listings');
        $aListing['canDelete'] = Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings') || ($aListing['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_delete_own_listing'));
        $aListing['canDoPermission'] = $aListing['canEdit'] || $aListing['canFeature'] || $aListing['canSponsorAll'] ||  $aListing['canSponsorOwn'] || $aListing['canSponsorInFeed'] || $aListing['canDelete'];
    }

    /**
     * @param $listing
     */
    public function getMoreContactUserInfomation(&$listing)
    {
        $user = db()->select(Phpfox::getUserField() . ', p.photo_id as cover_photo_id, p.destination AS cover_photo_path, p.server_id AS cover_photo_server_id')
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')
            ->leftJoin(Phpfox::getT('photo'), 'p', 'p.photo_id = uf.cover_photo')
            ->where('u.user_id = ' . (int)$listing['user_id'])
            ->execute('getSlaveRow');
        if (!empty($user)) {
            $listing = array_merge($listing, $user);
        }
    }

    /**
     * @param $userId
     * @return array|int|string
     */
    public function getMyFollowerByUserId($userId)
    {
        return db()->select('*')
            ->from(Phpfox::getT('advancedmarketplace_follow'))
            ->where('user_id = ' . (int)$userId . ' and user_follow_id = ' . Phpfox::getUserId())
            ->execute('getSlaveRow');
    }

    /**
     * @param null $userId
     * @return array|int|string
     */
    public function getUserCurrentPoints($userId = null)
    {
        if (empty($userId)) {
            $userId = Phpfox::getUserId();
        }
        return db()->select('activity_points')
            ->from(Phpfox::getT('user_activity'))
            ->where('user_id = ' . (int)$userId)
            ->execute('getSlaveField');
    }

    /**
     * @param null $userId
     * @return array|int|string
     */
    public function getMyWishlistCount($userId = null)
    {

        if (empty($userId)) {
            $userId = Phpfox::getUserId();
        }

        return db()->select('COUNT(*)')
            ->from(Phpfox::getT('advancedmarketplace_wishlist'), 'aw')
            ->join($this->_sTable, 'a', 'a.listing_id = aw.listing_id')
            ->where('aw.user_id = ' . (int)$userId . ' AND aw.is_wishlist = 1 AND a.view_id = 0')
            ->execute('getSlaveField');
    }

    public function getListing($iId, $userId = null, $checkPurchase = false)
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.Service_AdvancedMarketplace_getlisting')) ? eval($sPlugin) : false);

        $userId = !empty($userId) ? $userId : Phpfox::getUserId();

        if (Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik',
                'lik.type_id = \'advancedmarketplace\' AND lik.item_id = l.listing_id AND lik.user_id = ' . $userId);
        }

        if (Phpfox::isModule('track')) {
            $this->database()->select("advancedmarketplace_track.item_id AS is_viewed, ")->leftJoin(Phpfox::getT('advancedmarketplace_track'),
                'advancedmarketplace_track',
                'advancedmarketplace_track.item_id = l.listing_id AND advancedmarketplace_track.user_id = ' . Phpfox::getUserBy('user_id'));
        }

        if($checkPurchase) {
            db()->leftJoin(Phpfox::getT('advancedmarketplace_invoice'), 'ai', 'ai.listing_id = l.listing_id AND ai.user_id = '. (int)$userId .' AND ai.status = "completed"');
        }

        $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f',
            "f.user_id = l.user_id AND f.friend_user_id = " . $userId);

        $aListing = $this->database()->select(Phpfox::getUserField() . ', mt.short_description, mt.short_description_parsed, mt.description_parsed, mt.description, l.*, ml.invite_id, ml.visited_id, uf.total_score AS total_activity_score, uf.total_rating, ua.activity_points, ' . (Phpfox::getParam('core.allow_html') ? 'mt.description_parsed' : 'mt.description') . ' AS description, ar.rate_id, ar.timestamp AS review_time_stamp, ar.rating, ar.content AS review_content, ar.user_id AS review_user_id, aw.listing_id AS is_wishlist' . ($checkPurchase ? ', ai.invoice_id AS is_purchased' : ''))
            ->from($this->_sTable, 'l')
            ->join(Phpfox::getT('advancedmarketplace_text'), 'mt', 'mt.listing_id = l.listing_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = l.user_id')
            ->join(Phpfox::getT('user_activity'), 'ua', 'ua.user_id = l.user_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_rate'), 'ar', 'ar.listing_id = l.listing_id AND ar.user_id = ' . $userId)
            ->leftJoin(Phpfox::getT('advancedmarketplace_invite'), 'ml',
                'ml.listing_id = l.listing_id AND ml.invited_user_id = ' . $userId)
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'aw', 'aw.listing_id = l.listing_id AND aw.is_wishlist = 1 AND aw.user_id = ' . $userId)
            ->where('l.listing_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aListing['listing_id'])) {
            return false;
        }

        if (!Phpfox::isModule('like')) {
            $aListing['is_liked'] = false;
        }

        if ($aListing['view_id'] == '1') {
            if ($aListing['user_id'] == $userId || Phpfox::getUserParam('advancedmarketplace.can_approve_listings')) {

            } else {
                return false;
            }
        }

        if (!empty($aListing['location']) || !empty($aListing['country_iso'])) {
            if (!empty($aListing['location'])) {
                $aLocation[] = $aListing['location'];
            }
            if (!empty($aListing['address'])) {
                $aLocation[] = $aListing['address'];
            }
            if (!empty($aListing['city'])) {
                $aLocation[] = $aListing['city'];
            }
            if (!empty($aListing['postal_code'])) {
                $aLocation[] = $aListing['postal_code'];
            }
            if (!empty($aListing['country_child_id'])) {
                $aLocation[] = Phpfox::getService('core.country')->getChild($aListing['country_child_id']);
            }
            if (!empty($aListing['country_iso'])) {
                $aLocation[] = Phpfox::getService('core.country')->getCountry($aListing['country_iso']);
            }

            $aListing['map_location'] = implode(', ', $aLocation);
            $aListing['map_location_url'] = urlencode($aListing['map_location']);
        }

        $aListing['categories'] = Phpfox::getService('advancedmarketplace.category')->getCategoriesById($aListing['listing_id']);
        $aListing['bookmark_url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
            $aListing['listing_id'], $aListing['title']);
        $aListing['category'] = Phpfox::getService('advancedmarketplace.category')->getCategoryId($aListing['listing_id']);

        if ($aListing['has_expiry'] && $aListing['expiry_date'] < PHPFOX_TIME) {
            $aListing['is_expired'] = true;
        }

        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('advancedmarketplace', $aListing['listing_id']);
            if (isset($aTags[$aListing['listing_id']])) {
                $aListing['tag_list'] = $aTags[$aListing['listing_id']];
            }
        }

        $table = Phpfox::getT('advancedmarketplace_rate');
        $averageScore = !empty($aListing['total_score']) ? $aListing['total_score'] / 2 : 0;
        list($aListing['total_rating_star'], $aListing['average_score']) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($averageScore);
        $aListing['total_review'] = !empty($aListing['total_rate']) ? $aListing['total_rate'] : 0;

        if (!empty($aListing['rate_id'])) {
            list($aListing['rating_star'], $aListing['rating']) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['rating'] / 2);
            $aListing['review_time'] = Phpfox::getLib('date')->convertTime($aListing['review_time_stamp']);
        }

        $ratingGroups = db()->select('COUNT(*) AS total_review, rating')
            ->from($table)
            ->where('listing_id = ' . (int)$iId)
            ->group('rating')
            ->execute('getSlaveRows');
        $ratingGroups = array_combine(array_column($ratingGroups, 'rating'), array_column($ratingGroups, 'total_review'));
        $charts = [];
        for ($i = 1; $i <= 5; $i++) {
            $data = [
                'rating_star' => '',
                'total_review' => 0,
            ];
            if (!empty($ratingGroups[$i * 2]) && $ratingGroups[$i * 2]['total_review'] > 0) {
                $data['total_review'] = $ratingGroups[$i * 2]['total_review'];
            }
            list($data['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($i);
            $charts[$i] = $data;
        }

        $aListing['review_chart'] = $charts;
        krsort($aListing['review_chart']);
        $this->getPermissions($aListing);
        return $aListing;
    }

    public function getForEdit($iId, $bForce = false)
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.Service_AdvancedMarketplace_getforedit')) ? eval($sPlugin) : false);

        $aListing = $this->database()->select('l.*, description, short_description')
            ->from($this->_sTable, 'l')
            ->join(Phpfox::getT('advancedmarketplace_text'), 'mt', 'mt.listing_id = l.listing_id')
            ->where('l.listing_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if ((($aListing['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing')) || ($bForce === true)) {
            $aListing['categories'] = Phpfox::getService('advancedmarketplace.category')->getCategoryIds($aListing['listing_id']);
            $aListing['category'] = Phpfox::getService('advancedmarketplace.category')->getCategoryId($aListing['listing_id']);

            if ($aListing['has_expiry']) {
                $aListing['expiry_date'] = Phpfox::getLib('date')->convertFromGmt($aListing['expiry_date'], Phpfox::getTimeZone());

                $aListing['expiry_year'] = date('Y', $aListing['expiry_date']);
                $aListing['expiry_month'] = date('n', $aListing['expiry_date']);
                $aListing['expiry_day'] = date('j', $aListing['expiry_date']);
                $aListing['expiry_hour'] = date('H', $aListing['expiry_date']);
                $aListing['expiry_minute'] = date('i', $aListing['expiry_date']);
            }
            $aListing['params'] = [
                'id' => $aListing['listing_id']
            ];

            return $aListing;
        }

        return false;
    }

    public function getInvoice($iId)
    {
        $aInvoice = $this->database()->select('mi.*, m.title, m.user_id AS advancedmarketplace_user_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('advancedmarketplace_invoice'), 'mi')
            ->join(Phpfox::getT('advancedmarketplace'), 'm', 'm.listing_id = mi.listing_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = mi.user_id')
            ->where('mi.invoice_id = ' . (int)$iId)
            ->execute('getRow');

        return (isset($aInvoice['invoice_id']) ? $aInvoice : false);
    }

    public function getInvoices($aCond, $bGroupUser = false, $iPage = 1, $iPageSize = 10)
    {
        if ($bGroupUser) {
            $this->database()->group('mi.user_id');
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('advancedmarketplace'), 't')
            ->join(Phpfox::getT('advancedmarketplace_invoice'), 'mi', 't.listing_id = mi.listing_id')
            ->where($aCond)
            ->execute('getSlaveField');

        if ($bGroupUser) {
            $this->database()->group('mi.user_id');
        }

        $aRows = $this->database()->select('mi.*, t.title, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('advancedmarketplace'), 't')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = t.user_id')
            ->join(Phpfox::getT('advancedmarketplace_invoice'), 'mi', 't.listing_id = mi.listing_id')
            ->where($aCond)
            ->order('mi.time_stamp DESC')
            ->limit($iPage, $iPageSize)
            ->execute('getSlaveRows');

        foreach ($aRows as $iKey => $aRow) {
            switch ($aRow['status']) {
                case 'completed':
                    $aRows[$iKey]['status_phrase'] = _p('advancedmarketplace.paid');
                    break;
                case 'cancel':
                    $aRows[$iKey]['status_phrase'] = _p('advancedmarketplace.cancelled');
                    break;
                case 'pending':
                    $aRows[$iKey]['status_phrase'] = _p('advancedmarketplace.pending_payment');
                    break;
                default:
                    $aRows[$iKey]['status_phrase'] = _p('advancedmarketplace_pending');
                    break;
            }
            $aRows[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aRow['price'], $aRow['currency_id']);
            $aRows[$iKey]['link'] = Phpfox::permalink('advancedmarketplace.detail',$aRow['listing_id'], $aRow['title']);
        }

        return array($iCnt, $aRows);
    }

    public function getForProfileBlock($iUserId, $iLimit = 5)
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.Service_AdvancedMarketplace_getforprofileblock')) ? eval($sPlugin) : false);

        return $this->database()->select('m.*')
            ->from($this->_sTable, 'm')
            ->where('m.view_id = 0 AND m.group_id = 0 AND m.user_id = ' . (int)$iUserId)
            ->limit($iLimit)
            ->order('m.time_stamp DESC')
            ->execute('getSlaveRows');
    }

    public function getImages($iId, $iLimit = null)
    {
        return $this->database()->select('image_id, image_path, server_id')
            ->from(Phpfox::getT('advancedmarketplace_image'))
            ->where('listing_id = ' . (int)$iId)
            ->order('ordering ASC')
            ->limit($iLimit)
            ->execute('getSlaveRows');
    }

    public function getSponsorListings($iLimit = 0)
    {
        $sCacheId = $this->cache()->set('advancedmarketplace_sponsored');

        if (!($aListing = $this->cache()->get($sCacheId))) {
            $aListing = $this->database()->select('m.*, mt.*, s.sponsor_id, m.title, m.currency_id, m.price, m.time_stamp, m.image_path, m.server_id, ' . phpfox::getUserField() . ', wl.listing_id AS is_wishlist')
                ->from($this->_sTable, 'm')
                ->join(Phpfox::getT('better_ads_sponsor'), 's', 's.item_id = m.listing_id AND s.module_id = "advancedmarketplace" AND s.is_active = 1 AND s.is_custom = 3')
                ->join(Phpfox::getT('advancedmarketplace_text'), 'mt', 'mt.listing_id = m.listing_id')
                ->join(phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
                ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = m.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
                ->where('m.view_id = 0 AND m.privacy = 0 AND m.group_id = 0 AND m.is_sponsor = 1 AND m.post_status != 2')
                ->order(" rand() ")
                ->execute('getSlaveRows');

            $this->cache()->save($sCacheId, $aListing);
        } else {
            if (is_array($aListing)) {
                foreach ($aListing as $iKey => $aRow) {
                    if ($aRow['has_expiry'] && ($aRow['expiry_date'] <= PHPFOX_TIME)) {
                        unset($aListing[$iKey]);
                    }
                }
            }
        }

        if ($aListing === true || (is_array($aListing) && !count($aListing))) {
            return array();
        }

        shuffle($aListing);
        $aOut = array_slice($aListing, 0, $iLimit);
        $urlObject = \Phpfox_Url::instance();
        foreach ($aOut as $iKey => $aItem) {
            $aOut[$iKey]['url'] = $urlObject->makeUrl('ad.sponsor', ['view' => $aItem['sponsor_id']]);
            $aOut[$iKey]['average_score'] = !empty($aItem['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aItem['total_score'] / 2, true) : 0;
            list($aOut[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aOut[$iKey]['average_score']);
            $aOut[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aItem['price'], $aItem['currency_id']);
        }

        return $aOut;
    }

    public function getInvites($iListing, $iType, $iPage = 0, $iPageSize = 8)
    {
        $aInvites = array();
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('advancedmarketplace_invite'))
            ->where('listing_id = ' . (int)$iListing . ' AND visited_id = ' . (int)$iType)
            ->execute('getSlaveField');

        if ($iCnt) {
            $aInvites = $this->database()->select('ei.*, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('advancedmarketplace_invite'), 'ei')
                ->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ei.invited_user_id')
                ->where('ei.listing_id = ' . (int)$iListing . ' AND ei.visited_id = ' . (int)$iType)
                ->limit($iPage, $iPageSize, $iCnt)
                ->order('ei.invite_id DESC')
                ->execute('getSlaveRows');
        }

        return array($iCnt, $aInvites);
    }

    public function getUserListings($iListingId, $iUserId)
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.Service_AdvancedMarketplace_getuserlistings_count')) ? eval($sPlugin) : false);

        $iCnt = $this->database()->select('COUNT(*)')
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.listing_id != ' . (int)$iListingId . ' AND v.view_id = 0 AND v.user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');

        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.Service_AdvancedMarketplace_getuserlistings_query')) ? eval($sPlugin) : false);

        $aRows = $this->database()->select('v.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.listing_id != ' . (int)$iListingId . ' AND v.view_id = 0 AND v.user_id = ' . (int)$iUserId)
            ->limit(Phpfox::getParam('advancedmarketplace.total_listing_more_from'))
            ->order('v.time_stamp DESC')
            ->execute('getSlaveRows');

        return array($iCnt, $aRows);
    }

    public function getPendingTotal()
    {
        $sCond = 'view_id = 1 AND post_status != 2 AND (has_expiry = 0 OR expiry_date > ' . PHPFOX_TIME . ')';

        return $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($sCond)
            ->execute('getSlaveField');
    }

    public function isAlreadyInvited($iItemId, $aFriends)
    {

        if ((int)$iItemId === 0) {
            return false;
        }

        if (is_array($aFriends)) {
            if (!count($aFriends)) {
                return false;
            }

            $sIds = [];
            foreach ($aFriends as $aFriend) {
                if (!isset($aFriend['user_id'])) {
                    continue;
                }

                $sIds[] = $aFriend['user_id'];
            }

            $aInvites = $this->database()->select('invite_id, visited_id, invited_user_id')
                ->from(Phpfox::getT('advancedmarketplace_invite'))
                ->where('listing_id = ' . (int)$iItemId . ' AND invited_user_id IN(' . implode(', ', $sIds) . ')')
                ->execute('getSlaveRows');

            $aCache = array();
            foreach ($aInvites as $aInvite) {
                $aCache[$aInvite['invited_user_id']] = ($aInvite['visited_id'] ? _p('visited') : _p('invited'));
            }

            if (count($aCache)) {
                return $aCache;
            }
        }

        return false;
    }

    public function getFeatured($iLimit = 0)
    {
        $sCond = 'm.view_id = 0 AND m.privacy = 0 AND m.is_featured = 1 AND (has_expiry = 0 OR expiry_date > ' . PHPFOX_TIME . ')';

        if ($iLimit) {
            $this->database()->limit($iLimit);
        }

        $aRows = $this->database()->select('m.*, m.country_iso AS item_country_iso, ' . Phpfox::getUserField() . ', wl.listing_id AS is_wishlist, t.*')
            ->from(Phpfox::getT('advancedmarketplace'), 'm')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->join(phpfox::getT('advancedmarketplace_text'), 't', 't.listing_id = m.listing_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = m.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->where($sCond)
            ->execute('getSlaveRows');

        if (!is_array($aRows)) {
            return array();
        }

        shuffle($aRows);
        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['images'] = $this->getImages($aRow['listing_id'], 5);
            $aRows[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aRow['listing_id'],
                $aRow['title']);
            $aRows[$iKey]['average_score'] = !empty($aRow['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aRow['total_score'] / 2, true) : 0;
            list($aRows[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aRows[$iKey]['average_score']);
            $aRows[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aRow['price'], $aRow['currency_id']);
            $aRows[$iKey]['country_iso'] = $aRow['item_country_iso'];
        }
        return $aRows;
    }

    public function getTotalInvites()
    {
        static $iCnt = null;

        if ($iCnt !== null) {
            return $iCnt;
        }

        $sCond = 'm.view_id = 0 AND mi.visited_id = 0 AND mi.invited_user_id = ' . Phpfox::getUserId() . ' AND (has_expiry = 0 OR expiry_date > ' . PHPFOX_TIME . ')';

        $iCnt = (int)$this->database()->select('COUNT(m.listing_id)')
            ->from(Phpfox::getT('advancedmarketplace_invite'), 'mi')
            ->join(Phpfox::getT('advancedmarketplace'), 'm', 'm.listing_id = mi.listing_id')
            ->where($sCond)
            ->execute('getSlaveField');

        return $iCnt;
    }

    public function getUserInvites($iLimit = 5)
    {
        $iCnt = $this->getTotalInvites();

        $sCond = 'm.view_id = 0 AND mi.visited_id = 0 AND mi.invited_user_id = ' . Phpfox::getUserId() . ' AND (has_expiry = 0 OR expiry_date > ' . PHPFOX_TIME . ')';

        $aRows = $this->database()->select('m.*, m.country_iso as sCountry, ' . phpfox::getUserField())
            ->from(Phpfox::getT('advancedmarketplace_invite'), 'mi')
            ->join(Phpfox::getT('advancedmarketplace'), 'm', 'm.listing_id = mi.listing_id')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where($sCond)
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if ($iCnt > 0) {
            foreach ($aRows as $iKey => $aListing) {
                $aRows[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                    $aListing['listing_id'], $aListing['title']);
                $fAVGRating = Phpfox::getLib("database")
                    ->select("AVG(rating)")
                    ->from(Phpfox::getT("advancedmarketplace_rate"))
                    ->where(sprintf("listing_id = %d", $aListing['listing_id']))
                    ->execute("getSlaveField");

                $iRatingCount = Phpfox::getLib("database")
                    ->select("count(*)")
                    ->from(Phpfox::getT("advancedmarketplace_rate"))
                    ->where(sprintf("listing_id = %d", $aListing['listing_id']))
                    ->execute("getSlaveField");

                $aRows[$iKey]['rating'] = $fAVGRating;
                $aRows[$iKey]['rating_count'] = $iRatingCount;
            }
        }

        return array($iCnt, $aRows);
    }

    public function proccessImageName($imgName, $strAppend)
    {
        return sprintf($imgName, $strAppend);
    }

    public function getTagCloud()
    {
        $sCond = 't.category_id = "advancedmarketplace" and am.view_id = 0 and am.post_status != 2 AND (has_expiry = 0 OR expiry_date > ' . PHPFOX_TIME . ')';

        $aRows = phpfox::getLib('database')->select('t.category_id, t.tag_text AS tag, t.tag_url, COUNT(t.item_id) AS total')
            ->from(phpfox::getT('tag'), 't')
            ->join(phpfox::getT('advancedmarketplace'), 'am', 'am.listing_id = t.item_id')
            ->where($sCond)
            ->group('tag_text, tag_url')
            ->having('total > ' . (int)Phpfox::getParam('tag.tag_min_display'))
            ->order('total DESC')
            ->limit(Phpfox::getParam('tag.tag_trend_total_display'))
            ->execute('getSlaveRows');
        foreach ($aRows as $aRow) {
            $aTempTags[] = array
            (
                'value' => $aRow['total'],
                'key' => $aRow['tag'],
                'url' => $aRow['tag_url'],
                'link' => Phpfox::getLib('url')->makeUrl('advancedmarketplace.search.', array('tag', $aRow['tag_url']))
            );
        }
        if (empty($aTempTags)) {
            return array();
        }

        return $aTempTags;
    }

    public function getListings($aConds = array(), $sSort = 'l.time_stamp desc', $iPage = 0, $iLimit = 0)
    {
        $sCond = '';
        if (!empty($aConds)) {
            foreach ($aConds as $c) {
                if (empty($sCond)) {
                    $sCond = $c;
                } else {
                    $sCond .= ' AND ' . $c;
                }
            }
        }
        $iQuery = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where($sCond);
        if (isset($_POST['category_id'])) {
            $iQuery->innerjoin(phpfox::getT('advancedmarketplace_category_data'), 'cd', 'cd.listing_id = l.listing_id');
            //$oQuery->innerjoin(phpfox::getT('advancedmarketplace_category_data'), 'cd', 'cd.listing_id = l.listing_id');
        }
        //->group('l.listing_id')
        $iCnt = $iQuery->execute('getField');

        $oQuery = $this->database()->select('l.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(phpfox::getT('advancedmarketplace_category_data'), 'cd', 'cd.listing_id = l.listing_id')
            ->leftjoin(phpfox::getT('advancedmarketplace_today_listing'), 'td', 'td.listing_id = l.listing_id')
            ->where($sCond)
            ->group('l.listing_id');

        $oQuery->order('l.time_stamp desc');
        if ($iLimit > 0) {
            $oQuery->limit($iPage, $iLimit, $iCnt);
        }

        $aListings = $oQuery->execute('getRows');

        if (!empty($aListings)) {

            foreach ($aListings as $iKey => $aListing) {

                /*
                $sCategoryIds = phpfox::getService('advancedmarketplace.category')->getCategoryIds($aListing['listing_id']);
                                $aCategoryIds = explode(',', $sCategoryIds);
                                $iChildCat = $aCategoryIds[0];
                                foreach($aCategoryIds as $aCat)
                                {
                                    $iCat = phpfox::getService('advancedmarketplace.category')->getChildIds($aCat);
                                    if(empty($iCat))
                                    {
                                        $iChildCat = $aCat;
                                    }
                                }
                                $aCat = phpfox::getService('advancedmarketplace.category')->getForEdit($iChildCat);
                                $aListings[$iKey]['category'] = $aCat['name'];*/

                $aListings[$iKey]['categories'] = Phpfox::getService('advancedmarketplace.category')->getCategoriesById($aListing['listing_id']);
                $aListings[$iKey]['time_stamp'] = phpfox::getTime(Phpfox::getParam('advancedmarketplace.advancedmarketplace_view_time_stamp'),
                    $aListing['time_stamp']);
            }

        }

        return array($iCnt, $aListings);

    }

    public function getTodayListings($aConds = array(), $sSort = 'listing_id desc', $iPage = 0, $iLimit = 0)
    {
        $sCond = '';
        if (!empty($aConds)) {
            foreach ($aConds as $c) {
                if (empty($sCond)) {
                    $sCond = $c;
                } else {
                    $sCond .= ' AND ' . $c;
                }
            }
        }
        $iQuery = phpfox::getLib('database')->select('count(DISTINCT td.listing_id)')
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(phpfox::getT('advancedmarketplace_today_listing'), 'td', 'td.listing_id = l.listing_id')
            ->where($sCond)
            ->group('l.listing_id');
        if (isset($_POST['category_id'])) {
            $iQuery->innerjoin(phpfox::getT('advancedmarketplace_category_data'), 'cd', 'cd.listing_id = l.listing_id');
        }
        $iCnt = $iQuery->execute('getField');

        $oQuery = $this->database()->select('l.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(phpfox::getT('advancedmarketplace_category_data'), 'cd', 'cd.listing_id = l.listing_id')
            ->join(phpfox::getT('advancedmarketplace_today_listing'), 'td', 'td.listing_id = l.listing_id')
            ->where($sCond)
            ->group('l.listing_id')
            ->order($sSort);

        if ($iLimit > 0) {
            $oQuery->limit($iPage, $iLimit, $iCnt);
        }

        $aListings = $oQuery->execute('getRows');

        if (!empty($aListings)) {
            $listingIds = array_column($aListings,'listing_id');

            $statistic = db()->select('MIN(time_stamp) AS most_recent_date, listing_id, COUNT(today_listing_id) AS total_date')
                        ->from(Phpfox::getT('advancedmarketplace_today_listing'))
                        ->where('listing_id IN ('. implode(',', $listingIds) . ') AND time_stamp > '. PHPFOX_TIME)
                        ->group('listing_id')
                        ->execute('getSlaveRows');
            $totalDates = array_combine(array_column($statistic,'listing_id'), array_column($statistic,'total_date'));
            $mostRecentDates = array_combine(array_column($statistic,'listing_id'), array_column($statistic,'most_recent_date'));

            foreach ($aListings as $iKey => $aListing) {
                $aListings[$iKey]['categories'] = Phpfox::getService('advancedmarketplace.category')->getCategoriesById($aListing['listing_id']);
                $aListings[$iKey]['time_stamp'] = phpfox::getTime(Phpfox::getParam('advancedmarketplace.advancedmarketplace_view_time_stamp'),
                    $aListing['time_stamp']);
                $aListings[$iKey]['total_dates'] = !empty($totalDates[$aListing['listing_id']]) ? $totalDates[$aListing['listing_id']] : null;
                $aListings[$iKey]['most_recent_date'] = !empty($mostRecentDates[$aListing['listing_id']]) ? Phpfox::getTime('m/d/Y',$mostRecentDates[$aListing['listing_id']]) : null;
            }

        }

        return array($iCnt, $aListings);

    }

    public function getTodayListing($iListingId)
    {
        $aRows = $this->database()->select('today_listing_id, listing_id, (time_stamp * 1000) as `time_stamp`')
            ->from(Phpfox::getT('advancedmarketplace_today_listing'))
            ->where(sprintf("listing_id=%d AND time_stamp > (86400 * 31)", $iListingId))
            ->order('time_stamp ASC')
            ->execute('getSlaveRows');
        return ($aRows);
    }

    public function getTagText($listingId)
    {
        $aTags = $this->database()->select('tag_text')
            ->from(Phpfox::getT('tag'))
            ->where('item_id =' . (int)$listingId)
            ->execute('getSlaveRows');

        return $aTags;
    }

    public function getSimilarListings($iLimit, $tagText, $iListingId, $sideBlocks = true)
    {
        if(empty($tagText) || !is_array($tagText)) {
            return false;
        }
        $select = 't.*, wl.listing_id AS is_wishlist';
        if(!$sideBlocks) {
            $select .= ', '. Phpfox::getUserField();
            db()->join(Phpfox::getT('user'), 'u', 'u.user_id = t.user_id');
        }

        $aRows = $this->database()->select($select)
            ->from($this->_sTable, 't')
            ->join(Phpfox::getT('tag'), 'tag', 'tag.item_id = t.listing_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = t.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->where('t.view_id = 0 AND t.post_status != 2 AND tag.tag_text IN ("'. implode('","',$tagText) .'") AND tag.item_id <>' . (int)$iListingId)
            ->order('t.time_stamp DESC')
            ->group('t.listing_id')
            ->limit($iLimit)
            ->execute('getSlaveRows');


        $this->_addCategoryUrl($aRows);
        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['average_score'] = !empty($aRow['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aRow['total_score'] / 2, true) : 0;
            list($aRows[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aRows[$iKey]['average_score']);
            $aRows[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aRow['price'], $aRow['currency_id']);
        }

        return $aRows;
    }


    public function getListingStatistics()
    {
        $aListingStatistics = array();
        $iTotalListings = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'))
            ->execute('getSlaveField');

        $iApprovedListings = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'))
            ->where('(post_status = 1 OR post_status = 2) and view_id = 0')
            ->execute('getSlaveField');

        $iDraftListings = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'))
            ->where('post_status = 2')
            ->execute('getSlaveField');

        $iFeaturedListings = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'))
            ->where('is_featured = 1')
            ->execute('getSlaveField');

        $iSponsoredListings = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'))
            ->where('is_sponsor = 1')
            ->execute('getSlaveField');

        $iAvailableListings = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'))
            ->where('post_status != 2 and (view_id = 0 OR view_id = 2) ')
            ->execute('getSlaveField');

        $iClosedListings = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'))
            ->where('view_id = 2')
            ->execute('getSlaveField');

        $iTotalReviews = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace_rate'))
            ->execute('getSlaveField');

        $iTotalReviewListings = phpfox::getLib('database')->select('count(DISTINCT listing_id)')
            ->from(phpfox::getT('advancedmarketplace_rate'))
            ->execute('getSlaveField');

        $aListingStatistics['total_listings'] = $iTotalListings;
        $aListingStatistics['approved_listings'] = $iApprovedListings;
        $aListingStatistics['draft_listings'] = $iDraftListings;
        $aListingStatistics['featured_listings'] = $iFeaturedListings;
        $aListingStatistics['sponsored_listings'] = $iSponsoredListings;
        $aListingStatistics['available_listings'] = $iAvailableListings;
        $aListingStatistics['closed_listings'] = $iClosedListings;
        $aListingStatistics['total_reviews'] = $iTotalReviews;
        $aListingStatistics['total_reviewed_listings'] = $iTotalReviewListings;

        return $aListingStatistics;
    }

    public function getReviewsListing($iId, $iPage, $iPageSize)
    {

        $aReviews = phpfox::getLib('database')->select('r.*,' . phpfox::getUserField())
            ->from(phpfox::getT('advancedmarketplace_rate'), 'r')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = r.user_id')
            ->where('listing_id = ' . $iId . ' and l.post_status != 2 and l.view_id = 0')
            ->limit($iPage, $iPageSize, 2)
            ->execute('getSlaveRows');

        return $aReviews;
    }

    public function getCountReviewsListing($iId)
    {
        return phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace_rate'))
            ->where('listing_id = ' . $iId)
            ->execute('getSlaveField');
    }

    public function loadSubcatByParentID($iParentCatId)
    {
        return Phpfox::getLib("database")
            ->select("*")
            ->from(Phpfox::getT('advancedmarketplace_category'), "p")
            ->where(sprintf("p.parent_id = %d", $iParentCatId))
            ->execute("getSlaveRows");
    }

    public function frontend_getRecentListings($aConds = array(), $sSort = 'listing_id desc', $iPage = 0, $iLimit = 0)
    {
        $sCond = '';
        if (!empty($aConds)) {
            foreach ($aConds as $c) {
                if (empty($sCond)) {
                    $sCond = $c;
                } else {
                    $sCond .= ' AND ' . $c;
                }
            }
        }

        $iCnt = phpfox::getLib('database')->select('count(*)')
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where($sCond)
            ->execute('getField');

        // filter u.country_iso
        $aUserFields = explode(', ', Phpfox::getUserField());
        if (($key = array_search('u.country_iso', $aUserFields)) !== false) {
            unset($aUserFields[$key]);
        }
        $sUserFields = implode(', ', $aUserFields);

        $oQuery = $this->database()->select('l.title, l.listing_id, l.image_path, l.server_id, l.price, l.country_iso, l.is_sponsor, l.is_featured, l.time_stamp, l.post_status, l.currency_id, l.total_rate, l.total_score, t.description, t.short_description, ' . $sUserFields . ', wl.listing_id AS is_wishlist')
            ->from(Phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(phpfox::getT('advancedmarketplace_text'), 't', 't.listing_id = l.listing_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = l.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->order($sSort);

        //privacy
        $where = 'l.post_status != 2 and l.privacy = 0 and l.view_id = 0 AND (has_expiry = 0 OR expiry_date > ' . PHPFOX_TIME . ')';

        if ($iLimit) {
            $oQuery->limit($iLimit);
        }

        $aListings = $oQuery->where($where)->execute('getRows');

        if (!empty($aListings) && is_array($aListings)) {
            foreach ($aListings as $iKey => $aListing) {
                if ($aListing['has_expiry'] && $aListing['expiry_date'] > PHPFOX_TIME) {
                    $aRows[$iKey]['is_expired'] = true;
                }
                $aListings[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                    $aListing['listing_id'], $aListing['title']);
                $aListings[$iKey]['average_score'] = !empty($aListing['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['total_score'] / 2, true) : 0;
                list($aListings[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListings[$iKey]['average_score']);
                $aListings[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
            }
        }

        return array($iCnt, $aListings);
    }

    public function frontend_getListings($aConds = array(), $sSort = 'listing_id desc', $iPage = 0, $iLimit = 0)
    {
        $sCond = '';

        if (!empty($aConds)) {
            foreach ($aConds as $c) {
                if (empty($sCond)) {
                    $sCond = $c;
                } else {
                    $sCond .= ' AND ' . $c;
                }
            }
        }

        // privacy
        if (isset($sCond)) {
            $sCond .= ' AND ';
        }
        $sCond .= 'l.post_status != 2 and l.privacy = 0 and l.view_id = 0 AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')';

        $iCnt = $this->database()->select("count(*)")
            ->from(Phpfox::getT('advancedmarketplace'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(phpfox::getT('advancedmarketplace_text'), 't', 't.listing_id = l.listing_id')
            ->where($sCond . ' AND l.total_view > 0')
            ->executeField();

        $oQuery = $this->database()->select('l.title, l.listing_id, l.image_path, l.server_id, l.price, l.country_iso, l.is_sponsor, l.is_featured, l.currency_id, l.total_view, l.total_like, l.total_comment, l.time_stamp, l.total_score, l.total_rate, l.post_status,t.description, t.short_description, ' . Phpfox::getUserField() . ', wl.listing_id AS is_wishlist')
            ->from(Phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(phpfox::getT('advancedmarketplace_text'), 't', 't.listing_id = l.listing_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = l.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->where($sCond)
            ->order($sSort);


        if ($iLimit) {
            $oQuery->limit($iLimit);
        }

        $aListings = $oQuery->execute('getRows');

        if (!empty($aListings) && is_array($aListings)) {
            foreach ($aListings as $iKey => $aListing) {
                $aListings[$iKey]['average_score'] = !empty($aListing['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['total_score'] / 2, true) : 0;
                list($aListings[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListings[$iKey]['average_score']);
                $aListings[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
            }
        }

        $this->_addCategoryUrl($aListings);

        return array($iCnt, $aListings);
    }

    public function frontend_getRecentViewListings($iUserId = null, $sOrderBy = null, $sLimit = null)
    {
        $sCond = 'rw.user_id = ' . phpfox::getUserId() . ' AND l.post_status != 2 AND l.view_id = 0 AND l.privacy = 0 AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')';

        $iCnt = $this->database()->select("count(*)")
            ->from(Phpfox::getT('advancedmarketplace'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(Phpfox::getT('advancedmarketplace_recent_view'), "rw", "l.listing_id = rw.listing_id")
            ->where($sCond)
            ->executeField();

        if ($sLimit) {
            $this->database()->limit($sLimit);
        }

        $aListings = $this->database()->select("l.title, l.listing_id, l.image_path, l.server_id, l.price, l.country_iso, l.total_view, l.time_stamp, l.total_rate, l.total_score, l.currency_id, l.post_status, l.is_featured, l.is_sponsor, " . Phpfox::getUserField() . ', wl.listing_id AS is_wishlist')
            ->from(Phpfox::getT('advancedmarketplace'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(Phpfox::getT('advancedmarketplace_recent_view'), "rw", "l.listing_id = rw.listing_id")
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = l.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->where($sCond)
            ->order("rw.timestamp DESC")
            ->execute("getRows");

        if (!empty($aListings) && is_array($aListings)) {
            foreach ($aListings as $iKey => $aListing) {
                $aListings[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                    $aListing['listing_id'], $aListing['title']);
                $aListings[$iKey]['average_score'] = !empty($aListing['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['total_score'] / 2, true) : 0;
                list($aReturn[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListings[$iKey]['average_score']);
                $aListings[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
            }
        }

        return array($iCnt, $aListings);
    }

    public function frontend_getTodayListings($iUserId = null, $sOrderBy = null, $sLimit = null)
    {
        $oQuery = $this->database()
            ->select('l.title, l.listing_id, l.image_path, l.server_id, l.price, l.country_iso, l.total_score, l.total_rate, l.currency_id, l.post_status, l.is_featured, l.is_sponsor ,' . phpfox::getUserField() . ', tdl.time_stamp, wl.listing_id AS is_wishlist')
            ->from(Phpfox::getT("advancedmarketplace"), "l")
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(Phpfox::getT('advancedmarketplace_today_listing'), 'tdl', 'tdl.listing_id = l.listing_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = l.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->where('l.post_status != 2 and l.view_id = 0 and l.privacy = 0  AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')');

        if ($sOrderBy !== null) {
            $oQuery->order(" rand() " . $sOrderBy);
        } else {
            $oQuery->order(" rand() ");
        }

        $aListings = $oQuery->execute("getRows");
        $aReturns = [];
        if (!empty($aListings) && is_array($aListings)) {
            date_default_timezone_set('UTC');
            foreach ($aListings as $iKey => $aListing) {
                if ($sLimit && count($aReturns) == $sLimit) {
                    break;
                }
                if (date('Ymd') == date('Ymd', $aListing['time_stamp'])) {
                    if (!in_array($aListing['listing_id'], array_column($aReturns, 'listing_id'))) {
                        $aListing['average_score'] = !empty($aListing['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['total_score'] / 2, true) : 0;
                        list($aListing['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['average_score']);
                        $aListing['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
                        $aListing['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                            $aListing['listing_id'], $aListing['title']);
                        $aReturns[] = $aListing;
                    }
                    continue;
                }
            }
        }

        return $aReturns;
    }

    public function frontend_getListingReview($iListingId = null, $iLimit = null, $iPage = 0)
    {
        $oQueryCount = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT("advancedmarketplace_rate"), "p")
            ->join(Phpfox::getT("advancedmarketplace"), "l", "l.listing_id = p.listing_id")
            ->join(Phpfox::getT("user"), "u", "p.user_id = u.user_id");

        if ($iListingId !== null) {
            $oQueryCount->where(sprintf("l.listing_id = %d", $iListingId));
        }

        $iCount = $oQueryCount->execute('getSlaveField');

        $oQuery = Phpfox::getLib("database")
            ->select("p.*, u.*")
            ->from(Phpfox::getT("advancedmarketplace_rate"), "p")
            ->join(Phpfox::getT("advancedmarketplace"), "l", "l.listing_id = p.listing_id")
            ->join(Phpfox::getT("user"), "u", "p.user_id = u.user_id")
            ->order('p.timestamp DESC');
        if ($iListingId !== null) {
            $oQuery->where(sprintf("l.listing_id = %d", $iListingId));
        }

        return array($iCount, $oQuery->execute("getRows"));
    }

    //nhanlt
    public function backend_getcustomfieldinfos()
    {
        return array(
            /* _p */
            ("advancedmarketplace.text_line") => array(
                "tag" => "<input type=\"text\" name=\"jh_#%name%#_\" value=\"jh_#%text%#_\" id=\"jh_#%id%#_\" class=\"jh_#%class%#_\" jh_#%custom_attribute%#_ />",
                "sub_tags" => null,
            ),
            /* _p */
            ("advancedmarketplace.combo_box") => array(
                "tag" => "<select type=\"\" name=\"jh_#%name%#_\" value=\"jh_#%value%#_\" id=\"jh_#%id%#_\" class=\"jh_#%class%#_\ jh_#%custom_attribute%#_>jh_#%sub_tags%#_</select>",
                "sub_tags" => "<option value=\"jh_#%value%#_\">jh_#%text%#_</options>",
            ),
            /* _p */
            ("advancedmarketplace.select_radio") => array(
                "tag" => "<label for=\"jh_#%id%#_\"><input type=\"radio\" name=\"jh_#%name_multi%#_\" value=\"jh_#%value%#_\" id=\"jh_#%id%#_\" class=\"jh_#%class%#_\ jh_#%custom_attribute%#_ />jh_#%value%#_</label>",
                "sub_tags" => "<label>jh_#%text%#_: <input type=\"radio\" value=\"jh_#%value%#_\" name=\"jh_#%name_multi%#_\"></label>",
            ),
            /* _p */
            ("advancedmarketplace.select_checkbox") => array(
                "tag" => "<label for=\"jh_#%id%#_\"><input type=\"checkbox\" name=\"jh_#%name%#_\" value=\"jh_#%value%#_\" id=\"jh_#%id%#_\" class=\"jh_#%class%#_\" jh_#%custom_attribute%#_ />jh_#%text%#_</label>",
                "sub_tags" => null,
            ),
        );
    }


    public function frontend_getInterestedListings($iId, $iLimit = 0)
    {
        $aCategories = phpfox::getLib('database')->select('cd.category_id')
            ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
            ->where('cd.listing_id = ' . $iId)
            ->execute('getSlaveRows');
        $sCategories = '';
        foreach ($aCategories as $iKey => $aCategory) {
            $sCategories .= $aCategory['category_id'] . ',';
        }
        $iCatId = phpfox::getService('advancedmarketplace.category')->getChildIdsOfCats($aCategories);

        if (empty($iCatId)) {
            $iCatId['category_id'] = 0;

            return array(null, null);
        }
        $iCnt = phpfox::getLib('database')->select('count(cd.category_id)')
            ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
            ->where('cd.listing_id =' . $iId)
            ->execute('getSlaveField');
        $aListingIds = phpfox::getLib('database')->select('cd.listing_id')
            ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
            ->where('cd.category_id = ' . $iCatId['category_id'] . ' and cd.listing_id != ' . $iId)
            ->execute('getRows');
        $sListingIds = '';

        foreach ($aListingIds as $iKey => $aId) {
            $sListingIds .= $aId['listing_id'] . ',';
        }
        if (empty($sListingIds)) {
            return array(null, null);
        }

        $iCount = 0;
        $sListingIds = substr($sListingIds, 0, strlen($sListingIds) - 1);

        $sCond = 'cd.listing_id in (' . $sListingIds . ') and l.post_status != 2 and l.view_id = 0 and l.privacy = 0 AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')';

        db()->select('count(cd.listing_id) as iCnt')
            ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
            ->join(phpfox::getT('advancedmarketplace'), 'l', 'l.listing_id = cd.listing_id')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where($sCond)
            ->group('cd.listing_id')
            ->having('count(cd.listing_id) = ' . $iCnt);

        if ($iLimit) {
            db()->limit($iLimit);
        }

        $aCounts = db()->executeRows();
        foreach ($aCounts as $aCount) {
            $iCount += $aCount['iCnt'];
        }

        db()->select('l.*, ' . phpfox::getUserField() . ', wl.listing_id AS is_wishlist')
            ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
            ->join(phpfox::getT('advancedmarketplace'), 'l', 'l.listing_id = cd.listing_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = l.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where($sCond)
            ->limit(phpfox::getParam('advancedmarketplace.total_listing_more_from'))
            ->group('cd.listing_id')
            ->having('count(cd.listing_id) = ' . $iCnt);

        if ($iLimit) {
            db()->limit($iLimit);
        }

        $aListings = db()->execute('getRows');
        $this->_addCategoryUrl($aListings);
        foreach ($aListings as $iKey => $aListing) {
            $aListings[$iKey]['average_score'] = !empty($aListing['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['total_score'] / 2, true) : 0;
            list($aListings[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListings[$iKey]['average_score']);
            $aListings[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
        }
        return array($iCount, $aListings);
    }

    public function frontend_getSellerListings($iId, $iUserId, $iLimit = 0)
    {
        $sCond = 'l.user_id = ' . $iUserId . ' and l.listing_id != ' . $iId . ' and l.post_status != 2 and l.view_id = 0 AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')';

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->where($sCond)
            ->execute('getSlaveField');

        if ($iLimit) {
            db()->limit($iLimit);
        }

        $aListings = db()->select('l.*, ' . phpfox::getUserField() . ', wl.listing_id AS is_wishlist')
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = l.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->where($sCond)
            ->group('l.listing_id')
            ->execute('getRows');

        $this->_addCategoryUrl($aListings);
        foreach ($aListings as $iKey => $aListing) {
            $aListings[$iKey]['average_score'] = !empty($aListing['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['total_score'] / 2, true) : 0;
            list($aListings[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListings[$iKey]['average_score']);
            $aListings[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
        }
        return array($iCnt, $aListings);
    }

    public function getImagesOfListing($iId)
    {
        $aImage = phpfox::getLib('database')->select('image_path, server_id')
            ->from(phpfox::getT('advancedmarketplace_image'))
            ->where('listing_id = ' . $iId)
            ->execute('getRows');

        return $aImage;
    }

    public function getMostReviewedListing($iLimit = 0)
    {
        $sCond = 'l.post_status != 2 and l.view_id = 0 and l.privacy = 0 and l.total_rate > 0 AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')';

        $iCnt = db()->select('count(*)')
            ->from(':advancedmarketplace', 'l')
            ->where($sCond)
            ->executeField();

        if ($iLimit) {
            db()->limit($iLimit);
        }

        $aListings = db()->select('l.*,' . Phpfox::getUserField() . ', wl.listing_id AS is_wishlist')
            ->from(Phpfox::getT('advancedmarketplace'), 'l')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'wl', 'wl.listing_id = l.listing_id AND wl.is_wishlist = 1 AND wl.user_id = ' . Phpfox::getUserId())
            ->where($sCond)
            ->group('l.listing_id')
            ->order('l.total_rate desc, l.total_score desc')
            ->execute('getRows');
        if (!empty($aListings) && is_array($aListings)) {
            foreach ($aListings as $iKey => $aListing) {
                $aListings[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                    $aListing['listing_id'], $aListing['title']);
                $aListings[$iKey]['average_score'] = !empty($aListing['total_score']) ? Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListing['total_score'] / 2, true) : 0;
                list($aListings[$iKey]['rating_star'],) = Phpfox::getService('advancedmarketplace.rate')->parseRatingStar($aListings[$iKey]['average_score']);
                $aListings[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
            }
        }

        return array($iCnt, $aListings);
    }

    public function frontend_getFeatureListings($iLimit)
    {
        $sCond = 'l.is_featured = 1 and l.post_status != 2 and l.privacy = 0 and l.view_id = 0 AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')';

        $aListings = Phpfox::getLib('database')->select('l.title, l.listing_id, l.image_path, l.server_id, l.price, l.country_iso as country_name, l.city, l.country_child_id, l.time_stamp, l.currency_id, t.description, t.short_description,' . phpfox::getUserField())
            ->from(phpfox::getT('advancedmarketplace'), 'l')
            ->join(phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
            ->join(phpfox::getT('advancedmarketplace_text'), 't', 't.listing_id = l.listing_id')
            ->group('l.listing_id')
            ->where($sCond)
            ->order(" rand() ")
            ->limit(4)
            ->execute('getRows');

        $aListings = array_slice($aListings, 0, 4);
        if (!empty($aListings) && is_array($aListings)) {
            foreach ($aListings as $iKey => $aListing) {
                $aListings[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                    $aListing['listing_id'], $aListing['title']);
            }
        }

        return $aListings;
    }

    public function getExistingReview($iItemId, $iUserId)
    {
        $aReview = phpfox::getLib('database')->select('r.rate_id')
            ->from(phpfox::getT('advancedmarketplace_rate'), 'r')
            ->where('r.listing_id = ' . $iItemId . ' and r.user_id = ' . $iUserId)
            ->execute('getField');

        return $aReview;
    }

    /**
     *
     * @param $iLimit
     * @return array|int|mixed|string
     */
    public function getTopSellers($iLimit, $sOrder = '')
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmartketplace.component_service_advancedmartketplace_gettopsellers')) ? eval($sPlugin) : false);

        db()->select('COUNT(am.listing_id) as total_available, 0 AS total_sold, am.user_id')
            ->from($this->_sTable, 'am')
            ->where('am.module_id = \'advancedmarketplace\' AND am.view_id = 0 AND am.post_status = 1 AND (am.has_expiry = 0 OR (am.has_expiry = 1 AND am.expiry_date > '. PHPFOX_TIME .'))')
            ->group('am.user_id')
            ->union();

        db()->select('0 AS total_available, COUNT(am.listing_id) AS total_sold, am.user_id')
            ->from($this->_sTable, 'am')
            ->where('am.view_id = 2 AND am.module_id = \'advancedmarketplace\'')
            ->group('am.user_id')
            ->union()
            ->unionFrom('s');

        $topSellers = db()->select('SUM(s.total_available) AS total_available, SUM(s.total_sold) AS total_sold, SUM(total_available + total_sold) AS total_listing, p.photo_id as cover_photo_id, p.destination AS cover_photo_path, p.server_id AS cover_photo_server_id, ' . Phpfox::getUserField())
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = s.user_id')
                        ->join(Phpfox::getT('user_field'), 'user_field', 'user_field.user_id = u.user_id')
                        ->leftJoin(Phpfox::getT('photo'), 'p', 'p.photo_id = user_field.cover_photo')
                        ->limit($iLimit)
                        ->order($sOrder)
                        ->group('s.user_id')
                        ->execute('getSlaveRows');
        return $topSellers;
    }

    public function getTotalListings($idUser, $getAll = true)
    {
        return $this->database()->select('COUNT(listing_id) as total_listing')
            ->from($this->_sTable)
            ->where('user_id =' . (int)$idUser . (!$getAll ? ' AND view_id = 0 AND post_status = 1' : ''))
            ->execute('getSlaveField');
    }

    private function _addCategoryUrl(&$aListings)
    {
        if (!empty($aListings) && is_array($aListings)) {
            foreach ($aListings as $iKey => $aListing) {
                $aListings[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                    $aListing['listing_id'], $aListing['title']);
            }
        }
    }

    // nhanlt
    public function buildSubCategory($aCategories, $level = 0, $cId, $iLevel = null)
    {
        if (empty($aCategories)) {
            return false;
        }
        if ($iLevel <= 0) {
            return false;
        }
        // $level--;
        echo sprintf("<ul>");
        foreach ($aCategories as $key => $aCategory) {
            // if($aCategory["level"] >= $iLevel) return false;
            $sLIClass = "";
            if ($aCategory["category_id"] == $cId) {
                // ++$level;
                $sLIClass = "active";
            }

            echo sprintf("<li class=\"submenu $sLIClass\"><a href=\"%s\">%s</a>", $aCategory['url'],
                $aCategory["name"]);
            if (!empty($aCategory["children"])) {
                $this->buildSubCategory($aCategory["children"], $level + 1, $cId, $iLevel - 1);
            }
            echo "</li>";
        }
        echo sprintf("</ul>");
    }

    public function getListingCoordinates()
    {
        $aListings = $this->database()->select('listing_id, lat, lng')
            ->from(phpfox::getT('advancedmarketplace'))
            ->execute('getRows');

        return $aListings;
    }

    public function getSetting()
    {
        $aSettings = array();


        $aLocationSetting = phpfox::getLib('database')->select('*')
            ->from(phpfox::getT('advancedmarketplace_setting'))
            ->where('var_name = "location_setting"')
            ->execute('getRow');
        $aSettings['location_setting'] = (isset($aLocationSetting['value'])) ? $aLocationSetting['value'] : '';

        return $aSettings;
    }

    public function getSettings()
    {

        $aSettings = array();

        $aLocationSetting = phpfox::getLib('database')->select('*')
            ->from(phpfox::getT('advancedmarketplace_setting'))
            ->where('var_name = "location_setting"')
            ->execute('getRow');
        $aSettings['location_setting'] = (isset($aLocationSetting['value'])) ? $aLocationSetting['value'] : '';

        return $aSettings;
    }

    public function getListingsByIds($aIds)
    {
        $aRows = $this->database()->select('listing_id, lat, lng, title, location, address, city')
            ->from($this->_sTable)
            //->where("listing_id IN ($sIds)")
            ->execute('getRows');

        foreach ($aRows as $iKey => $aListing) {
            //$aEvent['start_time'] = Phpfox::getLib('date')->convertFromGmt($aEvent['start_time'], $aEvent['start_gmt_offset']);

            $aListing['link'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aListing['listing_id'],
                $aListing['title']);
            $aRows[$iKey] = $aListing;
        }

        return $aRows;
    }

    /**
     * get feed for not add feed when edit
     * @param $iListingId
     * @return bool
     */

    public function isListingOnFeed($iListingId)
    {
        $aRow = phpfox::getLib('database')->select('*')
            ->from(Phpfox::getT('feed'))
            ->where('type_id = "advancedmarketplace" AND item_id = "' . $iListingId . '"')
            ->execute('getSlaveRow');

        if ($aRow) {
            return true;
        }

        return false;
    }

    public function countImages($iId)
    {
        return $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('advancedmarketplace_image'))
            ->where('listing_id = ' . (int)$iId)
            ->order('ordering ASC')
            ->execute('getSlaveField');
    }

    public function getUploadParams($aParams = null)
    {
        if (isset($aParams['id'])) {
            $iTotalImage = Phpfox::getService('advancedmarketplace')->countImages($aParams['id']);
            $iRemainImage = Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit') - $iTotalImage;
        } else {
            $iRemainImage = Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit');
        }
        $iMaxFileSize = Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'sending' => '$Core.advancedmarketplace.dropzoneOnSending',
            'success' => '$Core.advancedmarketplace.dropzoneOnSuccess',
            'queuecomplete' => '$Core.advancedmarketplace.dropzoneQueueComplete',
        ];

        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('advancedmarketplace.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'submit_button' => '#js_listing_done_upload',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS,
            'update_space' => true,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'on_remove' => 'advancedmarketplace.deleteImage',
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number', ['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => Phpfox::getParam('advancedmarketplace.thumbnail_sizes')
        ];
    }

    public function getUploadDefaultParams($aParams = null)
    {
        $iMaxFileSize = Phpfox::getUserParam('advancedmarketplace.max_upload_size_listing');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('advancedmarketplace.dir_pic'),
            'upload_path' => Phpfox::getParam('advancedmarketplace.url_pic'),
            'thumbnail_sizes' => Phpfox::getParam('advancedmarketplace.thumbnail_sizes'),
            'label' => _p('featured_photo') . ' <span class="p-text-danger">*</span></label>',
            'type_description' => _p('allowed_file_type_jpg_gif_or_png') . ' ' . _p('the_file_size_limit_is_file_size_kb', ['file_size' => $iMaxFileSize * 1048576 / 1024]),
            'max_size_description' => _p('we_suggest_uploading_square_photo_for_best_display_result'),
        ];
    }

    /**
     * Get owner id from listing id
     * @param $iListingId
     * @return int|resource
     */
    public function getOwnerId($iListingId)
    {
        if (!$iListingId) {
            return false;
        }

        return db()->select('user_id')->from(':advancedmarketplace')->where(['listing_id' => $iListingId])->executeField();
    }

    /**
     * Count listing of a specific user
     * @param int $iUserId
     * @return int|string
     */
    public function countMyListings($iUserId = 0)
    {
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }

        return db()->select('count(*)')->from(':advancedmarketplace')->where(['user_id' => $iUserId])->executeField();
    }

    public function getSectionMenu()
    {
        if ((defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW)
            || (defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE)
        ) {
            return [];
        }
        $userLogin = Phpfox::isUser();

        $sInviteTotal = $sMyListingCnt = '';
        if ($userLogin && ($iTotalInvites = Phpfox::getService('advancedmarketplace')->getTotalInvites())) {
            $sInviteTotal = '<span class="invited count-item">' . $iTotalInvites . '</span>';
        }
        $iMyListingCnt = Phpfox::getService('advancedmarketplace')->countMyListings();
        if ($userLogin && $iMyListingCnt) {
            $sMyListingCnt = '<span class="count-item">' . $iMyListingCnt . '</span>';
        }

        $aFilterMenu = array(
            _p('home') => '',
            _p('all_listings') => 'all',
            _p('my_listings') . $sMyListingCnt => 'my',
        );

        if ($userLogin) {
            $wishlistCount = $this->getMyWishlistCount();
            $aFilterMenu[_p('wish_list_replacement') . '<span class="wishlist count-item js_wishlist_count_menu '. (!empty($wishlistCount) ? '' : 'hide') .'">' . (!empty($wishlistCount) ? $wishlistCount : 0) . '</span>'] = 'my-wishlist';
        }

        $aFilterMenu[_p('listing_invites') . $sInviteTotal] = 'invites';

        if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community')) {
            $aFilterMenu[_p('advancedmarketplace.friends_listings')] = 'friend';
        }

        if (Phpfox::getUserParam('advancedmarketplace.can_approve_listings')) {
            $iPendingTotal = Phpfox::getService('advancedmarketplace')->getPendingTotal();

            if ($iPendingTotal) {
                $aFilterMenu[_p('advancedmarketplace.pending_listings') . '<span class="pending count-item">' . $iPendingTotal . '</span>'] = 'pending';
            }
        }
        if (Phpfox::getUserParam('advancedmarketplace.can_view_expired')) {
            $aFilterMenu[_p('advancedmarketplace.expired')] = 'expired';
        }

        $aFilterMenu[_p('view_on_maps')] = 'gmap';

        if ($userLogin) {
            $aFilterMenu[_p('advancedmarketplace.invoices')] = 'advancedmarketplace.invoice';
            $aFilterMenu[_p('advancedmarketplace_seller_management')] = 'advancedmarketplace.invoice.seller';
        }

        return $aFilterMenu;
    }

    public function hasSponsorInFeedFeature($aListing)
    {
        if (!empty($aListing['module_id'])) {
            if (!Phpfox::isModule($aListing['module_id']) || !Phpfox::hasCallback($aListing['module_id'],
                    'getFeedDetails')) {
                return false;
            }
            $aFeedCallback = Phpfox::callback($aListing['module_id'] . '.getFeedDetails', $aListing['item_id']);
            if ($aListing['module_id'] != 'pages' && empty($aFeedCallback['add_to_main_feed'])) {
                return false;
            }
        }

        return Phpfox::isModule('ad') && $aListing['user_id'] == Phpfox::getUserId() && Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed'));
    }

    /* for dislike function
    public function getInfoForAction($aItem)
	{
		if (is_numeric($aItem))
		{
			$aItem = array('item_id' => $aItem);
		}
		$aRow = $this->database()->select('m.listing_id, m.title, m.user_id, u.gender, u.full_name')
			->from(Phpfox::getT('advancedmarketplace'), 'm')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
			->where('m.listing_id = ' . (int) $aItem['item_id'])
			->execute('getSlaveRow');

		$aRow['link'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aRow['listing_id'], $aRow['title']);
		return $aRow;
	}
    */

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('advancedmarketplace.Service_AdvancedMarketplace__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
