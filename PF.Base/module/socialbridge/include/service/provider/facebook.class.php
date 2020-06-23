<?php

defined('PHPFOX') or exit('NO DICE!');

require_once __DIR__ . PHPFOX_DS . '..' . PHPFOX_DS . 'libs' . PHPFOX_DS . 'facebook.php';

class Socialbridge_Service_Provider_Facebook extends SocialBridge_Service_Provider_Abstract
{
    protected $_name = 'facebook';

    protected $_appPicture = null;

    public function getAppPicture()
    {
        if (null == $this->_appPicture) {
            $this->_appPicture = Phpfox::getParam('core.path') . 'theme/frontend/default/style/default/image/noimage/item.png';
        }

        return $this->_appPicture;
    }

    /**
     * get api object
     * @param null $iUserId
     * @param bool $bIsCache
     * @return FacebookSBYN
     */
    public function getApi($iUserId = null, $bIsCache = true)
    {
        $config = $this->getSetting();

        if (isset($config['pic']) && $config['pic']) {
            $this->_appPicture = Phpfox::getParam('core.path') . 'file/pic/photo/' . str_replace('%s', '',
                    $config['pic']);
        } else {
            $this->_appPicture = Phpfox::getParam('core.path') . 'theme/frontend/default/style/default/image/noimage/item.png';
        }

        if (null == $this->_api || !$bIsCache) {

            if (isset($config['app_id'])) {
                $config['appId'] = $config['app_id'];
            }

            $this->_api = new FacebookSBYN($config);

            list($token, $profile) = $this->getTokenData($iUserId);

            if ($token) {
                $this->_api->setAccessToken($token);
                $this->_profile = $profile;
            }
        }

        return $this->_api;
    }

    /**
     * get connected facebook profile as array or object
     * @param string $iFacebookUID
     * @param bool $bIsGetNew
     * @return array|NULL
     */
    public function getProfile($iFacebookUID = 'me', $bIsGetNew = false)
    {
        if (null == $this->_profile || $bIsGetNew) {
            $oFacebook = $this->getApi();

            if ($iFacebookUID == null) {
                $iFacebookUID = "me";
            }

            try {
                $extrafields = '?fields=about,birthday,email,name,first_name,last_name,gender,picture,link,website';

                $me = $oFacebook->api('/' . $iFacebookUID . $extrafields);
            } catch (exception $e) {
                return array();
            }

            $iFacebookUID = isset($me['id']) ? $me['id'] : "";

            if (!isset($me['link'])) {
                $me['link'] = "http://facebook.com/" . $iFacebookUID;
            }

            $me['identity'] = $me['id'];
            $me['full_name'] = @$me['name'];
            $location = (isset($me['location']) ? @$me['location']['name'] : '');
            if ($location) {
                $arr_location = explode(",", $location);
                if (count($arr_location) > 0) {
                    $me['country'] = $arr_location[count($arr_location) - 1];
                }
            }
            if (isset($me['user_name'])) {
                $me['facebook'] = $me['username'];
            } else {
                $me['facebook'] = $me['id'];
                $me['user_name'] = $me['first_name'] . $me['last_name'];
            }
            $me['service'] = 'facebook';
            if (isset($me['birthday']) && !empty($me['birthday'])) {
                $birthday = explode("/", $me['birthday']);
                //02/01/1987
                // month -> 0
                // day -> 1
                // year -> 2
                $me['birthday'] = Phpfox::getService('user')->buildAge($birthday[1], $birthday[0], $birthday[2]);
                $me['birthday_search'] = Phpfox::getLib('date')->mktime(0, 0, 0, $birthday[0], $birthday[1],
                    $birthday[2]);
            }
            if (empty($me['gender'])) {
                $me['gender'] = '';
            }
            $me['gender'] = ($me['gender'] == 'male') ? 1 : 2;

            $imgLink = "http://graph.facebook.com/%s/picture";
            $imgLink = sprintf($imgLink, $me['identity']);
            $me['img_url'] = $imgLink;

            $this->_profile = $me;
        }

        return $this->_profile;

    }

