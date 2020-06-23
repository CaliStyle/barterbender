<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright        [YOUNETCO]
 * @author        NghiDV
 * @package        Module_Suggestion
 * @version        $Id: Suggestion.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Service_Suggestion extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('suggestion');
    }

    protected $_countObject = 0;
    protected $_countPeopleYouMayKnow = 0;
    protected $_countPagesYouMayLike = 0;
    protected $_countJobYouMayLike = 0;

    protected $_iFriends = 0;
    protected $_iMys = 0;

    protected $_countObj = array();

    public function getCountObj()
    {
        return $this->_countObj;
    }

    public function getTotalFriendsSuggestion()
    {
        return $this->_iFriends;
    }

    public function getTotalMySuggestion()
    {
        return $this->_iMys;
    }

    public function getCountObject()
    {
        return $this->_countObject;
    }

    public function getCountPeopleYouMayKnow()
    {
        return $this->_countPeopleYouMayKnow;
    }

    public function getCountPagesYouMayLike()
    {
        return $this->_countPagesYouMayLike;
    }

    public function getCountJobsYouMayLike()
    {
        return $this->_countJobYouMayLike;
    }

    /*
     * check is page with short link
     */
    public function isPage($sUrl)
    {
        if (!Phpfox::isModule('pages')) {
            return false;
        }
        $aPage = $this->database()->select('*')
            ->from(Phpfox::getT('pages_url'))
            ->where('vanity_url = \'' . $this->database()->escape($sUrl) . '\'')
            ->execute('getSlaveRow');

        if (!isset($aPage['page_id'])) {
            return false;
        }

        return $aPage;
    }

    public function getModuleName($sModule)
    {

        $sModulePhrase = $this->database()->select('m.phrase_var_name')->from(Phpfox::getT('module'), 'm')->where('module_id = "' . $sModule . '" AND product_id != "phpfox"')->execute('getField');

        if ($sModulePhrase != null && Phpfox::isModule($sModule)) {
            if ($sModulePhrase == 'module_advancedphoto')
                $sModulePhrase = 'advancedphoto';

            return ucfirst(_p($sModule . '.' . $sModulePhrase));
        } else {
            $sModulePhrase = $this->database()->select('m.phrase_var_name')->from(Phpfox::getT('module'), 'm')->where('module_id = "' . $sModule . '" AND product_id = "phpfox"')->execute('getField');
            if ($sModulePhrase != null && Phpfox::isModule($sModule)) {
                $phares = ucfirst(_p($sModule . '.' . $sModulePhrase));
                $phares = trim($phares, "s");
                return $phares;
            } else {
                return ucfirst($sModule);
            }
        }
    }


    public function getUserList()
    {

        /*dont get friends list in filter of search in suggestion/recommendation block.*/
        return;

        /*        static $aRows = array();


                if ($aRows) 		{
                    return $aRows;
                }


                $aRows = $this->database()->select('fl.list_id, fl.name, COUNT(f.friend_id) AS used')
                        ->from(Phpfox::getT('friend_list'), 'fl')

                        ->leftJoin(Phpfox::getT('friend'), 'f', 'f.list_id = fl.list_id AND f.user_id = fl.user_id')
                        ->where('fl.user_id = ' . (int) Phpfox::getUserId())
                        ->group('fl.list_id')
                        ->order('fl.name ASC')
                        ->execute('getSlaveRows');*/


    }

    public function isNotificationMessage($sModule)
    {
        /*check module name does not have string 'itemLiked' and 'friend'; 
         * expect feed item like and just friend together
         * expect comment not show suggestion
         */
        $bHasItemLiked = strpos($sModule, 'itemLiked');
        $bHasFriend = strpos($sModule, 'friend');
        $bHasComment = strpos($sModule, 'comment');
        if ($bHasFriend !== FALSE || $bHasItemLiked !== FALSE || $bHasComment !== FALSE)
            return true;
        return false;
    }

    public function getPhotoDetail($iPhotoId)
    {
        return $this->database()->select('p.title')->from(Phpfox::getT('photo'), 'p')->where('photo_id = ' . (int)$iPhotoId)->execute('getRow');
    }

    public function getFoxFeedsProDetail($iFoxFeedsProId)
    {
        return $this->database()->select('p.item_alias')->from(Phpfox::getT('ynnews_items'), 'p')->where('item_id = ' . (int)$iFoxFeedsProId)->execute('getRow');
    }

    public function getContestDetail($iContestId)
    {
        return $this->database()
            ->select('c.contest_name')
            ->from(Phpfox::getT('contest'), 'c')
            ->where('contest_id = ' . (int)$iContestId)
            ->execute('getRow');
    }

    public function getContestReminder($iContestId)
    {
        return $this->database()
            ->select('c.*')
            ->from(Phpfox::getT('contest'), 'c')
            ->where('contest_id = ' . (int)$iContestId)
            ->execute('getRow');
    }

    public function getFundRaisingDetail($iItemId)
    {
        return $this->database()
            ->select('fc.title')
            ->from(Phpfox::getT('fundraising_campaign'), 'fc')
            ->where('campaign_id = ' . (int)$iItemId)
            ->execute('getRow');
    }

    public function getCouponDetail($iItemId)
    {
        return $this->database()
            ->select('c.title')
            ->from(Phpfox::getT('coupon'), 'c')
            ->where('coupon_id = ' . (int)$iItemId)
            ->execute('getRow');
    }

    public function getCouponReminder($iItemId)
    {
        return $this->database()
            ->select('c.*')
            ->from(Phpfox::getT('coupon'), 'c')
            ->where('coupon_id = ' . (int)$iItemId)
            ->execute('getRow');
    }


    public function getPetitionDetail($iItemId)
    {
        return $this->database()
            ->select('p.title')
            ->from(Phpfox::getT('petition'), 'p')
            ->where('petition_id = ' . (int)$iItemId)
            ->execute('getRow');
    }

    public function getJobDetail($iItemId)
    {
        return $this->database()
            ->select('j.title')
            ->from(Phpfox::getT('jobposting_job'), 'j')
            ->where('job_id = ' . (int)$iItemId)
            ->execute('getRow');
    }

    public function getJobReminder($iItemId)
    {
        return $this->database()
            ->select('j.*')
            ->from(Phpfox::getT('jobposting_job'), 'j')
            ->where('job_id = ' . (int)$iItemId)
            ->execute('getRow');
    }


    public function getKnowledgeBaseDetail($iItemId)
    {
        return $this->database()
            ->select('a.title')
            ->from(Phpfox::getT('gettingstarted_article'), 'a')
            ->where('article_id = ' . (int)$iItemId)
            ->execute('getRow');
    }

    public function getUltimateVideos_videoDetail($iItemId)
    {
        return $this->database()
            ->select('v.title')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->where('video_id = ' . (int)$iItemId)
            ->execute('getRow');
    }

    public function getUltimateVideos_playlistDetail($iItemId)
    {
        return $this->database()
            ->select('p.title')
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'p')
            ->where('video_id = ' . (int)$iItemId)
            ->execute('getRow');
    }

    public function getYnsocialstore_StoreDetail($iItemId)
    {
        return $this->database()
            ->select('s.name')
            ->from(Phpfox::getT('ynstore_store'), 's')
            ->where('store_id = ' . (int)$iItemId)
            ->execute('getRow');
    }

    public function getYnsocialstore_ProductDetail($iItemId)
    {
        $this->database()->select('ep.*')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->where('ep.product_id = ' . (int) $iItemId)
            ->execute('getSlaveRow');
    }

    public function getYnblog_BlogDetail($iItemId)
    {
        $this->database()->select('*')
            ->from(Phpfox::getT('ynblog_blogs'))
            ->where('blog_id = ' . (int) $iItemId)
            ->execute('getSlaveRow');
    }


    /*
     * get user by field
     */

    public function getUserBy($sField, $sValue)
    {
        return $this->database()->select('*')->from(Phpfox::getT('user'))->where($sField . ' = "' . $sValue . '" AND user_name != ""')->execute('getRow');
    }

    /*
     * get random pages to share ...
     */

    public function getPages()
    {
        if (Phpfox::isModule('pages')) {
            $iLimit = (int)Phpfox::getUserParam('suggestion.number_of_entries_display_in_blocks');

            $aRows = $this->database()
                ->select('p.*, pu.vanity_url, '. Phpfox::getUserField())
                ->from(Phpfox::getT('pages'), 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.view_id = 0 AND p.item_type = 0')
                ->order('p.time_stamp DESC')
                ->limit($iLimit)
                ->execute('getRows');

            if (count($aRows) > 0) {
                foreach ($aRows as &$aRow) {
                    if (!isset($aRow['vanity_url']) || empty($aRow['vanity_url'])) {
                        $sLink = Phpfox::permalink('pages', $aRow['page_id'], $aRow['title']);
                    } else {
                        $sLink = $aRow['vanity_url'];
                    }
                    $aRow['title_link'] = Phpfox::getService('suggestion.url')->makeLink($sLink, $aRow['title']);

                    $aUser = Phpfox::getService('suggestion')->getUser($aRow['user_id']);
                    $aUser['suffix'] = '_50_square';
                    $aUser['max_width'] = '50';
                    $aUser['max_height'] = '50';
                    $aUser['user'] = $aUser;

                    $img = '<span class="thumb">' . Phpfox::getLib('phpfox.image.helper')->display($aUser) . '</span>';

                    $pattern = '/(.+)href="(.+)" title(.+)/i';
                    $replacement = 'href="${2}';
                    $strtmp = preg_replace($pattern, $replacement, $img);
                    $img = str_replace($strtmp, 'href="' . $sLink, $img);

                    $aRow['img'] = $img;

                    $aRow['link'] = $sLink;

                    $aRow['encode_link'] = base64_encode($aRow['title_link']);

                    $aRow['isAllowSuggestion'] = Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getService('suggestion')->isSupportModule('pages');

                    //process privacy
                    $iPrivacy = $aRow['privacy'];
                    $iUserId = $aRow['user_id'];
                    $iFriendId = Phpfox::getUserId();
                    $aRow['is_right'] = (int)Phpfox::getService('suggestion')->isRightPrivacy($iPrivacy, $iUserId, $iFriendId);

                    $isUserViewSuggestion = Phpfox::getService('suggestion')->isUserViewSuggestion($iFriendId, 'suggestion_pages', $aRow['page_id']);

                    //if recent is belong current user, not display link join pages;
                    if ($aRow['user_id'] == Phpfox::getUserId()) {
                        $aRow['display_join_link'] = false;
                        $aRow['user_link'] = _p('suggestion.me');
                    } elseif (!$isUserViewSuggestion) {
                        $aRow['display_join_link'] = true;
                        $aRow['user_link'] = Phpfox::getService('suggestion')->getUserLink($aRow['user_id']);
                    } else {
                        $aRow['display_join_link'] = false;
                        $aRow['user_link'] = Phpfox::getService('suggestion')->getUserLink($aRow['user_id']);
                    }
                }
            }
            return $aRows;
        }
    }

    /*
     * check FriendID is right privacy with UserID
     */

    public function isRightPrivacy($iPrivacy, $iUserId, $iFriendId)
    {

        if ($iUserId == $iFriendId) return true;

        switch ($iPrivacy) {
            case 0:
                return true;
                break;
            case 1://is friends
                return Phpfox::getService('suggestion')->isMyFriend($iFriendId, $iUserId);
                break;
            case 2://get friends of friends
                $aRows = Phpfox::getService('suggestion')->getFriendsOfFriends($iUserId);
                $sFriendsId = ',' . implode(',', $aRows) . ',';
                if (strpos($sFriendsId, ',' . $iFriendId . ',') !== FALSE) {
                    return true;
                }
                return false;
                break;
            case 3://only me
                return false;
                break;
        }
    }

    /*
     * get friends list of user's friends
     */

    public function getFriendsOfFriends($iUserId)
    {
        $aFriendsList = Phpfox::getService('suggestion')->getFriendsOfUserId($iUserId);
        $aRet = array();

        if (count($aFriendsList) > 0) {
            foreach ($aFriendsList as $k => $iFriendId) {

                $_aFriendsList = Phpfox::getService('suggestion')->getFriendsOfUserId($iFriendId['friend_user_id']);
                if (count($_aFriendsList) > 0) {

                    foreach ($_aFriendsList as $_k => $_iFriendId) {
                        $aRet[$_iFriendId['friend_user_id']] = $_iFriendId['friend_user_id'];
                    }
                }
            }
        }

        return $aRet;
    }

    /*
     * get friends list of userid
     */

    public function getFriendsOfUserId($iUserId)
    {

        $aRows =

            $this->database()
                ->select('friend_user_id')
                ->from(Phpfox::getT('friend'), 'f')
                ->join(Phpfox::getT('user'), 'u', 'f.friend_user_id = u.user_id')
                ->where('f.user_id = ' . (int)$iUserId . " AND u.profile_page_id = 0")
                ->execute('getRows');


        return $aRows;
    }

    /*
     * get random events still not end date
     */

    public function getEvents()
    {
        if (Phpfox::isModule('event') || Phpfox::isModule('fevent')) {

            (Phpfox::isModule('fevent') == true ? $sTableName = 'fevent' : $sTableName = 'event');

            //get country iso of current user
            $sCountryIso = Phpfox::getUserBy('country_iso');

            $iLimit = (int)Phpfox::getUserParam('suggestion.number_of_entries_display_in_blocks');
            $aRows = $this->database()
                ->select('*')
                ->from(Phpfox::getT($sTableName), 'e')
                //->where('e.end_time > ' . PHPFOX_TIME . ' AND user_id != ' . (int) Phpfox::getUserId())//get event not expect current user
                ->where('e.end_time > ' . PHPFOX_TIME . ' AND e.country_iso ="' . $sCountryIso . '"')
                ->order('e.time_stamp DESC')
                ->limit($iLimit)
                ->execute('getRows');


            if (count($aRows) < $iLimit) {
                $iRemainItems = $iLimit - count($aRows);
                //get remain items with other location
                $aRows2 = $this->database()
                    ->select('*')
                    ->from(Phpfox::getT($sTableName), 'e')
                    //->where('e.end_time > ' . PHPFOX_TIME . ' AND user_id != ' . (int) Phpfox::getUserId())//get event not expect current user
                    ->where('e.end_time > ' . PHPFOX_TIME . ' AND e.country_iso !="' . $sCountryIso . '"')
                    ->order('e.time_stamp DESC')
                    ->limit($iRemainItems)
                    ->execute('getRows');
                $aRows = array_merge($aRows, $aRows2);
            }

            if (count($aRows) > 0) {
                foreach ($aRows as &$aRow) {
                    $sCallback = $sTableName . '.callback';
                    $sLink = Phpfox::getService($sCallback)->getFeedRedirect($aRow['event_id']);

                    $aRow['title_link'] = Phpfox::getService('suggestion.url')->makeLink($sLink, $aRow['title']);
                    //$aRow['join_link'] = Phpfox::getService('suggestion.url')->makeLink($sLink, _p('suggestion.join_event'));

                    $aUser = Phpfox::getService('suggestion')->getUser($aRow['user_id']);
                    //$aUser = Phpfox::getService('suggestion')->getUser(0);

                    //var_dump($aUser);
                    //die();
                    $aRow['hasUser'] = true;
                    if (!isset($aUser['user_id'])) {
                        $aRow['hasUser'] = false;
                    }

                    $aUser['suffix'] = '_50_square';
                    $aUser['max_width'] = '50';
                    $aUser['max_height'] = '50';
                    $aUser['user'] = $aUser;
                    $img = '<span class="thumb">' . Phpfox::getLib('phpfox.image.helper')->display($aUser) . '</span>';

                    /*

                    $pattern = '/(.+)href="(.+)" title(.+)/i';
                    $replacement = 'href="${2}';
                    $strtmp = preg_replace($pattern, $replacement, $img);
                    $img = str_replace($strtmp,'href="'.$sLink,$img);
                     * */

                    $aRow['img'] = $img;

                    $aRow['link'] = $sLink;

                    $aRow['encode_link'] = base64_encode($aRow['title_link']);

                    $aRow['isAllowSuggestion'] = Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getService('suggestion')->isSupportModule($sTableName);

                    //process privacy
                    $iPrivacy = $aRow['privacy'];
                    $iUserId = $aRow['user_id'];
                    $iFriendId = Phpfox::getUserId();

                    $isUserViewSuggestion = Phpfox::getService('suggestion')->isUserViewSuggestion($iFriendId, 'suggestion_' . $sTableName, $aRow['event_id'], $sTableName);

                    //if item is belong current user return true
                    if ($iUserId != $iFriendId)
                        $aRow['is_right'] = (int)Phpfox::getService('suggestion')->isRightPrivacy($iPrivacy, $iUserId, $iFriendId);
                    else
                        $aRow['is_right'] = 1;

                    //if recent is belong current user, not display link join pages;
                    if ($aRow['user_id'] == Phpfox::getUserId()) {
                        $aRow['display_join_link'] = false;
                        $aRow['user_link'] = _p('suggestion.me');
                    } elseif (!$isUserViewSuggestion) {
                        $aRow['display_join_link'] = true;
                        $aRow['user_link'] = Phpfox::getService('suggestion')->getUserLink($aRow['user_id']);
                    } else {
                        $aRow['display_join_link'] = false;
                        $aRow['user_link'] = Phpfox::getService('suggestion')->getUserLink($aRow['user_id']);
                    }
                }
            }
            return $aRows;
        }
    }

    public function isMyFriend($iFriendId, $iUserId = '')
    {
        if ($iUserId === '') {
            $aRow = $this->database()
                ->select('*')
                ->from(Phpfox::getT('friend'), 'f')
                ->where('f.user_id = ' . (int)Phpfox::getUserId() . ' AND f.friend_user_id = ' . (int)$iFriendId)
                ->execute('getRow');
            if (count($aRow) > 0)
                return true;
            return false;
        } else {
            $aRow = $this->database()
                ->select('*')
                ->from(Phpfox::getT('friend'), 'f')
                ->where('f.user_id = ' . (int)$iUserId . ' AND f.friend_user_id = ' . (int)$iFriendId)
                ->execute('getRow');
            if (count($aRow) > 0)
                return true;
            return false;
        }
    }

    public function getPrivateData($iUserId)
    {
        $aRow = $this->database()->select('*')
            ->from($this->_sTable, 's')
            ->where('s.user_id = ' . $iUserId . ' AND s.friend_user_id = 0 AND s.item_id = 0')
            ->order('s.suggestion_id DESC')
            ->execute('getRow');
        return $aRow;
    }

    public function getSuggestionData($iSuggestionId)
    {
        $aRows = $this->database()->select('DISTINCT(item_id) as item_id, processed')->from(Phpfox::getT('suggestion'))->where('suggestion_id = ' . (int)$iSuggestionId)->execute('getRows');
        return (array)$aRows;
    }

    public function getSuggestionDetailBySuggestionId($iSuggestionId)
    {
        $aRow = $this->database()->select('DISTINCT(item_id) as item_id')->from(Phpfox::getT('suggestion'))->where('suggestion_id = ' . (int)$iSuggestionId)->execute('getRow');
        return (array)$aRow;
    }

    public function getForumPostByForumPostID($iForum_Post_Id)
    {
        $aRow = $this->database()
            ->select('fp.*,ft.title as thread_title ')
            ->from(Phpfox::getT('forum_post'), 'fp')
            ->leftJoin(Phpfox::getT('forum_thread'), 'ft', 'ft.thread_id = fp.thread_id')
            ->where('fp.post_id = ' . (int)$iForum_Post_Id)->execute('getRow');
        return (array)$aRow;
    }

    public function getSearchKey($sView)
    {
        //check if current view return key or unset Key


        if (isset($_SESSION['suggestion']['current_view']) && $_SESSION['suggestion']['current_view'] != $sView) {
            Phpfox::getService('suggestion')->resetSearchKey();
            return '';
        }

        if (isset($_SESSION[Phpfox::getParam('core.session_prefix')]['search']['suggestion'])) {//is choose keywords
            foreach ($_SESSION[Phpfox::getParam('core.session_prefix')]['search']['suggestion'] as $key => $value) {
                if (isset($_SESSION['suggestion']['keys']))
                    unset($_SESSION['suggestion']['keys']);
                if ($value[0] != '') {
                    $_SESSION['suggestion']['keys'] = $value[0];
                    $_SESSION['suggestion']['current_view'] = $sView;
                    return $value[0] . '';
                } else {
                    return '';
                }
            }
        } else {
            if (isset($_SESSION['suggestion']['keys']))
                return $_SESSION['suggestion']['keys'];
            else
                return '';
        }
    }

    public function resetSearchKey()
    {
        if (isset($_SESSION[Phpfox::getParam('core.session_prefix')]['search']['suggestion'])) {
            unset($_SESSION[Phpfox::getParam('core.session_prefix')]['search']['suggestion']);
        }

        if (isset($_SESSION['suggestion']['keys']))
            unset($_SESSION['suggestion']['keys']);

        if (isset($_SESSION['suggestion']['current_view']))
            unset($_SESSION['suggestion']['current_view']);
    }

    public function getMessageStruct($sModule)
    {
        switch ($sModule) {
            case 'suggestion_friend';
                return _p('suggestion.message_friend_suggestion');
                break;
            case 'suggestion_recommendation';
                return _p('suggestion.message_friend_recommendation');
                break;
        }
    }

    /*
     * convert from short module to long 'friend => suggestion_suggestion' or from long to short suggestion_suggestion=>friend
     */

    public function convertModule($sModule)
    {
        $sModule = strtolower($sModule);
        if (strpos($sModule, 'suggestion_') === FALSE) {
            return 'suggestion_' . $sModule;
        } else {
            $sShortModule = preg_replace('/suggestion_/', '', $sModule);
            return preg_replace('/(suggestion|recommendation)/', 'friend', $sShortModule);
        }
    }

    public function isSupportModule($sModule)
    {
        /*hard code for module link*/
        if ($sModule == 'link') return true;

        $sSupportModule = Phpfox::getUserParam('suggestion.support_module');

        if ($sSupportModule != '') {
            $sModule = strtolower($sModule);
            if ($sModule == 'friend') return true;//default support module friend
            $sSupportModule = strtolower($sSupportModule);
            if ($sModule != "") {
                if (strpos($sSupportModule, $sModule) !== FALSE && Phpfox::isModule($sModule)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function buildMenu()
    {
        $aFilterMenu = array();

        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            $aFilterMenu[_p('suggestion.all_suggestions')] = 'all';
            $aFilterMenu[_p('suggestion.my_suggestions')] = 'my';
            $aFilterMenu[_p('suggestion.friend_suggestion')] = 'friends';
            $aFilterMenu[_p('suggestion.incoming_suggestions')] = 'incoming';
            $aFilterMenu[_p('suggestion.pending_suggestions')] = 'pending';
//			$aFilterMenu[] = true; //breakline
        }
        Phpfox::getLib('template')->buildSectionMenu('suggestion', $aFilterMenu);
    }

    public function getSuggestionFriendList($iFriendId)
    {

        $where = ' friend_user_id=' . $iFriendId . ' AND module_id = "suggestion_friend" AND processed = 0';

        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('suggestion'), 's')
            ->where($where)
            ->execute('getRows');


        $aAlreadyFriends = $this->getFriendsOfUserId(Phpfox::getUserId());


        $aFriends = array();

        if (!empty($aAlreadyFriends)) {
            foreach ($aAlreadyFriends as $key => $value) {
                $aFriends[] = $value['friend_user_id'];
            }
        }

        foreach ($aRows as $key => $aRow) {

            foreach ($aFriends as $aFriend) {
                if ($aFriend == $aRow['item_id']) {

                    Phpfox::getService('suggestion.process')->changeStatusSuggestFriend(
                        $aRow['user_id'],
                        $aRow['friend_user_id'],
                        $aRow['item_id'],
                        1,
                        "suggestion_friend"
                    );
                    unset($aRows[$key]);
                    break;
                }
            }
        }

        $aSuggestFriend = array();

        $aPendingFriends = $this->getFriendsPendingList($iFriendId, 0, '');
        foreach ($aRows as $aRow) {

            $flag_add_suggest_friend = 1;
            foreach ($aPendingFriends as $PendingFriend) {
                if ($PendingFriend['user_id'] == $aRow['item_id']) {
                    $flag_add_suggest_friend = 0;

                    Phpfox::getService('suggestion.process')->changeStatusSuggestFriend(
                        $aRow['user_id'],
                        $aRow['friend_user_id'],
                        $aRow['item_id'],
                        1,
                        "suggestion_friend"
                    );
                    break;
                }
            }
            if ($flag_add_suggest_friend) {

                $aSuggestFriend[$aRow['item_id']]['suggest_id'] = $aRow['suggestion_id'];
                $aSuggestFriend[$aRow['item_id']]['suggestion_id'] = $aRow['suggestion_id'];
                $aSuggestFriend[$aRow['item_id']]['sView'] = 'friendsfriend';
                $aSuggestFriend[$aRow['item_id']]['module_id'] = $aRow['module_id'];
                $aSuggestFriend[$aRow['item_id']]['item_id'] = $aRow['item_id'];
                $aSuggestFriend[$aRow['item_id']]['url'] = $aRow['url'];
                $aSuggestFriend[$aRow['item_id']]['item_id'] = $aRow['item_id'];
                $aSuggestFriend[$aRow['item_id']]['title'] = $aRow['title'];
                $aSuggestFriend[$aRow['item_id']]['friend_user_id'] = $aRow['friend_user_id'];
                $aSuggestFriend[$aRow['item_id']]['user_suggest'] = $this->getUser($aRow['user_id']);
                $aSuggestFriend[$aRow['item_id']]['number_mutal_friend'] = count($this->getMutualFriends($aRow['item_id']));
                $aSuggestFriend[$aRow['item_id']]['info_suggestion_friend'] = $this->getUser($aRow['item_id']);
            }
        }


        return $aSuggestFriend;

    }

    public function getPeopleYouMayKnow($iUserId, $offset = -1)
    {

        /*get friend of friend*/
        $aFriendsLists = Phpfox::getService('suggestion')->getFriendsOfUserId($iUserId);
        $aFriendsLists1 = array();

        //covert to key
        foreach ($aFriendsLists as $key => $aFriendsList) {
            $aFriendsLists1[] = $aFriendsList['friend_user_id'];
        }


        $aFriendOfFriend = array();

        if (count($aFriendsLists1) > 0) {

            foreach ($aFriendsLists1 as $k => $iFriendId) {

                $_aFriendsList = Phpfox::getService('suggestion')->getFriendsOfUserId($iFriendId);
                if (count($_aFriendsList) > 0) {

                    foreach ($_aFriendsList as $_k => $_iFriendId) {
                        if (!in_array($_iFriendId['friend_user_id'], $aFriendsLists1))
                            $aFriendOfFriend[$_iFriendId['friend_user_id']] = $_iFriendId['friend_user_id'];
                    }
                }
            }
        }

        unset($aFriendOfFriend[$iUserId]);

        /*remove request pending friend*/
        $aRequestPendingFriends = $this->getFriendsPendingList($iUserId);
        $aRequestPendingFriend1 = array();

        if (!empty($aRequestPendingFriends)) {
            foreach ($aRequestPendingFriends as $key => $aRequestPendingFriend) {
                $aRequestPendingFriend1[] = $aRequestPendingFriend['user_id'];
            }

            foreach ($aRequestPendingFriend1 as $key => $value) {
                unset($aFriendOfFriend[$value]);
            }
        }
        /*remove who alrealy send request*/
        $aWhoRequesFriends = $this->getWhoRequestFriend($iUserId);

        $aWhoRequesFriends1 = array();

        if (!empty($aWhoRequesFriends)) {
            foreach ($aWhoRequesFriends as $key => $aWhoRequesFriend) {
                $aWhoRequesFriends1[] = $aWhoRequesFriend['friend_user_id'];
            }

            foreach ($aWhoRequesFriends1 as $key => $value) {
                unset($aFriendOfFriend[$value]);
            }
        }


        $allSuggestions = $this->getSuggestionListForModule($iUserId, 'suggestion_friend');


        $aPeopleYouMayKnow = array();

        if (count($allSuggestions) > 0) {
            foreach ($allSuggestions as $key => $allSuggestion) {
                $allSuggestions1[] = $allSuggestion['item_id'];
            }

            foreach ($allSuggestions1 as $key => $value) {
                unset($aFriendOfFriend[$value]);
            }
        }
        $aFriendOfFriend1 = array();
        if ($offset == -1) {
            shuffle($aFriendOfFriend);

            $limit = count($aFriendOfFriend) < Phpfox::getParam('suggestion.number_people_may_you_know_header_bar') ? count($aFriendOfFriend) : Phpfox::getParam('suggestion.number_people_may_you_know_header_bar');


            for ($i = 0; $i < $limit; $i++) {

                $aFriendOfFriend1[] = array_shift($aFriendOfFriend);
            }
        } else {
            $aFriendOfFriend1 = array_slice($aFriendOfFriend, $offset, Phpfox::getParam('suggestion.number_item_on_other_block'));
        }

        foreach ($aFriendOfFriend1 as $key => $aFriendsList) {

            $aPeopleYouMayKnow[$aFriendsList]['number_mutal_friend'] = count($this->getMutualFriends($aFriendsList));
            $aPeopleYouMayKnow[$aFriendsList]['info_suggestion_friend'] = $this->getUser($aFriendsList);

            $aPeopleYouMayKnow[$aFriendsList]['avatar'] = Phpfox::getService('suggestion')->getUserAvatar($aFriendsList);
            $aPeopleYouMayKnow[$aFriendsList]['info'] = Phpfox::getService('suggestion')->getUserLink($aFriendsList);

            $url = Phpfox::getLib('url')->makeUrl($aPeopleYouMayKnow[$aFriendsList]['info_suggestion_friend']['user_name'], array('friend', 'mutual'));
            list($iTotal, $aMultualFriend) = Phpfox::getService('friend')->getMutualFriends($aFriendsList);

            if ($iTotal == 1) {
                $phare = _p('friend.1_friend_in_common');
                $aPeopleYouMayKnow[$aFriendsList]['create'] = '<div class="p_bottom_5" style="position:relative;"><a href="' . $url . '">' . $phare . '</a></div>';
            } elseif ($iTotal == 0) {
                $aPeopleYouMayKnow[$aFriendsList]['create'] = '';
            } else {
                $phare = _p('friend.total_friends_in_common', array('total' => $iTotal));
                $aPeopleYouMayKnow[$aFriendsList]['create'] = '<div class="p_bottom_5" style="position:relative;"><a href="' . $url . '">' . $phare . '</a></div>';
            }
            $aPeopleYouMayKnow[$aFriendsList]['accept'] = _p('suggestion.add_friend_header_block');
            $aPeopleYouMayKnow[$aFriendsList]['friend_user_id'] = Phpfox::getUserId();
            $aPeopleYouMayKnow[$aFriendsList]['friend_friend_user_id'] = $aFriendsList;
            $aPeopleYouMayKnow[$aFriendsList]['url'] = '';
            $aPeopleYouMayKnow[$aFriendsList]['module_id'] = 'people_you_may_know';
            $aPeopleYouMayKnow[$aFriendsList]['suggestion_id'] = '';
            $aPeopleYouMayKnow[$aFriendsList]['suggest'] = '';
            $aPeopleYouMayKnow[$aFriendsList]['message'] = '';


        }

        $total = count($aFriendOfFriend);
        $this->_countPeopleYouMayKnow = $total;


        return $aPeopleYouMayKnow;
    }


    public function getPageYouMayLike($offset = -1)
    {
        //get pages that current user created.
        $aCreatedSql = phpfox::getLib("database")->select('page_id')
            ->from(phpfox::getT('pages'))
            ->where('user_id=' . phpfox::getUserId().' AND item_type = 0')
            ->execute('getRows');
        $aCreated = array();
        if (count($aCreatedSql) > 0) {
            foreach ($aCreatedSql as $value) {
                $aCreated[] = $value['page_id'];
            }
        }

        // get page current user like
        $aLikedSql = phpfox::getLib("database")->select('item_id as page_id')
            ->from(phpfox::getT('like'))
            ->where("type_id = 'pages' AND user_id=" . phpfox::getUserId())
            ->execute('getRows');

        $aLiked = array();
        if (count($aLikedSql) > 0) {
            foreach ($aLikedSql as $value) {
                $aLiked[] = $value['page_id'];
            }
        }
        $aOthers = array_unique(array_merge($aCreated, $aLiked));
        $aLikeOtherPages = array_diff($aLiked, $aCreated);
        $aPageSame = array();
        if (!empty($aLikeOtherPages)) {
            $aPageSameCategorySql = phpfox::getLib("database")
                ->select('category_id')
                ->from(phpfox::getT('pages'))
                ->where(" item_type = 0 AND page_id IN (" . implode($aLikeOtherPages, ',') . ") AND user_id != " . Phpfox::getUserId())
                ->execute('getRows');
            $aPageSameCategory = array();
            foreach ($aPageSameCategorySql as $value) {
                $aPageSameCategory[] = $value['category_id'];
            }
            if(!count($aPageSameCategory))
            {
                $aPageSameCategory[] = 0;
            }

            $aPageSameOwnerSql = phpfox::getLib("database")
                ->select('user_id')
                ->from(phpfox::getT('pages'))
                ->where(" item_type = 0 AND page_id IN (" . implode($aLiked, ',') . ") AND user_id != " . Phpfox::getUserId())
                ->execute('getRows');

            $aPageSameOwner = array();
            foreach ($aPageSameOwnerSql as $value) {
                $aPageSameOwner[] = $value['user_id'];
            }
            if(!count($aPageSameOwner))
            {
                $aPageSameOwner[] = 0;
            }
            $aPageSameSql = phpfox::getLib("database")->select('page_id')
                ->from(phpfox::getT('pages'))
                ->where(" item_type = 0 AND category_id IN (" . implode($aPageSameCategory, ',') . ") OR user_id IN (" . implode($aPageSameOwner, ',') . ") ")
                ->execute('getRows');

            foreach ($aPageSameSql as $value) {
                $aPageSame[] = $value['page_id'];
            }
        }
        $aPageSame = array_diff($aPageSame, $aLiked);
        if (!empty($aPageSame)) {

            $aPages = Phpfox_Database::instance()->select('*')
                ->from(phpfox::getT('pages'))
                ->where(" item_type = 0 AND page_id IN  (" . implode($aPageSame, ',') . ')')
                ->order('time_stamp DESC')
                ->execute('getRows');
        } else
            if (!empty($aOthers)) {
                $aPages = Phpfox_Database::instance()->select('*')
                    ->from(phpfox::getT('pages'))
                    ->where(" item_type = 0 AND page_id NOT IN  (" . implode($aOthers, ',') . ')')
                    ->order('time_stamp DESC')
                    ->execute('getRows');
            } else {
                $aPages = array();
            }
        foreach ($aPages as $key => &$aPage) {
            $aPage['module_id'] = 'pages_you_may_like';
            $aPage['url'] = Phpfox::getLib('url')->makeUrl('pages', $aPage['page_id']);
            $aPage['create'] = '<p>' . _p('suggestion.added_by_name_on_time', array('name' => $this->getUserLink($aPage['user_id']), 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aPage['time_stamp']))) . '</p>';
            $aPage['info'] = Phpfox::getService('suggestion.url')->makeLink($aPage['url'], Phpfox::getLib('phpfox.parse.output')->shorten($aPage['title'], 70, '...'));
            $object = $this->getObject($aPage['page_id'], 'pages');
            if (isset($object['image_path']) && $object['image_path'] != '') {
                $aImgs = $this->parserData($object['image_server_id'], $object['image_path'], 'core.url_user');
                $img = $this->getObjectAvatar($aImgs);
                $aPage['avatar'] = '<a href="' . $aPage['url'] . '">' . $img . '</a>';
            } else {
                $aPage['avatar'] = '<a href="' . $aPage['url'] . '"><img src="' . Phpfox::getParam('core.path') . '/module/suggestion/static/image/pages.png" alt="" height="75" width="75" ></a>';
            }
            $aPage['suggestion_id'] = '';
            $aPage['suggest'] = '';
            $aPage['friend_user_id'] = Phpfox::getUserId();
            $aPage['friend_friend_user_id'] = $aPage['page_id'];
            $aPage['message'] = '';
            $aPage['accept'] = _p('suggestion.like');

        }
        $total = count($aPages);
        $this->_countPagesYouMayLike = $total;
        if ($offset != -1) {
            $aPages = array_slice($aPages, $offset, Phpfox::getParam('suggestion.number_item_on_other_block'));
        }/*------limit and random---*/
        return $aPages;
    }

    public function getJobYouMayLike($offset = -1)
    {
        $aAppliedJobSql = phpfox::getLib("database")->select('job_id')
            ->from(phpfox::getT('jobposting_application'))
            ->where('user_id=' . phpfox::getUserId())
            ->execute('getRows');

        $aAppliedJob = array();

        foreach ($aAppliedJobSql as $value) {
            $aAppliedJob[] = $value['job_id'];
        }


        $aCategoryAppliedSql = phpfox::getLib("database")->select('jcd.category_id')
            ->from(phpfox::getT('jobposting_application'), 'ja')
            ->leftJoin(phpfox::getT('jobposting_job'), 'jj', 'ja.job_id = jj.job_id')
            ->leftJoin(phpfox::getT('jobposting_category_data'), 'jcd', 'jcd.company_id = jj.company_id')
            ->where('ja.user_id=' . phpfox::getUserId())
            ->execute('getRows');

        $aCategoryApplied = array();

        foreach ($aCategoryAppliedSql as $value) {
            $aCategoryApplied[] = $value['category_id'];
        }

        $aJobSame = array();
        if (!empty($aAppliedJob)) {
            $aJobSameSql = phpfox::getLib("database")->select('jj.job_id')
                ->from(phpfox::getT('jobposting_job'), 'jj')
                ->leftJoin(phpfox::getT('jobposting_category_data'), 'jcd', 'jcd.company_id = jj.company_id')
                ->where(" jcd.category_id IN (" . implode($aCategoryApplied, ',') . ")")
                ->execute('getRows');


            foreach ($aJobSameSql as $value) {
                $aJobSame[] = $value['job_id'];
            }

            $aJobSame = array_diff($aJobSame, $aAppliedJob);
        }

        $queryJobs = phpfox::getLib("database")->select('*')
            ->from(phpfox::getT('jobposting_job'));

        if (!empty($aJobSame)) {
            $aJobs = $queryJobs->where("job_id IN  (" . implode($aJobSame, ',') . ') AND is_approved = 1')
                ->order('time_stamp DESC')
                ->execute('getRows');
        } else {

            $aJobs = $queryJobs->order('time_stamp DESC')
                ->execute('getRows');
        }


        foreach ($aJobs as $key => &$aJob) {

            $aJob['module_id'] = 'job_you_may_like';
            $aJob['url'] = Phpfox::getLib('url')->makeUrl('jobposting', $aJob['job_id']);
            $aJob['create'] = '<p>' . _p('suggestion.added_by_name_on_time', array('name' => $this->getUserLink($aJob['user_id']), 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aJob['time_stamp']))) . '</p>';
            $aJob['info'] = Phpfox::getService('suggestion.url')->makeLink($aJob['url'], Phpfox::getLib('phpfox.parse.output')->shorten($aJob['title'], 70, '...'));


            $object = $this->getObject($aJob['job_id'], 'jobposting');
            if (isset($object['image_path']) && $object['image_path'] != '') {
                $aImgs = $this->parserData($object['server_id'], 'jobposting/' . $object['image_path'], 'core.url_pic');
                $img = $this->getObjectAvatar($aImgs);
                $aJob['avatar'] = '<a href="' . $aJob['url'] . '">' . $img . '</a>';
            } else {
                $aJob['avatar'] = '<a href="' . $aJob['url'] . '"><img src="' . Phpfox::getParam('core.path') . '/module/suggestion/static/image/no_photo.png" alt="" height="75" width="75" ></a>';
            }

            $aJob['suggestion_id'] = '';
            $aJob['suggest'] = '';
            $aJob['friend_user_id'] = Phpfox::getUserId();
            $aJob['friend_friend_user_id'] = $aJob['job_id'];
            $aJob['accept'] = _p('suggestion.apply_job');
            $aJob['message'] = '';
        }

        $total = count($aJobs);

        $this->_countJobYouMayLike = $total;

        if ($offset != -1) {
            $aJobs = array_slice($aJobs, $offset, Phpfox::getParam('suggestion.number_item_on_other_block'));
        }/*------limit and random---*/

        return $aJobs;


    }


    /*
     * get suggestion list of UserID to iFriendId
     */

    public function getSuggestionListForModule($friend_user_id, $sModule = '')
    {
        $where = ' module_id = "' . $sModule . '" AND friend_user_id ="' . $friend_user_id . '"';
        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('suggestion'), 's')
            ->where($where)
            ->execute('getRows');

        return $aRows;
    }


    public function getUserLink($iUserId, $_blankpage = true)
    {

        $aUser = Phpfox::getService('user')->getUser($iUserId, 'u.full_name, u.user_name');
        if ($_blankpage == true) {
            $target = "target='_blank'";
        } else
            $target = "";
        $sLink = "";
        try {
            $sLink = Phpfox::permalink($aUser['user_name'], null);
        } catch (exception $e) {
            //var_dump($iUserId);
            //die();
        }
        return "<a href='" . $sLink . "' " . $target . ">" . $aUser['full_name'] . "</a>";
    }

    public function getUserAvatar($iUserId, $_blankpage = true)
    {
        $aUser = Phpfox::getService('user')->getUser($iUserId);

        $paramImage = array();
        $paramImage['user'] = $aUser;
        $paramImage['path'] = 'core.url_user';
        $paramImage['file'] = $aUser['user_image'];
        $paramImage['suffix'] = '_50_square';
        $paramImage['max_width'] = '50';
        $paramImage['max_height'] = '50';
        $imageHTML = Phpfox::getLib('phpfox.image.helper')->display($paramImage);

        return $imageHTML;
    }

    public function getPhotoAvatar($object)
    {
        $paramImage = array();
        $paramImage['server_id'] = $object['server_id'];
        $paramImage['file'] = $object['file'];
        $paramImage['max_width'] = $object['max_width'];
        $paramImage['max_height'] = $object['max_height'];
        $paramImage['path'] = $object['path'];

        $paramImage['suffix'] = "_500";
        $imageHTML = Phpfox::getLib('phpfox.image.helper')->display($paramImage);
        return $imageHTML;
    }

    public function getObjectAvatar($object)
    {
        $paramImage = array();
        $paramImage['server_id'] = $object['server_id'];
        $paramImage['file'] = $object['file'];
        $paramImage['max_width'] = $object['max_width'];
        $paramImage['max_height'] = $object['max_height'];
        $paramImage['path'] = $object['path'];

        $paramImage['suffix'] = "";

        $imageHTML = Phpfox::getLib('phpfox.image.helper')->display($paramImage);
        return $imageHTML;
    }

    public function parserData($server_id, $file, $path)
    {
        $paramImage = array();
        $paramImage['server_id'] = $server_id;
        $paramImage['file'] = $file;
        $paramImage['path'] = $path;
        $paramImage['max_width'] = 75;
        $paramImage['max_height'] = 75;
        return $paramImage;
    }

    public function getObject($itemId, $type)
    {
        switch ($type) {

            case 'pages':
                $object = Phpfox::getService($type)->getForView($itemId);
                break;

            case 'poll':
                $object = Phpfox::getService($type)->getPollById($itemId);

                break;
            case 'album':
                $object = Phpfox::getService('photo.album')->get('p.album_id = ' . $itemId . ' AND p.is_cover = 1', 'p.photo_id');
                $object = isset($object[1][0]) ? $object[1][0] : null;
                break;
            case 'photo':
                $object = Phpfox::getService('photo')->getForEdit($itemId);
                break;
            case 'advancedalbum':
                $object = Phpfox::getService('advancedphoto.album')->get('p.album_id = ' . $itemId . ' AND p.is_cover = 1', 'p.photo_id');
                $object = isset($object[1][0]) ? $object[1][0] : null;
                break;
            case 'advancedphoto':
                $object = Phpfox::getService('advancedphoto')->getForEdit($itemId);
                break;
            case 'jobposting':
                $aJob = Phpfox::getService('jobposting.job')->getJobByJobId($itemId);
                $object = Phpfox::getService('jobposting.company')->getForEdit($aJob['company_id']);
                break;

            case 'fevent':
                $object = Phpfox::getService($type)->getEventByID($itemId);
                break;
            case 'event':
                $object = Phpfox::getService($type)->getEvent($itemId);
                break;
            case 'coupon':
                $object = $this->getCouponReminder($itemId);
                break;
            case 'ultimatevideo_video':
                $object = Phpfox::getService('ultimatevideo')->getVideoForEdit($itemId);
                break;
            case 'ultimatevideo_playlist':
                $object = Phpfox::getService('ultimatevideo.playlist')->getForEdit($itemId);
                break;
            case 'ynsocialstore_store':
                $object = Phpfox::getService('ynsocialstore')->getStoreForEdit($itemId);
                break;
            case 'ynsocialstore_product':
                $object = Phpfox::getService('ynsocialstore.product')->getProductForEdit($itemId);
                break;
            case 'ynblog':
                $object = Phpfox::getService('ynblog.blog')->getBlog($itemId);
                break;
            case 'blog':
                $object = Phpfox::getService('blog')->getBlog($itemId);
                break;
            case 'quiz':
                $object = Phpfox::getService('quiz')->getQuizById($itemId);
                break;
            case 'contest':
                $object = Phpfox::getService('contest.contest')->getContestById($itemId);
                break;
            case 'petition':
                $object = Phpfox::getService('petition.petition')->getPetition($itemId);
                break;
            case 'ynsocialstore':
                $object = Phpfox::getService('ynsocialstore')->getQuickStoreById($itemId);
                break;
            case 'marketplace':
                $object = Phpfox::getService('marketplace')->getListing($itemId);
                break;
            case 'videochannel':
                $object = Phpfox::getService('videochannel')->getVideo($itemId);
                break;
            default :
                $object = Phpfox::getService($type)->getForEdit($itemId);

                break;
        }

        if (!isset($object) || $object == null)
            return null;


        return $object;
    }


    public function getSuggestionPopup($_notification)
    {
        $aRows = phpfox::getLib("database")->select('*')
            ->from(phpfox::getT('suggestion_setting'))
            ->where('user_id=' . phpfox::getUserId() . ' and user_notification="' . $_notification . '"')
            ->execute('getSlaveRows');
        return $aRows;

    }

    public function isAllowSuggestionPopup()
    {
        //$aRow = Phpfox::getService('user.privacy')->get();
        //return !isset($aRow[0]['notification']['suggestion.enable_system_suggestion']);
        if (count($this->getSuggestionPopup("suggestion.enable_system_suggestion")) > 0) {
            return 0;
        }
        return 1;
    }

    public function isAllowContentSuggestionPopup()
    {
        if (count($this->getSuggestionPopup("suggestion.enable_content_suggestion_popup")) > 0) {
            return 0;
        }
        return 1;
    }

    public function isAllowRecommendationPopup()
    {
        //$aRow = Phpfox::getService('user.privacy')->get();
        //return !isset($aRow[0]['notification']['suggestion.enable_system_recommendation']);
        if (count($this->getSuggestionPopup("suggestion.enable_system_recommendation")) > 0) {
            return 0;
        }
        return 1;
    }

    public function getUser($iUserId)
    {
        $aUser = $this->database()
            ->select(Phpfox::getUserField())
            ->from(Phpfox::getT('user'), 'u')
            ->where('u.user_id = ' . (int)$iUserId)
            ->execute('getRow');
        return $aUser;
    }

    public function getMutualFriends($iUserId, $iLimit = 0)
    {
        if ($iUserId > 0 && (int)Phpfox::getUserId() > 0) {
            $aRows = $this->database()->select('f.friend_user_id as id')
                ->from(Phpfox::getT('friend'), 'f')
                ->innerJoin(Phpfox::getT('friend'), 'f2', 'f.user_id = ' . $iUserId . ' AND f2.friend_user_id = f.friend_user_id AND f2.user_id = ' . Phpfox::getUserId());

            if ($iLimit > 0) {
                $aRows = $this->database()
                    ->limit($iLimit)
                    ->execute('getRows');
            } else {
                $aRows = $this->database()
                    ->execute('getRows');
            }
            return $aRows;
        }
        return array();
    }

    public function getSuggestionDetailByUserId($iUserId, $iFriendId, $sModule, $iItemid)
    {
        $iUserId = (int)$iUserId;
        $iFriendId = (int)$iFriendId;
        $iItemid = (int)$iItemid;
        $sModule = $sModule . '';

        $aRow = $this->database()->select('*')
            ->from(Phpfox::getT('suggestion'), 's')
            ->where('s.user_id = ' . $iUserId . ' AND s.module_id="' . $sModule . '" AND s.item_id=' . $iItemid . ' AND s.friend_user_id = ' . $iFriendId)
            ->order('s.time_stamp DESC')
            ->execute('getRow');

        return $aRow;
    }

    public function isUserViewSuggestion($iFriendId, $sModule, $iItemid, $sTableName = 'event')
    {

        $iFriendId = (int)$iFriendId;
        $iItemid = (int)$iItemid;
        $sModule = $sModule . '';

        $aRow = $this->database()->select('s.suggestion_id')
            ->from(Phpfox::getT('suggestion'), 's')
            ->where('s.module_id="' . $sModule . '" AND s.item_id=' . $iItemid . ' AND s.friend_user_id = ' . $iFriendId . ' AND processed = 1')
            ->execute('getRow');
        /*check if user like this item*/
        $aType = explode('_', $sModule);
        $sType = $aType[1];

        $aLike = $this->database()->select('*')
            ->from(Phpfox::getT('like'), 'l')
            ->where('l.type_id = \'' . $this->database()->escape($sType) . '\' AND l.item_id = ' . (int)$iItemid . ' AND user_id = ' . (int)$iFriendId)
            ->execute('getRow');

        /*check if user join events*/
        if ($sModule == 'suggestion_event' || $sModule == 'suggestion_fevent') {
            $aEventInvite = $this->database()
                ->select('invite_id')
                ->from(Phpfox::getT($sTableName . '_invite'), 'e')
                ->where('e.invited_user_id = ' . (int)$iFriendId . ' AND event_id = ' . (int)$iItemid)
                ->execute('getRow');
        } else {
            $aEventInvite = array();
        }
        if (count($aRow) > 0 || count($aLike) > 0 || count($aEventInvite) > 0) return true;
        return false;
    }

    /*
     * get unique firend user id, whom has been suggested an item
     */

    public function getSuggestionListByUserId($iUserId, $iItemId, $sModule = '')
    {
        if ((int)$iUserId == 0 || $sModule == '' || (int)$iItemId == 0)
            return null;
        $aRows = $this->database()->select('DISTINCT s.friend_user_id, s.suggestion_id')
            ->from(Phpfox::getT('suggestion'), 's')
            ->where('s.user_id = ' . $iUserId . ' AND module_id="' . $sModule . '" AND processed<2 AND item_id = ' . $iItemId)
            ->execute('getRows');
        return $aRows;
    }

    /*
     * get suggestion of UserID to iFriendId and notification ID
     */

    public function getSuggestionDetailByNotification($iUserId, $iFriendId, $sModule = '', $iNotificationId = 0)
    {

        $where[] = 's.user_id =' . $iUserId;
        $where[] = 's.friend_user_id=' . $iFriendId;
        $where[] = 'sn.notification_id=' . $iNotificationId;

        $where = implode(' AND ', $where);

        if ($sModule != '') {
            $where .= ' AND s.module_id ="' . $sModule . '"';
        }
        $aRows = $this->database()->select('s.*')
            ->from(Phpfox::getT('suggestion'), 's')
            ->join(Phpfox::getT('suggestion_notification'), 'sn', 'sn.suggestion_id = s.suggestion_id')
            ->where($where)
            ->order('time_stamp DESC')
            ->execute('getRow');

        return $aRows;
    }

    /*
     * get suggestion of UserID to iFriendId
     */

    public function getSuggestionDetail($iUserId, $iFriendId, $sModule = '')
    {

        $where[] = 's.user_id =' . $iUserId;
        $where[] = 'friend_user_id=' . $iFriendId;

        $where = implode(' AND ', $where);

        if ($sModule != '') {
            $where .= ' AND module_id ="' . $sModule . '"';
        }
        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('suggestion'), 's')
            ->where($where)
            ->order('time_stamp DESC')
            ->execute('getRow');

        return $aRows;
    }

    /*
     * get suggestion list of UserID to iFriendId
     */

    public function getSuggestionList($iUserId, $iFriendId, $sModule = '')
    {
        $where = 's.user_id =' . $iUserId . ' AND friend_user_id=' . $iFriendId;
        if ($sModule != '') {
            $where .= ' AND module_id ="' . $sModule . '"';
        }

        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('suggestion'), 's')
            ->where($where)
            ->execute('getRows');

        return $aRows;
    }


    /*
     * Get list of friends of iUserId, whom pending list in peding, not process
     * 
     */

    public function getFriendsPendingList($iUserId, $iLimit = 0, $sModule = '')
    {
        if ($iUserId > 0 && (int)Phpfox::getUserId() > 0) {
            $aRows = $this->database()->select('DISTINCT(fr.user_id) as user_id')
                ->from(Phpfox::getT('friend_request'), 'fr')
                ->where('fr.friend_user_id=' . $iUserId . ' AND is_ignore = 0');
            if ($iLimit > 0) {
                $aRows = $this->database()
                    ->limit($iLimit)
                    ->execute('getRows');
            } else {
                $aRows = $this->database()
                    ->execute('getRows');
            }
            return $aRows;
        } else {
            return null;
        }
    }

    /*
 * Get list of friends of iUserId, whom pending list in peding, not process
 *
 */

    public function getWhoRequestFriend($iUserId, $iLimit = 0, $sModule = '')
    {
        if ($iUserId > 0 && (int)Phpfox::getUserId() > 0) {
            $aRows = $this->database()->select('DISTINCT(fr.friend_user_id) as friend_user_id')
                ->from(Phpfox::getT('friend_request'), 'fr')
                ->where('fr.user_id=' . $iUserId . ' AND is_ignore = 0');
            if ($iLimit > 0) {
                $aRows = $this->database()
                    ->limit($iLimit)
                    ->execute('getRows');
            } else {
                $aRows = $this->database()
                    ->execute('getRows');
            }
            return $aRows;
        } else {
            return null;
        }
    }


    /*
     * get random total friends of iUserId
     */

    public function getFriendsOfUser($iUserId, $iLimit = null)
    {
        $aFriends = $this->database()->select('f.friend_id, rand() rnd, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('friend'), 'f')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_Id')
            ->where('u.view_id=0 and f.user_id = ' . (int)$iUserId . ' AND user_name != ""')
            ->order('rnd asc')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        return $aFriends;
    }

    /*
     * Get total friends of iUserId who has total friends lower than total friends set by admin
     * 
     */

    public function getLessFriendsList($iUserId, $iLimit = null)
    {

        $iLessFriends = (int)Phpfox::getUserParam('suggestion.total_less_friends');
        $iRandomFriends = (int)Phpfox::getUserParam('suggestion.number_of_entries_display_in_blocks');


        $aFriendsList = Phpfox::getService('suggestion')->getFriendsOfUser(Phpfox::getUserId());

        $iTotalFriends = 0;
        $result = array();

        //check if total friends to display in block greater than 0
        if ($iLessFriends > 0) {
            foreach ($aFriendsList as &$aFriend) {
                $iFriendId = $aFriend['user_id'];

                //                    $aFriendDetail = Phpfox::getService('suggestion')->getSuggestionDetail($iUserId, $iFriendId);
                //                    if (count($aFriendDetail)>0) continue;

                $_aFriend = Phpfox::getService('suggestion')->getFriendsOfUser($iFriendId);

                if (count($_aFriend) < $iLessFriends) {

                    $aFriend['total_friends'] = count($_aFriend);

                    //get user avatar
                    $iUserId = $aFriend['user_id'];
                    $aUser = Phpfox::getService('suggestion')->getUser($iUserId);
                    $aUser['suffix'] = '_50_square';
                    $aUser['max_width'] = '50';
                    $aUser['max_height'] = '50';
                    $aUser['user'] = $aUser;
                    $img = '<span class="thumb">' . Phpfox::getLib('phpfox.image.helper')->display($aUser) . '</span>';
                    $aFriend['img'] = $img;

                    $result[] = $aFriend;
                    $iTotalFriends++;
                }

                if ($iTotalFriends >= $iRandomFriends)
                    break;
            }
        }
        if (isset($result) && count($result) > 0)
            return $result;
        return null;
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
        if ($sPlugin = Phpfox_Plugin::get('suggestion.service_suggestion__call')) {
            eval($sPlugin);
            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function getAllCoutObj()
    {
        $sSupportModule = Phpfox::getUserParam('suggestion.support_module');
        $rSort = array();

        if ($sSupportModule != '') {
            $sSupportModule = explode(',', $sSupportModule);


            foreach ($sSupportModule as $sModule) {
                if (Phpfox::isModule($sModule)) {
                    $queryModule = 'suggestion_' . $sModule;

                    $where_friend = 's.processed = 0 AND s.friend_user_id = ' . Phpfox::getUserId() . ' AND s.module_id = "' . $queryModule . '"';

                    $iCnt_friend = Phpfox::getLib('phpfox.database')
                        ->select('COUNT(*)')
                        ->from(phpfox::getT('suggestion'), 's')->leftJoin(phpfox::getT('user'), 'u', 's.user_id = u.user_id')->where($where_friend)->execute('getSlaveField');

                    $this->_countObj['friends'][$sModule] = $iCnt_friend;


                    $where_my = 's.user_id = ' . Phpfox::getUserId() . ' AND s.module_id = "' . $queryModule . '"';

                    $iCnt_my = Phpfox::getLib('phpfox.database')
                        ->select('COUNT(*)')
                        ->from(phpfox::getT('suggestion'), 's')->leftJoin(phpfox::getT('user'), 'u', 's.user_id = u.user_id')->where($where_my)->execute('getSlaveField');

                    $this->_countObj['my'][$sModule] = $iCnt_my;

                }
            }
        }

        return $this->_countObj;

    }

    public function getAllSuggestion($params = array())
    {
        $aRows = array();
        if (!isset($params['iPage'])) {
            $params['iPage'] = 1;
        }
        switch ($params['sView']) {
            case 'friends':
                foreach ($params['rSort'] as $module_id) {
                    if (Phpfox::isModule(substr($module_id, 11, strlen($module_id)))) {
                        $sql = '
                            (SELECT s.*, u.*
                            FROM ' . phpFox::getT("suggestion") . ' AS s
                            LEFT JOIN ' . phpFox::getT("user") . ' AS u ON (s.user_id = u.user_id)
                            WHERE s.processed = 0 AND s.friend_user_id = ' . Phpfox::getUserId() . ' AND s.module_id = \'' . $module_id . '\'
                            ORDER BY s.suggestion_id DESC
                            LIMIT ' . $params['limit'] . ')';
                        $aRows[$params['sView'] . Phpfox::getService('suggestion')->convertModule($module_id)] = $this->database()->getSlaveRows($sql);
                    }
                }
                break;

            case 'friendsfriend':
            case 'friendsphoto':
            case 'friendsadvancedphoto':
            case 'friendsblog':
            case 'friendsforum':
            case 'friendspages':
            case 'friendspoll':
            case 'friendsvideo':
            case 'friendsvideochannel':
            case 'friendsquiz':
            case 'friendsevent':
            case 'friendsfevent':
            case 'friendsmusic':
            case 'friendsmusicsharing':
            case 'friendsmusicstore':
            case 'friendsmarketplace':
            case 'friendsadvmarketplace':
            case 'friendsdocument':
            case 'friendscontest':
            case 'friendsfundraising':
            case 'friendscoupon':
            case 'friendsjobposting':
            case 'friendspetition':
            case 'friendsgettingstarted':
            case 'friendsultimatevideo':
            case 'friendsynsocialstore':
            case 'friendsynblog':
                $where = 's.processed = 0 AND s.friend_user_id = ' . Phpfox::getUserId() . ' AND s.module_id = \'suggestion_' . substr($params['sView'], 7, strlen($params['sView'])) . '\'';
                $iCnt = Phpfox::getLib('phpfox.database')
                    ->select('COUNT(*)')
                    ->from(phpfox::getT('suggestion'), 's')
                    ->leftJoin(phpfox::getT('user'), 'u', 's.user_id = u.user_id')
                    ->where($where)
                    ->execute('getSlaveField');
                $this->_countObject = $iCnt;
                $this->_countObj[$params['sView']] = $iCnt;
                $aRows[$params['sView']] =
                    $this->database()
                        ->select('s.*, u.*')
                        ->from(phpfox::getT('suggestion'), 's')
                        ->leftJoin(phpfox::getT('user'), 'u', 's.user_id = u.user_id')
                        ->where($where)
                        ->order("s.suggestion_id DESC")
                        ->limit($params['iPage'], $params['limit'], $iCnt)
                        ->execute('getSlaveRows');
                if ($params['iPage'] == 2 && ceil($iCnt / $params['limit']) == 1) {
                    $aRows[$params['sView']] = array();
                }
                break;

            case 'my':
                foreach ($params['rSort'] as $module_id) {
                    if (Phpfox::isModule(substr($module_id, 11, strlen($module_id)))) {
                        $sql = '
                            (SELECT s.*, u.*
                            FROM ' . Phpfox::getT('suggestion') . ' AS s
                            LEFT JOIN ' . Phpfox::getT('user') . ' AS u ON (s.user_id = u.user_id)
                            WHERE s.user_id = ' . Phpfox::getUserId() . ' AND s.module_id = \'' . $module_id . '\'
                            ORDER BY s.suggestion_id DESC
                            LIMIT ' . $params['limit'] . ')';
                        $aRows[$params['sView'] . Phpfox::getService('suggestion')->convertModule($module_id)] = $this->database()->getSlaveRows($sql);
                    }
                }
                break;
            case 'myfriend':
            case 'myphoto':
            case 'myadvancedphoto':
            case 'myblog':
            case 'myforum':
            case 'mypages':
            case 'mypoll':
            case 'myvideo':
            case 'myvideochannel':
            case 'myquiz':
            case 'myevent':
            case 'myfevent':
            case 'mymusic':
            case 'mymusicsharing':
            case 'mymusicstore':
            case 'mymarketplace':
            case 'myadvmarketplace':
            case 'mydocument':
            case 'mycontest':
            case 'myfundraising':
            case 'mycoupon':
            case 'myjobposting':
            case 'mypetition':
            case 'mygettingstarted':
            case 'myultimatevideo':
            case 'myynsocialstore':
            case 'myynblog':
                $where = 's.user_id = ' . Phpfox::getUserId() . ' AND s.module_id = \'suggestion_' . substr($params['sView'], 2, strlen($params['sView'])) . '\'';
                $iCnt = Phpfox::getLib('phpfox.database')
                    ->select('COUNT(*)')
                    ->from(phpfox::getT('suggestion'), 's')
                    ->leftJoin(phpfox::getT('user'), 'u', 's.user_id = u.user_id')
                    ->where($where)->execute('getSlaveField');
                $this->_countObject = $iCnt;


                $aRows[$params['sView']] = $this->database()
                    ->select('s.*, u.*')
                    ->from(phpfox::getT('suggestion'), 's')
                    ->leftJoin(phpfox::getT('user'), 'u', 's.user_id = u.user_id')
                    ->where($where)->order("s.suggestion_id DESC")
                    ->limit($params['iPage'], $params['limit'], $iCnt)->execute('getSlaveRows');

                if ($params['iPage'] > 1 && ceil($iCnt / $params['limit']) == 1) {
                    $aRows[$params['sView']] = array();
                }

                break;
        }

        if (!$aRows)
            return null;

        // remove friend_user_id not exist
        $aRowNew = array();
        foreach ($aRows as $iKey => &$aItem) {
            $index = -1;
            $indexNew = 0;
            foreach ($aItem as &$aRow) {
                $index++;
                $removeRow = false;

                // MY suggest FRIEND . then FRIEND account is deleted
                $aUser = Phpfox::getService('user')->getUser($aRow['friend_user_id'], 'u.full_name, u.user_name');
                if (!isset($aUser["user_name"])) {
                    $removeRow = true;
                    // delete suggestion
                    $iSuggestId = $aRow['suggestion_id'];
                    if ($aRow['friend_user_id'] > 0)
                        Phpfox::getService('suggestion.	process')->deleteSuggestion($iSuggestId);
                }

                // FRIEND suggest MY . then FRIEND account is deleted
                $aUser = Phpfox::getService('user')->getUser($aRow['user_id'], 'u.full_name, u.user_name');
                if (!isset($aUser["user_name"])) {
                    $removeRow = true;
                    // delete suggestion
                    $iSuggestId = $aRow['suggestion_id'];
                    if ($aRow['user_id'] > 0)
                        Phpfox::getService('suggestion.process')->deleteSuggestion($iSuggestId);
                }

                if (!$removeRow) {
                    $aRowNew[$iKey][$indexNew] = $aRows[$iKey][$index];
                    $indexNew++;
                }

            }

        }
        $aRows = $this->getItemDetailMore($aRowNew);
        return $aRows;

    }

    public function getItemDetailMore($aRows)
    {
        $sView = $this->request()->get('view', 'incoming');
        $aMainMenus = Phpfox::getLib('template')->getMenu('main');
        foreach ($aRows as $iKey => &$aItem) {
            foreach ($aItem as &$aRow) {
                /*
                 *
                 * fix for module not in friend. merge friend_firend_user_id = item_id
                 * merge ID of current item instead of friend_user_id of friend.
                 *
                 */
                if (!isset($aRow['friend_friend_user_id'])) {
                    $aRow['friend_friend_user_id'] = $aRow['item_id'];
                }
                $sModule = Phpfox::getService('suggestion')->convertModule($aRow['module_id']);

                $sLink = $aRow['url'] = urldecode($aRow['url']);
                $aRow['header_name'] = ucfirst($sModule);


                foreach ($aMainMenus as $menu) {
                    if ($sModule == $menu['module']) {
                        $aRow['header_name'] = _p($sModule . '.' . $menu['var_name']);
                        break;
                    }
                }

                switch ($sModule) {
                    case 'friend':
                        if (!isset($aRow['item_id'])) break;

                        $item = Phpfox::getService('user')->get($aRow['item_id']);
                        $url = Phpfox::getLib('url')->makeUrl($item['user_name'], array('friend', 'mutual'));
                        list($iTotal, $aMultualFriend) = Phpfox::getService('friend')->getMutualFriends($aRow['item_id']);

                        if ($iTotal == 1) {
                            $phare = _p('friend.1_friend_in_common');
                            $aRow['create'] = '<div class="p_bottom_5" style="position:relative;"><a href="' . $url . '">' . $phare . '</a></div>';
                        } elseif ($iTotal == 0) {
                            $aRow['create'] = '';
                        } else {
                            $phare = _p('friend.total_friends_in_common', array('total' => $iTotal));
                            $aRow['create'] = '<div class="p_bottom_5" style="position:relative;"><a href="' . $url . '">' . $phare . '</a></div>';
                        }

                        if (substr($iKey, 0, 2) != 'my') {
                            $aRow['accept'] = _p('suggestion.accept');
                            $aRow['ignore'] = _p('suggestion.ignore');
                        } else {
                            $aRow['delete'] = _p('suggestion.delete');
                        }
                        $aRow['avatar'] = Phpfox::getService('suggestion')->getUserAvatar($aRow['item_id']);
                        $aRow['info'] = Phpfox::getService('suggestion')->getUserLink($aRow['item_id']);
                        if (substr($iKey, 0, 2) != 'my') {
                            $aRow['suggest'] = '<span>' . _p('suggestion.suggested_by') . ' ' . Phpfox::getService('suggestion')->getUserLink($aRow['user_id']) . '</span>';
                        } else {
                            $aRow['suggest'] = '<span>' . _p('suggestion.suggested_to') . ' ' . Phpfox::getService('suggestion')->getUserLink($aRow['friend_user_id']) . '</span>';
                        }
                        break;
                    case 'photo':
                        $object = array();
                        if (strpos($sLink, '/photo/album/') !== false) {
                            $object = $this->getObject($aRow['item_id'], 'album');
                        } else {
                            $object = $this->getObject($aRow['item_id'], 'photo');
                        }

                        $aRow['create'] = '<p>' . _p('suggestion.added_by_name_on_time', array('name' => $this->getUserLink($object['user_id']), 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $object['time_stamp']))) . '</p>';

                        if (isset($object['destination']) && $object['destination'] != '') {
                            $aImgs = $this->parserData($object['server_id'], $object['destination'], 'photo.url_photo');
                            $img = $this->getPhotoAvatar($aImgs);
                            $aRow['avatar'] = '<a href="' . $sLink . '">' . $img . '</a>';
                        } else {
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . Phpfox::getParam('core.path') . '/module/suggestion/static/image/no_photo.png" alt="" height="75" width="75" ></a>';
                        }

                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';
                        break;


                    case 'advancedphoto':
                        $object = array();
                        if (strpos($sLink, '/advancedphoto/album/') !== false) {
                            $object = $this->getObject($aRow['item_id'], 'advancedalbum');
                        } else {
                            $object = $this->getObject($aRow['item_id'], 'advancedphoto');
                        }

                        $aRow['create'] = '<p>' . _p('suggestion.added_by_name_on_time', array('name' => $this->getUserLink($object['user_id']), 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $object['time_stamp']))) . '</p>';
                        if (isset($object['destination']) && $object['destination'] != '') {
                            $aImgs = $this->parserData($object['server_id'], $object['destination'], 'photo.url_photo');
                            $img = $this->getObjectAvatar($aImgs);
                            $aRow['avatar'] = '<a href="' . $sLink . '">' . $img . '</a>';
                        } else {
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . Phpfox::getParam('core.path') . '/module/suggestion/static/image/no_photo.png" alt="" height="75" width="75" ></a>';
                        }
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';
                        break;

                    case 'jobposting':
                        $object = $this->getObject($aRow['item_id'], 'jobposting');
                        if (isset($object['image_path']) && $object['image_path'] != '') {
                            $aImgs = $this->parserData($object['server_id'], 'jobposting/' . $object['image_path'], 'core.url_pic');
                            $img = $this->getObjectAvatar($aImgs);
                            $aRow['avatar'] = '<a href="' . $sLink . '">' . $img . '</a>';
                        } else {
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . Phpfox::getParam('core.path') . '/module/suggestion/static/image/no_photo.png" alt="" height="75" width="75" ></a>';
                        }
                        $aRow['create'] = '<p>' . _p('suggestion.added_by_name_on_time', array('name' => $this->getUserLink($object['user_id']), 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $object['time_stamp']))) . '</p>';
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';
                        break;

                    case 'blog':

                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'blog');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/blog.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => 'blog/' . $object['image_path'],
                                        'suffix' => '_1024',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }
                        break;

                    case 'forum':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';
                        break;
                    case 'pages':

                        $sLink = Phpfox::getLib('url')->makeUrl('pages', $aRow['item_id']);

                        $aRow['info'] = "<a href='" . $sLink . "'>" . Phpfox::getLib('phpfox.parse.output')->shorten($aRow['title'], 70, '...') . "</a>";

                        $object = $this->getObject($aRow['item_id'], $sModule);
                        if (isset($object['image_path']) && $object['image_path'] != '') {
                            $aImgs = $this->parserData($object['image_server_id'], $object['image_path'], 'core.url_user');
                            $img = $this->getObjectAvatar($aImgs);
                            $aRow['avatar'] = '<a href="' . $sLink . '">' . $img . '</a>';
                        } else {
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . Phpfox::getParam('core.path') . '/module/suggestion/static/image/pages.png" alt="" height="75" width="75" ></a>';
                        }
                        $aRow['create'] = '<p>' . _p('suggestion.added_by_name_on_time', array('name' => $this->getUserLink($object['user_id']), 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $object['time_stamp']))) . '</p>';
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (substr($iKey, 0, 2) != 'my') {
                            $aRow['accept'] = _p('suggestion.like');
                        }

                        break;
                    case 'poll':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_rate';
                        $sPhraseAccept = 'suggestion.rate';
                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'poll');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/poll.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => 'poll/' . $object['image_path'],
                                        'suffix' => '_500',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }
                        break;
                    case 'video':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';
                        break;
                    case 'videochannel':

                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'videochannel');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/videochannel.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => $object['image_path'],
                                        'suffix' => '_120',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }

                        break;
                    case 'ynsocialstore':

                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'ynsocialstore');
                            if (empty($object['logo_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/ynsocialstore.png';
                            } elseif (strpos($object['logo_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => $object['logo_path'],
                                        'suffix' => '',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }

                        break;
                    case 'event':
                        $object = array();
                        if (strpos($sLink, '/fevent/') !== false) {
                            $object = $this->getObject($aRow['item_id'], 'fevent');
                        } else {
                            $object = $this->getObject($aRow['item_id'], 'event');
                        }
                        $aRow['create'] = '<p>' . _p('suggestion.added_by_name_on_time', array('name' => $this->getUserLink($object['user_id']), 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $object['time_stamp']))) . '</p>';
                        if (isset($object['image_path']) && $object['image_path'] != '') {
                            $aImgs = $this->parserData($object['server_id'], $object['image_path'], 'event.url_image');
                            $img = $this->getObjectAvatar($aImgs);
                            $aRow['avatar'] = '<a href="' . $sLink . '">' . $img . '</a>';
                        } else {
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . Phpfox::getParam('core.path') . '/module/suggestion/static/image/event.png" alt="" height="75" width="75" ></a>';
                        }

                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_join';
                        $sPhraseAccept = 'suggestion.join';
                        break;
                    case 'fevent':

                        $object = array();
                        if (strpos($sLink, '/fevent/') !== false) {
                            $object = $this->getObject($aRow['item_id'], 'fevent');
                        } else {
                            $object = $this->getObject($aRow['item_id'], 'event');
                        }

                        $object['path'] = 'event.url_image';
                        $aRow['create'] = '<p>' . _p('suggestion.added_by_name_on_time', array('name' => $this->getUserLink($object['user_id']), 'time' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $object['time_stamp']))) . '</p>';
                        if (isset($object['image_path']) && $object['image_path'] != '') {
                            $aImgs = $this->parserData($object['server_id'], $object['image_path'], 'event.url_image');
                            $img = $this->getObjectAvatar($aImgs);
                            $aRow['avatar'] = '<a href="' . $sLink . '">' . $img . '</a>';
                        } else {
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . Phpfox::getParam('core.path') . '/module/suggestion/static/image/no_photo.png" alt="" height="75" width="75" ></a>';
                        }

                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_join';
                        $sPhraseAccept = 'suggestion.join';
                        break;
                    case 'marketplace':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_take';
                        $sPhraseAccept = 'suggestion.take';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'marketplace');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/marketplace.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => 'marketplace/' . $object['image_path'],
                                        'suffix' => '_400',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }
                        break;
                    case 'quiz':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_take';
                        $sPhraseAccept = 'suggestion.take';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'quiz');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/quiz.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => 'quiz/' . $object['image_path'],
                                        'suffix' => '_500',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }
                        break;

                    case 'music':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_listen';
                        $sPhraseAccept = 'suggestion.listen';
                        break;
                    case 'ynblog':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'ynblog');
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . Phpfox::getService('ynblog.helper')->getImagePath($object['image_path'], $object['server_id'], '_grid') . '"></a>';
                        }
                        break;
                    case 'coupon':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'coupon');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/coupon.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => $object['image_path'],
                                        'suffix' => '',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }
                        break;
                    case 'ultimatevideo':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'ultimatevideo_video');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/ultimatevideo.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => $object['image_path'],
                                        'suffix' => '_250',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }
                        break;
                    case 'contest':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'contest');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/contest.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => 'contest/' . $object['image_path'],
                                        'suffix' => '_400',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }
                        break;
                    case 'petition':
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';

                        if (!isset($aRow['avatar'])) {
                            $object = $this->getObject($aRow['item_id'], 'petition');
                            if (empty($object['image_path'])) {
                                $sImage = Phpfox::getParam('core.path_actual') . 'PF.Base/module/suggestion/static/image/petition.png';
                            } elseif (strpos($object['image_path'], 'http') !== false) {
                                // Do nothing
                            } else {
                                $sImage = Phpfox::getLib('image.helper')->display(
                                    [
                                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                                        'path' => 'core.url_pic',
                                        'file' => $object['image_path'],
                                        'suffix' => '_1024',
                                        'return_url' => true,
                                    ]
                                );
                            }
                            $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . $sImage . '"></a>';
                        }
                        break;
                    case 'document':
                    default:
                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                        $sPhraseAccept = 'suggestion.view';
                        break;

                }

                if ($sModule != 'friend') {
                    $sLink = html_entity_decode($aRow['url']);
                    $sLink = urldecode($sLink);
                    if (!isset($aRow['info']))
                        $aRow['info'] = Phpfox::getService('suggestion.url')->makeLink($aRow['url'], Phpfox::getLib('phpfox.parse.output')->shorten($aRow['title'], 70, '...'));

                    if (!isset($aRow['create'])) {
                        $aRow['create'] = '';
                    }

                    if (!isset($aRow['avatar'])) {
                        $aRow['avatar'] = '<a href="' . $sLink . '"><img src="' . Phpfox::getParam('core.path') . "module/suggestion/static/image/" . substr($aRow['module_id'], 11, strlen($aRow['module_id'])) . '.png" height="75" width="75"></a>';
                    }

                    $_sModuleName = $this->getModuleName($sModule);

                    if (substr($iKey, 0, 2) != 'my') {
                        if (!isset($aRow['accept']))
                            $aRow['accept'] = _p('suggestion.view');
                        $aRow['ignore'] = _p('suggestion.ignore');
                        $aRow['suggest'] = '<span>' . _p('suggestion.suggested_by') . ' ' . Phpfox::getService('suggestion')->getUserLink($aRow['user_id']) . '</span>';
                    } else {
                        $aRow['delete'] = _p('suggestion.delete');
                        $aRow['suggest'] = '<span>' . _p('suggestion.suggested_to') . ' ' . Phpfox::getService('suggestion')->getUserLink($aRow['friend_user_id']) . '</span>';
                    }


                }
            }
        }

        return $aRows;
    }

    public function getEventYouMayJoin($params = array())
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('fevent'), 'e')
            ->leftJoin(Phpfox::getT('fevent_category_data'), 'ecat', 'e.event_id = ecat.event_id')
            ->where('e.event_id NOT IN (' . $params['sEventJoin'] . ') AND ( e.user_id IN (' . $params['sOwner'] . ') OR ecat.category_id IN (' . $params['sCategory'] . ') )')
            ->execute('getSlaveField');

        $this->_countObject = $iCnt;

        if ($params['iPage'] > 1 && ceil($iCnt / Phpfox::getParam('suggestion.number_item_on_other_block')) == 1) {
            return array($iCnt, array());
        }

        if ($iCnt) {
            $aEvent = $this->database()->select('e.*')
                ->from(Phpfox::getT('fevent'), 'e')
                ->leftJoin(Phpfox::getT('fevent_category_data'), 'ecat', 'e.event_id = ecat.event_id')
                ->where('e.event_id NOT IN (' . $params['sEventJoin'] . ') AND ( e.user_id IN (' . $params['sOwner'] . ') OR ecat.category_id IN (' . $params['sCategory'] . ') )')
                ->limit($params['iPage'], Phpfox::getParam('suggestion.number_item_on_other_block'), $iCnt)
                ->order('e.event_id DESC')
                ->execute('getSlaveRows');
        }
        return array($iCnt, $aEvent);
    }

    public function getListEventJoin($user_id)
    {
        $aEvent = $this->database()->select('ei.event_id')
            ->from(Phpfox::getT('fevent_invite'), 'ei')
            ->where('ei.user_id = ' . (int)$user_id . ' AND ei.rsvp_id = 1')
            ->order('ei.invite_id DESC')
            ->execute('getSlaveRows');
        $sEventId = '';
        if (count($aEvent) > 0) {
            foreach ($aEvent as $item) {
                $sEventId .= "'" . $item['event_id'] . "',";
            }
            $sEventId = substr($sEventId, 0, strlen($sEventId) - 1);
        }
        return $sEventId;
    }

    public function getListOwner($sEventJoin)
    {
        $aOwner = $this->database()->select('e.user_id')
            ->from(Phpfox::getT('fevent'), 'e')
            ->where('e.event_id IN ( ' . $sEventJoin . ' )')
            ->execute('getSlaveRows');

        $sOwnerId = '';
        if (count($aOwner) > 0) {
            foreach ($aOwner as $item) {
                $sOwnerId .= "'" . $item['user_id'] . "',";
            }
            $sOwnerId = substr($sOwnerId, 0, strlen($sOwnerId) - 1);
        }
        return $sOwnerId;
    }

    public function getListCategory($sEventJoin)
    {
        $aCategory = $this->database()->select('ecat.category_id')
            ->from(Phpfox::getT('fevent_category_data'), 'ecat')
            ->where('ecat.event_id IN ( ' . $sEventJoin . ' )')
            ->execute('getSlaveRows');

        $sCategory = '';
        if (count($aCategory) > 0) {
            foreach ($aCategory as $item) {
                $sCategory .= "'" . $item['category_id'] . "',";
            }
            $sCategory = substr($sCategory, 0, strlen($sCategory) - 1);
        }
        return $sCategory;
    }

    public function getTotalIncomingSuggestion($iUserId)
    {

        $sSupportModule = Phpfox::getUserParam('suggestion.support_module');
        $sSort = '';
        if ($sSupportModule != '') {
            $sSupportModule = explode(',', $sSupportModule);


            foreach ($sSupportModule as $sModule) {
                if (Phpfox::isModule($sModule)) {
                    $sSort .= "'suggestion_" . $sModule . "',";
                }
            }
        }

        $this->_iFriends = $this->countFriendsSuggestion(array('sSort' => substr($sSort, 0, strlen($sSort) - 1)));

        $this->_iMys = $this->countMySuggestion(array('sSort' => substr($sSort, 0, strlen($sSort) - 1)));

        return ($this->_iFriends + $this->_iMys);
    }

    public function countFriendsSuggestion($params)
    {
        $iFriends = 0;
        $sql = "
            SELECT COUNT(*)
            FROM " . Phpfox::getT("suggestion") . " AS s
            LEFT JOIN " . Phpfox::getT("user") . " AS u ON (s.user_id = u.user_id)
            WHERE s.processed = 0 AND s.friend_user_id = " . Phpfox::getUserId() . " AND s.module_id IN (" . $params['sSort'] . ")
            ORDER BY s.suggestion_id DESC";

        $iCnt = $this->database()->getSlaveField($sql);
        if ($iCnt > 0) {
            $iFriends += $iCnt;
        }
        return $iFriends;
    }

    public function countMySuggestion($params)
    {
        $iMys = 0;

        $sql = "
            SELECT COUNT(*)
            FROM " . Phpfox::getT("suggestion") . "  AS s
            LEFT JOIN " . Phpfox::getT("user") . " AS u ON (s.user_id = u.user_id)
            WHERE s.user_id = " . Phpfox::getUserId() . " AND s.module_id IN (" . $params['sSort'] . ")
            ORDER BY s.suggestion_id DESC";


        $iCnt = $this->database()->getSlaveField($sql);
        if ($iCnt > 0) {
            $iMys += $iCnt;
        }
        return $iMys;

    }
}

?>