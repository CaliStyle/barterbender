<?php

namespace Apps\P_AdvMarketplace\Service;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class Browse extends Phpfox_Service
{

    private $_sCategory = null;

    private $_bIsSeen = false;

    private $_isTagSearching = false;
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('advancedmarketplace');
    }

    public function setIsTagSearching($value) {
        $this->_isTagSearching = $value;
        return $this;
    }

    public function seen()
    {
        $this->_bIsSeen = true;

        return $this;
    }

    public function category($sCategory)
    {
        $this->_sCategory = $sCategory;

        return $this;
    }

    public function processRows(&$aRows)
    {
        foreach ($aRows as $iKey => $aListing) {
            $aRows[$iKey]['aFeed'] = array(
                'feed_display' => 'mini',
                'comment_type_id' => 'advancedmarketplace',
                'privacy' => $aListing['privacy'],
                'comment_privacy' => $aListing['privacy_comment'],
                'like_type_id' => 'advancedmarketplace',
                'feed_is_liked' => (isset($aListing['is_liked']) ? $aListing['is_liked'] : false),
                'feed_is_friend' => (isset($aListing['is_friend']) ? $aListing['is_friend'] : false),
                'item_id' => $aListing['listing_id'],
                'user_id' => $aListing['user_id'],
                'total_comment' => $aListing['total_comment'],
                'feed_total_like' => $aListing['total_like'],
                'total_like' => $aListing['total_like'],
                'feed_link' => Phpfox::getLib('url')->permalink('advancedmarketplace.detail', $aListing['listing_id'],
                    $aListing['title']),
                'feed_title' => $aListing['title'],
                'type_id' => 'advancedmarketplace',
                'feed_mini' => true
            );
            // Mark expired items here so its easier to display them in the template
            if ($aListing['has_expiry'] && $aListing['expiry_date'] <= PHPFOX_TIME) {
                $aRows[$iKey]['is_expired'] = true;
            }
            $aRows[$iKey]['url'] = Phpfox::getLib('url')->permalink('advancedmarketplace.detail',
                $aListing['listing_id'], $aListing['title']);
            Phpfox::getService('advancedmarketplace')->getPermissions($aRows[$iKey]);
        }
    }

    public function query()
    {
        $this->database()->select('mt.description_parsed AS description, mt.short_description, ar.listing_id AS is_wishlist, ')->join(Phpfox::getT('advancedmarketplace_text'),
            'mt', 'mt.listing_id = l.listing_id');
        if($this->request()->get('view') == 'my-wishlist') {
            db()->join(Phpfox::getT('advancedmarketplace_wishlist'), 'ar', 'ar.listing_id = l.listing_id AND ar.is_wishlist = 1 AND ar.user_id = '. Phpfox::getUserId());
        }
        else {
            db()->leftJoin(Phpfox::getT('advancedmarketplace_wishlist'), 'ar', 'ar.listing_id = l.listing_id AND ar.is_wishlist = 1 AND ar.user_id = '. Phpfox::getUserId());
        }
        if (Phpfox::isUser() && Phpfox::isModule('like')) {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik',
                'lik.type_id = \'advancedmarketplace\' AND lik.item_id = l.listing_id AND lik.user_id = ' . Phpfox::getUserId());
        }
        if (Phpfox::getLib('request')->get('sort') == 'recent-viewed') {
            $this->database()->select('rv.timestamp as review_time, ')
                ->join(Phpfox::getT('advancedmarketplace_recent_view'), 'rv',
                    'rv.listing_id = l.listing_id and rv.user_id = ' . phpfox::getUserId());
            $this->database()->order('review_time desc');

        }
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        if (Phpfox::getLib('search')->isSearch() && $bIsCount) {
            $this->database()->innerJoin(Phpfox::getT('user'), 'userDelete', 'userDelete.user_id = l.user_id');
            $this->database()->leftJoin(Phpfox::getT('advancedmarketplace_text'), 'mt', 'mt.listing_id = l.listing_id');
        }

        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
            $this->database()->join(Phpfox::getT('friend'), 'friends',
                'friends.user_id = l.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        if ($this->_sCategory !== null) {
            $this->database()->innerJoin(Phpfox::getT('advancedmarketplace_category_data'), 'mcd',
                'mcd.listing_id = l.listing_id');
            if (!$bIsCount) {
                $this->database()->group('l.listing_id');
            }
        }

        if (Phpfox::getLib('request')->get('sort') == 'most-reviewed') {
            $this->database()->order('l.total_rate desc, l.total_score desc');
        }

        list($isTagSearch, $tagSearchText) = Phpfox::getService('advancedmarketplace.helper')->_isTagSearching();
        if (($isTagSearch && !empty($tagSearchText) && $this->request()->get('req3') != 'category') || $this->_isTagSearching) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag',
                'tag.item_id = l.listing_id AND tag.category_id = \'advancedmarketplace\'');
        }

        if ($this->_bIsSeen !== false) {
            $this->database()->join(Phpfox::getT('advancedmarketplace_invite'), 'mi',
                'mi.listing_id = l.listing_id AND mi.visited_id = 0 AND mi.invited_user_id = ' . Phpfox::getUserId());
        }

    }

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
        if ($sPlugin = Phpfox_Plugin::get('advancedmarketplace.service_browse__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