    /**
     * get list of conntected twitters friends of current viewer
     * alias to get contacts
     * @TODO get a large of contact etc: 100,000 followers
     * @param int $iPage OPTIONAL DEFAULT  = 1
     * @param int $iLimit OPTIONAL DEFAULT = 50
     * @return array
     */
    public function getFriends($iPage = 1, $iLimit = 50)
    {
        return $this->getContacts($iPage, $iLimit);
    }

    /**
     * get list of facebook friends of current viewer
     * @param int $iPage OPTIONAL DEFAULT  = 1
     * @param int $iLimit OPTIONAL DEFAULT = 50
     * @return array
     */
    public function getContacts($iPage = 1, $iLimit = 50)
    {
        return array();
    }

    /**
     * @param int $iUserId
     * @param string $sRecipient
     * @param string $sSubject
     * @param string $sMessage
     * @param string $sLink
     * @return true|false
     * @throws Exception
     */
    public function sendInvitation($iUserId, $sRecipient, $sSubject, $sMessage, $sLink)
    {
        return true;
    }

    public function post($aVals)
    {
        try {
            $oFacebook = $this->getApi();

            $sType = $aVals['type'];

            if (!isset($aVals['picture'])) {
                $aVals['picture'] = '';
            }

            $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . Phpfox::getUserId());
            $aFeed = Phpfox::getLib('cache')->get($sIdCache);

            if (isset($aFeed['url'])) {
                $aVals['picture'] = $aFeed['url'];
            }
            if (isset($aFeed['iItemId']) && $aFeed) {
                $iItemId = $aFeed['iItemId'];
                if ($sType == 'link') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('link'))->where('link_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image']) && $aRow['image']) {
                        $aVals['picture'] = $aRow['image'];
                    }
                }

                if ($sType == 'resume') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('resume_basicinfo'))->where('resume_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . "/resume/" . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }
                }

                if ($sType == 'photo') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('photo'))->where('photo_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['destination']) && $aRow['destination']) {
                        $sUrlImage = Phpfox::getParam('photo.url_photo') . $aRow['destination'];
                        $aVals['picture'] = sprintf($sUrlImage, '_1024');
                    }
                }

                if ($sType == 'contest') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('contest'))->where('contest_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . "/contest/" . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }
                }

                if ($sType == 'poll') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('poll'))->where('poll_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('poll.url_image') . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }
                }

                if ($sType == 'quiz') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('quiz'))->where('quiz_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('quiz.url_image') . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }
                }

                if ($sType == 'video') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('video'))->where('video_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('video.url_image') . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }
                }

                if ($sType == 'coupon') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('coupon'))->where('coupon_id = ' . $iItemId)->execute('getRow');

                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }

                }

                if ($sType == 'directory') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('directory_business'))->where('business_id = ' . $iItemId)->execute('getRow');

                    if (isset($aRow['logo_path']) && $aRow['logo_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . $aRow['logo_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }

                }

                if ($sType == 'advancedphoto') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('photo'))->where('photo_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['destination']) && $aRow['destination']) {
                        $sUrlImage = Phpfox::getParam('photo.url_photo') . $aRow['destination'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }
                }

                if ($sType == 'advancedmarketplace') {
                    $aImageRow = $this->database()
                        ->select('*')
                        ->from(Phpfox::getT('advancedmarketplace_image'))
                        ->where('listing_id = ' . $iItemId)
                        ->execute('getRow');

                    if (isset($aImageRow['image_path']) && $aImageRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . 'advancedmarketplace' . PHPFOX_DS . $aImageRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '');
                    }
                }

                if ($sType == 'karaoke_song') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('karaoke_song'))->where('song_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_file') . 'karaoke/image' . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '_thumb_225x225');
                    }
                }

                if ($sType == 'karaoke_recording') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('karaoke_recording'))->where('recording_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_file') . 'karaoke/image' . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '_thumb_225x225');
                    }
                }

                if ($sType == 'musicsharing_album' || $sType == "musicstore_album") {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('m2bmusic_album'))->where('album_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['album_image']) && $aRow['album_image']) {
                        if ($sType == 'musicsharing_album') {
                            $sUrlImage = Phpfox::getParam('core.url_pic') . 'musicsharing' . PHPFOX_DS . $aRow['album_image'];
                        }
                        if ($sType == 'musicstore_album') {
                            $sUrlImage = Phpfox::getParam('core.url_pic') . 'musicstore' . PHPFOX_DS . $aRow['album_image'];
                        }

                        $aVals['picture'] = empty($sUrlImage) ? '' : sprintf($sUrlImage, '');
                    }
                }

                if ($sType == 'musicsharing_playlist' || $sType == "musicstore_playlist") {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('m2bmusic_playlist'))->where('playlist_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['playlist_image']) && $aRow['playlist_image']) {
                        if ($sType == 'musicsharing_playlist') {
                            $sUrlImage = Phpfox::getParam('core.url_pic') . 'musicsharing' . PHPFOX_DS . $aRow['playlist_image'];
                        }
                        if ($sType == 'musicstore_playlist') {
                            $sUrlImage = Phpfox::getParam('core.url_pic') . 'musicstore' . PHPFOX_DS . $aRow['playlist_image'];
                        }
                        $aVals['picture'] = empty($sUrlImage) ? '' : sprintf($sUrlImage, '_thumb_345x250');
                    }
                }
                if ($sType == 'ultimatevideo_video') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('ynultimatevideo_videos'))->where('video_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '_500');
                    }
                }
                if ($sType == 'ultimatevideo_playlist') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('ynultimatevideo_playlists'))->where('playlist_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '_500');
                    }
                }
                if ($sType == 'ynblog') {
                    $aRow = Phpfox::getLib('phpfox.database')->select('*')->from(Phpfox::getT('ynblog_blogs'))->where('blog_id = ' . $iItemId)->execute('getRow');
                    if (isset($aRow['image_path']) && $aRow['image_path']) {
                        $sUrlImage = Phpfox::getParam('core.url_pic') . 'ynadvancedblog/' . $aRow['image_path'];
                        $aVals['picture'] = sprintf($sUrlImage, '_grid');
                    }
                }
            }

            if ($aVals['picture'] == '') {
                $aConfig = Phpfox::getService('socialbridge')->getSetting('facebook');

                if (isset($aConfig['pic']) && $aConfig['pic'] != '') {
                    $aVals['picture'] = Phpfox::getLib('image.helper')->display(array(
                        'path' => 'photo.url_photo',
                        'file' => $aConfig['pic'],
                        'max_width' => 100,
                        'max_height' => 100,
                        'return_url' => true
                    ));
                } else {
                    $user_id = Phpfox::getUserId();
                    $aUser = $this->database()
                        ->select('*')
                        ->from(Phpfox::getT('user'))
                        ->where('user_id = ' . (int)$user_id)
                        ->execute('getRow');
                    if (!$aUser) {
                        $aVals['picture'] = Phpfox::getService('socialbridge')->getStaticPath() . 'module/socialbridge/static/image/default/default/no-image-facebook.png';
                    } else {
                        if ($aUser['user_image'] != '') {

                            $aVals['picture'] = Phpfox::getLib('image.helper')->display(array(
                                'path' => 'core.url_user',
                                'file' => $aUser['user_image'],
                                'max_width' => 100,
                                'max_height' => 100,
                                'suffix' => '_100',
                                'return_url' => true
                            ));
                        } else {
                            $aVals['picture'] = Phpfox::getService('socialbridge')->getStaticPath() . 'module/socialbridge/static/image/default/default/no-image-facebook.png';
                        }
                    }

                }
            }

            $aVals['content'] = preg_replace(array('/\[x=\d+\]/', '/\[\/x\]/'), '', $aVals['content']);
            $aVals['status'] = preg_replace(array('/\[x=\d+\]/', '/\[\/x\]/'), '', $aVals['status']);

            $aPostParam = array(
                'name' => html_entity_decode($aVals['content'], ENT_COMPAT, "UTF-8"),
                'message' => html_entity_decode($aVals['status'], ENT_COMPAT, "UTF-8"),
                'link' => $aVals['url'],
                'description' => phpfox::getParam('core.global_site_title')
            );

            if (isset($aVals['picture']) && $aVals['picture']) {
                $aPostParam['picture'] = $aVals['picture'];
            }

            (($sPlugin = Phpfox_Plugin::get('socialpublishers.component_controller_privacy_settings')) ? eval($sPlugin) : false);

            $oFacebook->api('/me/feed', 'POST', $aPostParam);
        } catch (exception $ex) {
            $aResponse['error'] = $ex->getMessage();
            $aResponse['apipublisher'] = 'facebook';

            return $aResponse;
        }

        return true;
    }

    public function getFeeds($iLastFeedTimestamp = 0, $iLimit = 100, $iUserId = null)
    {
        $oFacebook = $this->getApi($iUserId, false);
        $iServiceUserId = $oFacebook->getUser();

        if (!$iServiceUserId) {
            return false;
        }

        if ($iLastFeedTimestamp > 0) {
            $my_feeds = $oFacebook->api('/' . $iServiceUserId . '/feed?limit=' . $iLimit . '&since=' . $iLastFeedTimestamp);
            $result_me = @$my_feeds['data'];

            $friend_feeds = $oFacebook->api('/' . $iServiceUserId . '/home?limit=' . $iLimit . '&since=' . $iLastFeedTimestamp);
            $result_friend = @$friend_feeds['data'];
        } else {
            $my_feeds = $oFacebook->api('/' . $iServiceUserId . '/feed?limit=' . $iLimit);
            $result_me = @$my_feeds['data'];

            $friend_feeds = $oFacebook->api('/' . $iServiceUserId . '/home?limit=' . $iLimit);
            $result_friend = @$friend_feeds['data'];
        }

        $result = array();
        $result_all = array_merge($result_me, $result_friend);

        foreach ($result_all as $my_feed) {
            $description = '';
            if (isset($my_feed['description'])) {
                $description = $my_feed['description'];
            } else {
                if (isset($my_feed['story'])) {
                    $description = $my_feed['story'];
                }
            }

            $message = '';
            if (isset($my_feed['message'])) {
                $message = $my_feed['message'];
            } else {
                if (isset($my_feed['story'])) {
                    $message = $my_feed['story'];
                }
            }

            $result[] = array(
                'post_id' => $my_feed['id'],
                'actor_id' => $my_feed['from']['id'],
                'target_id' => '',
                'message' => $message,
                'description' => $description,
                'created_time' => strtotime($my_feed['created_time']),
                // 'attachment' => $my_feed[''],
                'permalink' => isset($my_feed['link']) ? $my_feed['link'] : '',
                'description_tags' => isset($my_feed['story_tags']) ? $my_feed['story_tags'] : '',
                'type' => $my_feed['type'],
                'picture' => isset($my_feed['picture']) ? $my_feed['picture'] : '',
                'name' => isset($my_feed['name']) ? $my_feed['name'] : '',
            );
        }

        return $result;
    }

    /**
     * @param null $aUserProfileId
     * @return array|NULL
     */

    function getPostedProfile($aUserProfileId = null)
    {
        if ($aUserProfileId == null) {
            $aUserProfileId = "me";
        }

        $me = $this->getApi()->api('/' . $aUserProfileId);

        $aUserProfileId = $me['id'];

        if (!isset($me['link'])) {
            $me['link'] = "http://facebook.com/" . $aUserProfileId;
        }

        $aUserProfile['user_name'] = isset($me['username']) ? $me['username'] : "";
        $aUserProfile['full_name'] = isset($me['name']) ? $me['name'] : "";
        $aUserProfile['email'] = isset($me['email']) ? $me['email'] : "";
        $aUserProfile['identity'] = $aUserProfileId;
        $aUserProfile['service'] = 'facebook';
        $imgLink = "http://graph.facebook.com/%s/picture";
        $imgLink = sprintf($imgLink, $aUserProfile['identity']);
        $aUserProfile['img_url'] = $imgLink;
        $aUserProfile['link'] = $me['link'];

        return $aUserProfile;
    }


    /**
     * @param string $sMessage , string $sPostId
     * @param $sPostId
     * @return string
     */
    public function comments($sMessage, $sPostId)
    {
        return $this->getApi()->api('/' . $sPostId . '/comments', 'post', array('message' => $sMessage), false);
    }

    public function getUserInfo($params = array())
    {
        $rid = $params['request_id'];
        try {
            $user = $this->getApi()->api('/' . $rid);
        } catch (Exception $e) {
            throw $e;
        }

        return $user;
    }
}
