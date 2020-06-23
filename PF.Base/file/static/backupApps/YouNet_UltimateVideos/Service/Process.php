<?php
/**
 * Created by PhpStorm.
 * User: davidnguyen
 * Date: 7/27/16
 * Time: 9:23 AM
 */

namespace Apps\YouNet_UltimateVideos\Service;


use Phpfox;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Database;
use Phpfox_Url;

require_once dirname(dirname(__FILE__)) . '/Google/autoload.php';

defined('PHPFOX') or exit('NO DICE!');

class Process extends Phpfox_Service
{
    private $_aCategories = array();

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_videos');
    }

    protected function database()
    {
        return Phpfox_Database::instance();
    }

    public function sponsor($videoId, $sponsor) {
        return db()->update($this->_sTable, ['is_sponsor' => (int)$sponsor], 'video_id = '. (int)$videoId);
    }

    public function Oauth2callback()
    {
        $video_id = $this->_getParam('video_id', 0);
        if (!isset($_SESSION['ynultimatevideo_youtube_video'])) {
            $_SESSION['ynultimatevideo_youtube_video'] = $video_id;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $token = '';
        if (isset($_SESSION['ynultimatevideo_youtube_token']))
            $token = $_SESSION['ynultimatevideo_youtube_token'];
        $OAUTH2_CLIENT_ID = $settings->getSetting('ynultimatevideo_youtube_clientid', "");
        $OAUTH2_CLIENT_SECRET = $settings->getSetting('ynultimatevideo_youtube_secret', "");

        $client = new Google_Client();
        $client->setClientId($OAUTH2_CLIENT_ID);
        $client->setClientSecret($OAUTH2_CLIENT_SECRET);
        $client->setAccessType('offline');
        $client->setScopes('https://www.googleapis.com/auth/youtube');
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"];
        }
        $redirect = $pageURL . Phpfox::getLib('url')->makeUrl('ultimatevideo.oauth2callback');
        $client->setRedirectUri($redirect);

        if ($token) {
            $client->setAccessToken($token);
            if ($client->isAccessTokenExpired()) {
                unset($_SESSION['ynultimatevideo_youtube_token']);
                $state = mt_rand();
                $client->setState($state);
                $_SESSION['state'] = $state;
                $authUrl = $client->createAuthUrl();
                $this->_redirectCustom($authUrl);
            }
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $token = $client->getAccessToken();
        }

        if (!$token) {
            $state = mt_rand();
            $client->setState($state);
            $_SESSION['state'] = $state;
            $authUrl = $client->createAuthUrl();
            $this->_redirectCustom($authUrl);
        } else {
            $_SESSION['ynultimatevideo_youtube_token'] = $token;
            if (isset($_SESSION['ynultimatevideo_youtube_video']) && !$video_id) {
                $video_id = $_SESSION['ynultimatevideo_youtube_video'];
            }
            unset($_SESSION['ynultimatevideo_youtube_video']);
            $video = Engine_Api::_()->getItem('ynultimatevideo_video', $video_id);
            if ($video) {
                $video->user_token = $token;
                $video->save();

                // Add to jobs
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynultimatevideo_uploadyoutube', array('item' => $video->getGuid()));
            }

            // redirect to manage page
            Phpfox::getLib('url')->send('ultimatevideo');
        }

        return null;
    }

    public function getCategoriesFromForm($aVals)
    {
        if (isset($aVals['category']) && count($aVals['category'])) {
            if (empty($aVals['category'][0])) {
                return false;
            } else if (!is_array($aVals['category'])) {
                $this->_aCategories[] = $aVals['category'];
            } else {
                foreach ($aVals['category'] as $iCategory) {
                    if (empty($iCategory)) {
                        continue;
                    }

                    if (!is_numeric($iCategory)) {
                        continue;
                    }

                    $this->_aCategories[] = $iCategory;
                }
            }
            return true;
        }

        return null;
    }

    public function add($aVals, $aVideo = null, $isFeed = false)
    {
        $oFilter = Phpfox::getLib('parse.input');

        if ($iFlood = user('ynuv_time_before_share_other_video', 0) != 0) {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => $this->_sTable, // Database table we plan to check
                    'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                if ($isFeed)
                    Phpfox::addMessage(_p('uploading_video_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                return \Phpfox_Error::set(_p('uploading_video_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
            }
        }

        $bHasCategory = $this->getCategoriesFromForm($aVals);

        $sVideoTitle = '';
        $adapter = Phpfox::getService('ultimatevideo')->getClass($aVals['video_source']);
        if ($aVals['video_source'] == "VideoURL") {
            $adapter->setParams(array('code' => $aVals['video_code']));
            $vImagePath = $adapter->getVideoLargeImage();
            $vDuration = $adapter->getVideoDuration();
        } elseif ($aVals['video_source'] != "Uploaded") {
            if ($aVals['video_source'] == 'Facebook') {
                $adapter->setParams(array('link' => $aVals['video_link']));
            } elseif ($aVals['video_source'] != "Dailymotion" && $aVals['video_source'] != "Embed") {
                $adapter->setParams(array('code' => $aVals['video_code']));
            } elseif ($aVals['video_source'] == "Dailymotion") {
                $adapter->setParams(array('link' => $aVals['video_link']));
                $adapter->setParams(array('code' => $aVals['video_code']));
            } elseif ($aVals['video_source'] == "Embed") {
                $adapter->setParams(array('link' => $aVals['video_link']));
                $adapter->setParams(array('code' => $aVals['video_code']));
            }
            if ($adapter->isValid()) {
                if ($aVals['video_source'] != "Embed") {
                    $adapter->fetchLink();
                    $vDuration = $adapter->getVideoDuration();
                    $vOriginalImagePath = $adapter->getVideoLargeImage();
                    if ($vOriginalImagePath && $aVals['video_source'] != "Facebook") {
                        $vImagePath = $this->downloadImage($vOriginalImagePath);
                    } elseif ($vOriginalImagePath && $aVals['video_source'] == "Facebook") {
                        $vImagePath = $vOriginalImagePath;
                    } else {
                        $vImagePath = "";
                    }
                } else {
                    $vImagePath = "";
                    $vDuration = 0;
                }
                $sVideoTitle = $adapter->getVideoTitle();
            } else {
                if ($isFeed)
                    Phpfox::addMessage(_p('video_code_is_not_valid_please_try_again'));
                return \Phpfox_Error::set(_p('video_code_is_not_valid_please_try_again'));
            }

        } else {
            $vImagePath = "";
            $vDuration = isset($aVals['duration']) ? $aVals['duration'] : 0;
        }
        if (empty($sVideoTitle)) {
            $sVideoTitle = _p('video_uploaded_on_space') . Phpfox::getTime('m/d/Y', PHPFOX_TIME);
        }
        if (empty($aVals['title'])) {
            $aVals['title'] = $sVideoTitle;
        }
        // Check callback.
        $aCallback = null;
        $bCheckPage = false;
        if (isset($aVals['callback_module'])) {
            $aCallback = [
                'item_id' => $aVals['callback_item_id'],
                'module' => $aVals['callback_module']
            ];

            if (($aCallback['module'] == "pages") || ($aCallback['module'] == "groups")) {
                $bCheckPage = true;
            }
        }
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        $description = $aVals['description'];
        $description = $oFilter->prepare($description);

        $aSql = array(
            'user_id' => isset($aVals['user_id']) ? $aVals['user_id'] : Phpfox::getUserId(),
            'status' => isset($aVals['force_status']) ? $aVals['force_status'] : (isset($aVals['video_source']) && ($aVals['video_source'] != 'Uploaded') ? 1 : 0),
            'parent_user_id' => (isset($aVals['parent_user_id']) ? (int)$aVals['parent_user_id'] : '0'),
            'type' => Phpfox::getService('ultimatevideo')->getSourTypeIdFromName($aVals['video_source']),
            'item_id' => ($aCallback === null ? (isset($aVals['parent_user_id']) ? (int)$aVals['parent_user_id'] : '0') : $aCallback['item_id']),
            'module_id' => ($aCallback === null ? '' : $aCallback['module']),
            'title' => $oFilter->clean($aVals['title'], 255),
            'privacy' => (int)(isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
            'time_stamp' => PHPFOX_TIME,
            'category_id' => $bHasCategory ? end($this->_aCategories) : 0,
            'code' => $aVals['video_code'],
            'description' => (empty($aVals['description'])) ? "" : $description,
            'video_path' => isset($aVals['video_path']) ? $aVals['video_path'] : "",
            'video_server_id' => isset($aVals['video_server_id']) ? $aVals['video_server_id'] : Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'duration' => $vDuration,
            'image_path' => isset($aVals['image_path']) && isset($aVals['force_status']) ? $aVals['image_path'] :  $vImagePath,
            'image_server_id' => isset($aVals['image_server_id']) ? $aVals['image_server_id'] : Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'is_approved' => isset($aVals['is_approved']) ? $aVals['is_approved'] : !Phpfox::getUserParam('ultimatevideo.ynuv_should_be_approve_before_display_video'),
            'allow_upload_channel' => (isset($aVals['allow_upload_channel'])) ? 1 : 0,
        );

        $iVideoId = $this->database()->insert($this->_sTable, $aSql);

        if (!empty($aVals['location'])) {
            $aLocation = [
                'location_name' => !empty($aVals['location']['name']) ? Phpfox::getLib('parse.input')->clean($aVals['location']['name']) : null,
                'location_latlng' => null
            ];
            if ((!empty($aVals['location']['latlng']))) {
                $aMatch = explode(',', $aVals['location']['latlng']);
                $aMatch['latitude'] = floatval($aMatch[0]);
                $aMatch['longitude'] = floatval($aMatch[1]);
                $aLocation['location_latlng'] = json_encode(array(
                    'latitude' => $aMatch['latitude'],
                    'longitude' => $aMatch['longitude']
                ));
            }
            $this->database()->update($this->_sTable, $aLocation, 'video_id =' . (int)$iVideoId);
        }
        // insert custom field by category
        if (isset($aVals['custom']) && count($aVals['custom']) > 0) {
            Phpfox::getService('ultimatevideo.custom.process')->addValue($aVals['custom'], $iVideoId);
        }
        //add tag
        if (isset($aVals['tag_list']) && !empty($aVals['tag_list']) && Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            $sTags = $aVals['tag_list'];
            Phpfox::getService('tag.process')->add('ynultimatevideo', $iVideoId, Phpfox::getUserId(), $sTags, false);
            $this->cache()->remove('ultimatevideo.video');
        }
        $iFeedId = 0;
        if ($iVideoId && $aVals['video_source'] != "Uploaded" && !Phpfox::getUserParam('ynuv_should_be_approve_before_display_video')) {
            if (isset($aVals['callback_module']) && Phpfox::isModule($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'getFeedDetails')) {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aVals['callback_module'] . '.getFeedDetails', $aVals['callback_item_id']))->add('ultimatevideo_video', $iVideoId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), $aVals['callback_item_id']) : null);
            } else {
                ((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? $iFeedId = Phpfox::getService('feed.process')->add('ultimatevideo_video', $iVideoId, $aVals['privacy'], 0, 0, $aVideo['user_id']) : null);
            }
        }
        $variable = user('ultimatevideo.points_ultimatevideo_video');
        $checkInt = filter_var($variable, FILTER_VALIDATE_INT);
        if ($checkInt && $variable > 0) {
            if ($iVideoId && !Phpfox::getUserParam('ynuv_should_be_approve_before_display_video')) {
                $this->updateCountVideoForCategory(end($this->_aCategories), "add");
                Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'ultimatevideo_video', '+');
            }
        }

        if ($aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('ultimatevideo', $iVideoId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }
        // plugin call
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.service_process_add__end')) ? eval($sPlugin) : false);

        return $iVideoId;
    }

    public function update($aVals, $iVideoId)
    {
        $oFilter = Phpfox::getLib('parse.input');

        $bHasCategory = $this->getCategoriesFromForm($aVals);
        $bHasCategoryActive = Phpfox::getService('ultimatevideo.category')->get() ? true : false;

        // Check callback.
        $aCallback = null;
        if (isset($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'uploadVideo')) {
            $aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadVideo', $aVals);
        }

        $description = $aVals['description'];
        $description = $oFilter->prepare($description);
        $aSql = array(
            'title' => (empty($aVals['title'])) ? "" : $oFilter->clean($aVals['title'], 255),
            'privacy' => (int)(isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
            'description' => (empty($aVals['description'])) ? "" : $description,
            'allow_upload_channel' => (isset($aVals['allow_upload_channel'])) ? 1 : 0,
        );

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aSql['image_path'] = 'ynultimatevideo' . PHPFOX_DS . $aFile['path'];
                $aSql['image_server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }

        if ($bHasCategoryActive) {
            $aSql['category_id'] = $bHasCategory ? end($this->_aCategories) : 0;
        }
        $this->database()->update($this->_sTable, $aSql, 'video_id = ' . (int)$iVideoId);
        // update custom field by category
        if (isset($aVals['custom']) && count($aVals['custom']) > 0) {
            $this->database()->delete(Phpfox::getT('ynultimatevideo_custom_value'), 'video_id = ' . (int)$iVideoId);
            Phpfox::getService('ultimatevideo.custom.process')->addValue($aVals['custom'], $iVideoId);
        }

        //update tag
        if (isset($aVals['tag_list']) && Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->update('ynultimatevideo', $iVideoId, Phpfox::getUserId(), $aVals['tag_list']);
            $this->cache()->remove('ultimatevideo.video');
        }
        //update privacy
        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == '4') {
                Phpfox::getService('privacy.process')->update('ultimatevideo', $iVideoId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            } else {
                Phpfox::getService('privacy.process')->delete('ultimatevideo', $iVideoId);
            }
        }

        if(Phpfox::isModule('feed')) {
            $hasParent = db()->select('module_id')
                        ->from(Phpfox::getT('ynultimatevideo_videos'))
                        ->where('video_id = '. $iVideoId)
                        ->execute('getSlaveField');
            if(empty($hasParent)) {
                Phpfox::getService('feed.process')->update('ultimatevideo_video', $iVideoId, $aVals['privacy'],
                    0);
            }
        }
        return $iVideoId;
    }

    public function downloadImage($sImgUrl)
    {
        if (!$sImgUrl) {
            return '';
        }
        $pos = stripos($sImgUrl, ".bmp");
        if ($pos > 0) {
            return $sImgUrl;
        }
        //Check Folder Storage
        $sNewsPicStorage = Phpfox::getParam('core.dir_pic') . 'ynultimatevideo';
        if (!is_dir($sNewsPicStorage)) {
            @mkdir($sNewsPicStorage, 0777, 1);
            @chmod($sNewsPicStorage, 0777);
        }

        // Generate Image object and store image to the temp file
        $iToken = rand();
        $oImage = \Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');

        if (empty($oImage) && (substr($sImgUrl, 0, 8) == 'https://')) {
            $sImgUrl = 'http://' . substr($sImgUrl, 8);
            $oImage = Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');
        }
        $sTempImage = 'ynultimatevideo_temp_thumbnail_' . $iToken . '_' . PHPFOX_TIME;
        \Phpfox::getLib('file')->writeToCache($sTempImage, $oImage);
        // Save image
        $ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '%s.jpg';
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 1024), 1024, 1024);
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 500), 500, 500);
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 250), 250, 250);
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 120), 120, 120);
        @unlink(PHPFOX_DIR_CACHE . $sTempImage);

        $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $ThumbNail);
        $sFileName = str_replace("\\", "/", $sFileName);
        // Return logo file
        return $sFileName;
    }

    public function featureVideo($iVideoId, $iIsFeatured)
    {
        if (!user('ynuv_can_feature_video') && !Phpfox::isAdmin()) {
            return false;
        }
        $oVideo = Phpfox::getService('ultimatevideo');
        $this->database()->update($this->_sTable, array('is_featured' => $iIsFeatured), "video_id = {$iVideoId}");
        $oVideoTitle = $this->database()->select('title')->from($this->_sTable)->where("video_id = {$iVideoId}")->execute('getSlaveField');
        if ($iIsFeatured) {
            $iOwnerId = $oVideo->getVideoOwnerId($iVideoId);
            if ($iOwnerId) {
                // Add notification
                $iSenderUserId = $iOwnerId;
                if ((int)Phpfox::getUserId() > 0) {
                    $iSenderUserId = Phpfox::getUserId();
                }
                Phpfox::getService("notification.process")->add("ultimatevideo_videofeature", $iVideoId, $iOwnerId, $iSenderUserId);
            }
        }
        return true;
    }

    public function approvedVideo($iVideoId, $iIsApproved)
    {
        if (!user('ynuv_can_approve_video') && !Phpfox::isAdmin()) {
            return false;
        }
        $oVideo = Phpfox::getService('ultimatevideo');
        $this->database()->update($this->_sTable, array('is_approved' => $iIsApproved), "video_id = {$iVideoId}");
        $aVideo = $this->database()->select('*')->from($this->_sTable)->where("video_id = {$iVideoId}")->execute('getRow');
        if ($iIsApproved) {
            $iOwnerId = $oVideo->getVideoOwnerId($iVideoId);
            if ($iOwnerId) {
                // Add notification
                $iSenderUserId = $iOwnerId;
                if ((int)Phpfox::getUserId() > 0) {
                    $iSenderUserId = Phpfox::getUserId();
                }
                Phpfox::getService("notification.process")->add("ultimatevideo_videoapprove", $iVideoId, $iOwnerId, $iSenderUserId);
            }
            if (isset($aVideo['module_id']) && Phpfox::isModule($aVideo['module_id']) && Phpfox::hasCallback($aVideo['module_id'], 'getFeedDetails')) {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aVideo['module_id'] . '.getFeedDetails', $aVideo['item_id']))->add('ultimatevideo_video', $iVideoId, $aVideo['privacy'], (isset($aVideo['privacy_comment']) ? (int)$aVideo['privacy_comment'] : 0), $aVideo['item_id'], $aVideo['user_id']) : null);
            } else {
                ((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? $iFeedId = Phpfox::getService('feed.process')->add('ultimatevideo_video', $iVideoId, $aVideo['privacy'], 0, 0, $aVideo['user_id']) : null);
            }

            Phpfox::getService('user.activity')->update($iOwnerId, 'ultimatevideo_video', '+');
        }

        return true;
    }

    public function canDelete($aVideo)
    {
        $bPass = false;
        if ($aVideo['video_id']) {
            // Check if is owner of groups / pages
            if ($aVideo['module_id'] == 'pages' && Phpfox::isModule('pages') && Phpfox::getService('pages')->isAdmin($aVideo['item_id'])) {
                $bPass = true; // is owner of page
            } elseif ($aVideo['module_id'] == 'groups' && Phpfox::isModule('groups') && Phpfox::getService('groups')->isAdmin($aVideo['item_id'])) {
                $bPass = true; // is owner of page
            } elseif (user('ynuv_can_delete_video_of_other_user') || (Phpfox::getUserId() == $aVideo['user_id'] && user('ynuv_can_delete_own_video'))) {
                $bPass = true;
            }
        }

        return $bPass;
    }

    public function deleteVideo($iVideoId, $bForce = false)
    {
        $aVideo = $this->database()->select('*')
            ->from($this->_sTable)
            ->where("video_id =" . (int)$iVideoId)
            ->execute('getRow');
        if ($bForce || $this->canDelete($aVideo)) {

            if (Phpfox::isModule('tag')) {
                Phpfox::getService('tag.process')->deleteForItem($aVideo['user_id'], $iVideoId, 'ynultimatevideo');
                $this->cache()->remove('tag');
                $this->cache()->remove('ultimatevideo.video');
            }
            if (!empty($aVideo['image_path'])) {
                $sImagePath = $aVideo['image_path'];
                $aImages = array(
                    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_120'),
                    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_250'),
                    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_500'),
                    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_1024')
                );
                foreach ($aImages as $sImage) {
                    if (file_exists($sImage)) {
                        @unlink($sImage);
                    }
                    if ($aVideo['image_server_id'] > 0) {
                        Phpfox::getLib('cdn')->remove($sImage);
                    }
                }
            }
            if (!empty($aVideo['video_path'])) {
                $sFilePath = 'ynultimatevideo/' . $aVideo['video_path'];
                $aFiles = array(
                    Phpfox::getParam('core.dir_file') . sprintf($sFilePath, ''),

                );
                foreach ($aFiles as $sFile) {
                    if (file_exists($sFile)) {
                        @unlink($sFile);
                    }
                }
                if ($aVideo['video_server_id'] > 0) {
                    foreach ($aFiles as $sFile) {
                        Phpfox::getLib('cdn')->remove($sFile);
                    }
                }
            }

            $this->database()->delete(Phpfox::getT('ynultimatevideo_ratings'), "video_id = " . (int)$iVideoId);
            $aPlaylists = Phpfox::getService('ultimatevideo.playlist')->getAllPlaylistOfVideo($iVideoId);
            if (!empty($aPlaylists)) {
                foreach ($aPlaylists as $key => $aPlaylist) {
                    Phpfox::getService('ultimatevideo.playlist.process')->updateCountVideoForPlaylist($aPlaylist['playlist_id'],
                        'delete');
                }
            }
            $this->database()->delete(Phpfox::getT('ynultimatevideo_playlist_data'), "video_id = " . (int)$iVideoId);
            $this->database()->delete(Phpfox::getT('ynultimatevideo_history'),
                "item_id = " . (int)$iVideoId . " AND item_type ='0'");
            $this->database()->delete(Phpfox::getT('ynultimatevideo_favorites'), "video_id = " . (int)$iVideoId);
            $this->database()->delete(Phpfox::getT('ynultimatevideo_watchlaters'), "video_id = " . (int)$iVideoId);
            $this->database()->delete(Phpfox::getT('ynultimatevideo_custom_value'), "video_id = " . (int)$iVideoId);
            $this->database()->delete($this->_sTable, "video_id = " . (int)$iVideoId);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('ultimatevideo_video',
                (int)$iVideoId) : null);
            Phpfox::getService('tag.process')->deleteForItem(Phpfox::getUserId(), $iVideoId, 'ynultimatevideo');
            $this->updateCountVideoForCategory($aVideo['category_id'], "delete");
            $variable = user('ultimatevideo.points_ultimatevideo_video');
            $checkInt = filter_var($variable, FILTER_VALIDATE_INT);
            if ($checkInt && $variable > 0 && $aVideo['is_approved']) {
                Phpfox::getService('user.activity')->update($aVideo['user_id'], 'ultimatevideo_video', '-');
            }
            //Delete pages feed

            if ($aVideo['module_id'] == 'pages') {
                $sType = 'ultimatevideo_video';

                $aFeeds = $this->database()->select('feed_id, user_id')
                    ->from(Phpfox::getT($aVideo['module_id'] . '_feed'))
                    ->where('type_id = \'' . $sType . '\' AND item_id = ' . (int)$iVideoId)
                    ->execute('getRows');
                if (count($aFeeds)) {
                    foreach ($aFeeds as $aFeed) {
                        $this->database()->delete(Phpfox::getT($aVideo['module_id'] . '_feed'),
                            'feed_id = ' . $aFeed['feed_id']);
                    }
                }
            }
        }
        return true;
    }

    public function updateCountVideoForCategory($iCategoryId, $sType)
    {
        $totalCount = $this->database()->select('used')
            ->from(Phpfox::getT('ynultimatevideo_category'))
            ->where('category_id = ' . (int)$iCategoryId)
            ->execute('getSlaveField');

        switch ($sType) {
            case "add":
                $totalCount = (int)$totalCount + 1;
                $this->database()->update(Phpfox::getT('ynultimatevideo_category'), array('used' => $totalCount), 'category_id = ' . (int)$iCategoryId);
                break;
            case "delete":
                $totalCount = (int)$totalCount - 1;
                $this->database()->update(Phpfox::getT('ynultimatevideo_category'), array('used' => $totalCount), 'category_id = ' . (int)$iCategoryId);
                break;
        }
    }

    public function convertVideos()
    {
        $iLimit = setting('ynuv_cron_limit_per_time', 5);
        $aVideos = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('status = 0 AND type = 3')
            ->limit($iLimit)
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');

        if (count($aVideos)) {
            foreach ($aVideos as $key => $aVideo) {
                if (!empty($aVideo['user_token']) && $aVideo['allow_upload_channel'] == 1) {
                    $this->uploadVideoToChannel($aVideo);
                } else {
                    $this->_convertVideo($aVideo);
                }

            }
        }
    }

    private function _convertVideo($aVideo)
    {
        if (isset($aVideo['vieo_id'])) {
            echo _p('argument_was_not_a_valid_video');
            return;
        }
        // Make sure FFMPEG path is set
        $ffmpeg_path = setting('ynuv_ffmpeg_path');
        $status = 2;
        if (!$ffmpeg_path) {
            echo _p('ffmpeg_not_configured');
            return;
        }
        // Make sure FFMPEG can be run
        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);
            if ($return > 0) {
                echo _p('ffmpeg_found_but_is_not_executable');
                return;
            }
        }

        // Check we can execute
        if (!function_exists('shell_exec')) {
            echo _p('unable_to_execute_shell_commands_using_shell_exec_the_function_is_disabled');
            return;
        }

        // Check the video directory
        $tmpDir = PHPFOX_DIR_FILE . 'ynultimatevideo' . PHPFOX_DS;
        if (!is_dir($tmpDir)) {
            if (!mkdir($tmpDir, 0777, true)) {
                echo _p('video_directory_did_not_exist_and_could_not_be_created');
                return;
            }
        }
        if (!is_writable($tmpDir)) {
            echo _p('video_directory_is_not_writable');
            return;
        }
        //2 video is in convert process
        $this->updateStatusVideo(2, $aVideo['video_id']);
        $filetype = $aVideo['code'];
        $videoPath = Phpfox::getParam('core.dir_file') . 'ynultimatevideo/' . sprintf($aVideo['video_path'], '');
        if (!file_exists($videoPath)) {
            //4 file not found
            $this->updateStatusVideo(4, $aVideo['video_id']);
            echo _p('could_not_pull_to_temporary_file');
            return;
        }

        $iToken = rand();
        if (!is_dir(PHPFOX_DIR_CACHE . 'ynultimatevideo')) {
            @mkdir(PHPFOX_DIR_CACHE . 'ynultimatevideo', 0777, 1);
            @chmod(PHPFOX_DIR_CACHE . 'ynultimatevideo', 0777);
        }
        $outputPath = PHPFOX_DIR_CACHE . 'ynultimatevideo' . DIRECTORY_SEPARATOR . $iToken . '_' . PHPFOX_TIME . '_vconvert.mp4';
        $thumbTempPath = PHPFOX_DIR_CACHE . 'ynultimatevideo' . DIRECTORY_SEPARATOR . $iToken . '_' . PHPFOX_TIME . '_vthumb_large.jpg';

        //Convert to Mp4 (h264 - HTML5, mpeg4 - IOS)
        $videoCommand = $ffmpeg_path . ' '
            . '-i ' . escapeshellarg($videoPath) . ' '
            . '-ab 64k' . ' '
            . '-ar 44100' . ' '
            . '-q:v 5' . ' '
            . '-r 25' . ' ';

        $videoCommand .= '-vcodec libx264' . ' '
            . '-acodec aac' . ' '
            . '-strict experimental' . ' '
            . '-preset veryfast' . ' '
            . '-f mp4' . ' ';

        $videoCommand .=
            '-y ' . escapeshellarg($outputPath) . ' '
            . '2>&1';
        // Prepare output header
        $output = PHP_EOL;
        $output .= $videoPath . PHP_EOL;
        $output .= $outputPath . PHP_EOL;
        // Execute video encode command
        $videoOutput = $output . $videoCommand . PHP_EOL . shell_exec($videoCommand);
        // Check for failure

        $success = true;

        // Unsupported format
        if (preg_match('/Unknown format/i', $videoOutput) || preg_match('/Unsupported codec/i', $videoOutput) || preg_match('/patch welcome/i', $videoOutput) || preg_match('/Audio encoding failed/i', $videoOutput) || !is_file($outputPath) || filesize($outputPath) <= 0) {
            $success = false;
            //3 unsupported format
            $this->updateStatusVideo(3, $aVideo['video_id']);
            $status = 3;
        } // This is for audio files
        else
            if (preg_match('/video:0kB/i', $videoOutput)) {
                $success = false;
                //5 audio files
                $this->updateStatusVideo(5, $aVideo['video_id']);
                $status = 5;
            }
        $notificationMessage = '';
        // Failure
        if (!$success) {
            $exceptionMessage = '';
            try {

                if ($status == 3) {
                    $exceptionMessage = 'Video format is not supported by FFMPEG.';
                    $notificationMessage = _p('your_video_conversion_failed_video_format_is_not_supported_by_ffmpeg');
                    echo $notificationMessage;
                } elseif ($status == 5) {
                    $exceptionMessage = 'Audio-only files are not supported.';
                    $notificationMessage = _p('your_video_conversion_failed_audio_files_are_not_supported');
                    echo $notificationMessage;
                } else {
                    $exceptionMessage = _p('unknown_encoding_error');
                    echo $exceptionMessage;
                }
                // @TODO //Send notification to owner
            } catch (Exception $e) {

            }
        } // Success
        else {
            // Get duration of the video to caculate where to get the thumbnail
            if (preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches)) {
                list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
                $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
            } else {
                $duration = 0;
            }

            // Fetch where to take the thumbnail
            $thumb_splice = $duration / 2;

            // Thumbnail proccess command
            $thumbCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($outputPath) . ' ' . '-f image2' . ' ' . '-ss ' . $thumb_splice . ' ' . '-vframes ' . '1' . ' ' . '-v 2' . ' ' . '-y ' . escapeshellarg($thumbTempPath) . ' ' . '2>&1';

            // Process thumbnail
            $thumbOutput = $output . $thumbCommand . PHP_EOL . shell_exec($thumbCommand);

            // Check output message for success
            $thumbSuccess = true;
            if (preg_match('/video:0kB/i', $thumbOutput)) {
                $thumbSuccess = false;
            }

            // Resize thumbnail
            if ($thumbSuccess) {
                try {
                    if (empty($aVideo['image_path']) && is_file($thumbTempPath)) {
                        $sNewsPicStorage = Phpfox::getParam('core.dir_pic') . 'ynultimatevideo';
                        if (!is_dir($sNewsPicStorage)) {
                            @mkdir($sNewsPicStorage, 0777, 1);
                            @chmod($sNewsPicStorage, 0777);
                        }
                        $ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '%s.jpg';
                        Phpfox::getLib('image')->createThumbnail($thumbTempPath, sprintf($ThumbNail, '_' . 120), 120, 120);
                        Phpfox::getLib('image')->createThumbnail($thumbTempPath, sprintf($ThumbNail, '_' . 250), 250, 250);
                        Phpfox::getLib('image')->createThumbnail($thumbTempPath, sprintf($ThumbNail, '_' . 500), 500, 500);
                        Phpfox::getLib('image')->createThumbnail($thumbTempPath, sprintf($ThumbNail, '_' . 1024), 1024, 1024);
                        $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $ThumbNail);
                        $sFileName = str_replace("\\", "/", $sFileName);
                        $this->updateVideoImage($sFileName, $aVideo['video_id']);
                        unlink($thumbTempPath);
                    }
                } catch (Exception $e) {
                    $thumbSuccess = false;
                }
            }

            // Save video
            try {
                $saveVideoPath = Phpfox::getLib('file')->upload($outputPath, PHPFOX_DIR_FILE . 'ynultimatevideo' . PHPFOX_DS, $aVideo['title']);
                //Set duration of video
                $this->database()->update($this->_sTable, array('duration' => $duration, 'video_path' => $saveVideoPath), 'video_id = ' . (int)$aVideo['video_id']);
                // 1 convert completed
                $status = 1;
                $notificationMessage = _p('your_video_is_converted');
                $this->updateStatusVideo(1, $aVideo['video_id']);
                // delete the files from temp dir
                if (!empty($aVideo['image_path']))
                    unlink($thumbTempPath);
                unlink($outputPath);
                unlink($videoPath);

            } catch (Exception $e) {

                unlink($outputPath);

                // 7 convert fail
                $status = 7;
                $this->updateStatusVideo(7, $aVideo['video_id']);

                // notify the owner
                $notificationMessage = _p('video_conversion_failed_you_may_be_over_the_site_upload_limit_try_uploading_a_smaller_file_or_delete_some_files_to_free_up_space');
                throw $e;
                // throw
            }

        }
        if ($status != 1) {
            unlink(Phpfox::getParam('core.dir_file') . 'ynultimatevideo/' . sprintf($aVideo['video_path'], ''));
        }
        Phpfox::getService('notification.process')->add('ultimatevideo_videoconvert', $aVideo['video_id'], $aVideo['user_id'], null, true);
        if ($status == 1 && !empty($aVideo['is_approved'])) {
            if (isset($aVideo['module_id']) && Phpfox::isModule($aVideo['module_id']) && Phpfox::hasCallback($aVideo['module_id'], 'getFeedDetails')) {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aVideo['module_id'] . '.getFeedDetails', $aVideo['item_id']))->add('ultimatevideo_video', $aVideo['video_id'], $aVideo['privacy'], (isset($aVideo['privacy_comment']) ? (int)$aVideo['privacy_comment'] : 0), $aVideo['item_id'], $aVideo['user_id']) : null);
            } else {
                ((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? Phpfox::getService('feed.process')->add('ultimatevideo_video', $aVideo['video_id'], $aVideo['privacy'], 0, 0, $aVideo['user_id']) : null);
            }

        }
    }

    public function updateStatusVideo($iStatus, $iVideoId)
    {
        $this->database()->update($this->_sTable, array('status' => $iStatus), 'video_id =' . (int)$iVideoId);
        return true;
    }

    public function updateViewCount($iId)
    {
        $this->database()->query("
			UPDATE " . $this->_sTable . "
			SET total_view = total_view + 1
			WHERE video_id = " . (int)$iId . "
		");
        return true;
    }

    public function updateVideoImage($imagePath, $iVideoId)
    {
        $this->database()->update($this->_sTable, array('image_path' => $imagePath), 'video_id = ' . (int)$iVideoId);
        return true;
    }

    public function sendVideoInvitations($videoId, $aVals, $iType)
    {
        $sUserIds = implode(',', array_map(function ($tmp) {
            return intval($tmp);
        }, $aVals['invite']));

        $aUsers = $this->database()->select('user_id, email, language_id, full_name')
            ->from(Phpfox::getT('user'))
            ->where('user_id IN(' . $sUserIds . ')')
            ->execute('getSlaveRows');

        foreach ($aUsers as $aUser) {
            if (isset($aInvited['user'][$aUser['user_id']])) {
                continue;
            }

            Phpfox::getLib('mail')->to($aUser['user_id'])
                ->subject($aVals['subject'])
                ->message($aVals['personal_message'])
                ->send();

            if ($iType == 1) {
                Phpfox::getService('notification.process')->add('ultimatevideo_invitevideo', $videoId, $aUser['user_id']);
            } else {
                Phpfox::getService('notification.process')->add('ultimatevideo_inviteplaylist', $videoId, $aUser['user_id']);
            }
        }

        return true;
    }

    public function uploadVideoToChannel($aVideo)
    {
        /*
         * You can acquire an OAuth 2.0 client ID and client secret from the
         * Google Developers Console <https://console.developers.google.com/>
         * For more information about using OAuth 2.0 to access Google APIs, please see:
         * <https://developers.google.com/youtube/v3/guides/authentication>
         * Please ensure that you have enabled the YouTube Data API for your project.
         */
        if (!$aVideo) {
            echo _p('video_not_found');
            return;
        }
        $owner = $aVideo['user_id'];

        // get allow to upload to youtube (user and admin)
        $user_allow = $aVideo['allow_upload_channel'];
        if (!$user_allow) {
            echo _p('not_allow_upload_to_youtube');
            return;
        }

        // Get token from user (owner of video)
        $token = $aVideo['user_token'];

        // Get Client ID and Client secret key from Youtube API
        $OAUTH2_CLIENT_ID = setting('ynuv_youtube_client_id', "");
        $OAUTH2_CLIENT_SECRET = setting('ynuv_youtube_client_secret', "");

        if (empty($OAUTH2_CLIENT_ID) || empty($OAUTH2_CLIENT_SECRET) || empty($token)) {
            _p('youtube_settings_were_missing');
            return;
        }
        $this->updateStatusVideo(2, $aVideo['video_id']);
        // get new google client
        $client = new \Google_Client();
        $client->setClientId($OAUTH2_CLIENT_ID);
        $client->setClientSecret($OAUTH2_CLIENT_SECRET);
        $client->setAccessType('offline');
        $client->setAccessToken($token);

        /**
         * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
         */
        if ($client->isAccessTokenExpired()) {
            $newToken = json_decode($client->getAccessToken());
            $client->refreshToken($newToken->refresh_token);
        }

        // Check to ensure that the access token was successfully acquired.
        if ($client->getAccessToken()) {
            try {

                // Define an object that will be used to make all API requests.
                $youtube = new \Google_Service_YouTube($client);
                // Check the video directory
                $tmpDir = PHPFOX_DIR_FILE . 'ynultimatevideo' . PHPFOX_DS;
                if (!is_dir($tmpDir)) {
                    if (!mkdir($tmpDir, 0777, true)) {
                        echo _p('video_directory_did_not_exist_and_could_not_be_created');
                        return;
                    }
                }
                if (!is_writable($tmpDir)) {
                    echo _p('video_directory_is_not_writable');
                    return;
                }
                $originalPath = Phpfox::getParam('core.dir_file') . 'ynultimatevideo/' . sprintf($aVideo['video_path'], '');
                if (!file_exists(Phpfox::getParam('core.dir_file') . 'ynultimatevideo/' . sprintf($aVideo['video_path'], ''))) {
                    //4 file not found
                    $this->updateStatusVideo(4, $aVideo['video_id']);
                    echo _p('could_not_pull_to_temporary_file');
                    return;
                }

                // Create a snippet with title, description, tags and category ID
                // Create an asset resource and set its snippet metadata and type.
                // This example sets the video's title, description, keyword tags, and
                // video category.
                $snippet = new \Google_Service_YouTube_VideoSnippet();
                $snippet->setTitle($aVideo['title']);
                $snippet->setDescription($aVideo['description']);

                // Numeric video category. See
                // https://developers.google.com/youtube/v3/docs/videoCategories/list
                $snippet->setCategoryId("22");

                // Set the video's status to "public". Valid statuses are "public",
                // "private" and "unlisted".
                $status = new \Google_Service_YouTube_VideoStatus();
                $status->privacyStatus = "public";

                // Associate the snippet and status objects with a new video resource.
                $video = new \Google_Service_YouTube_Video();
                $video->setSnippet($snippet);
                $video->setStatus($status);

                // Specify the size of each chunk of data, in bytes. Set a higher value for
                // reliable connection as fewer chunks lead to faster uploads. Set a lower
                // value for better recovery on less reliable connections.
                $chunkSizeBytes = 1 * 1024 * 1024;

                // Setting the defer flag to true tells the client to return a request which can be called
                // with ->execute(); instead of making the API call immediately.
                $client->setDefer(true);

                // Create a request for the API's videos.insert method to create and upload the video.
                $insertRequest = $youtube->videos->insert("status,snippet", $video);

                // Create a MediaFileUpload object for resumable uploads.
                $media = new \Google_Http_MediaFileUpload(
                    $client,
                    $insertRequest,
                    'video/*',
                    null,
                    true,
                    $chunkSizeBytes
                );
                $media->setFileSize(filesize($originalPath));

                // Read the media file and upload it chunk by chunk.
                $status = false;
                $handle = fopen($originalPath, "rb");
                while (!$status && !feof($handle)) {
                    $chunk = fread($handle, $chunkSizeBytes);
                    $status = $media->nextChunk($chunk);
                }
                fclose($handle);

                // If you want to make other calls after the file upload, set setDefer back to false
                $client->setDefer(false);

                //update video data (replace uploaded video to youtube video)

                $this->database()->update($this->_sTable, [
                    'code' => $status['id'],
                    'video_path' => "",
                    'type' => 1,
                    'status' => 1,
                    'allow_upload_channel' => 0,
                    'user_token' => ""
                ], 'video_id =' . $aVideo['video_id']);

                // save thumbnail
                $adapter = Phpfox::getService('ultimatevideo')->getClass('Youtube');;
                $adapter->setParams(array(
                    'code' => $status['id'],
                    'video_id' => $aVideo['video_id']
                ));
                $vImagePath = "";
                if ($adapter->getVideoLargeImage())
                    $vOriginalImagePath = $adapter->getVideoLargeImage();
                $vImagePath = $this->downloadImage($vOriginalImagePath);
                $vDuration = 0;
                if ($adapter->getVideoDuration())
                    $vDuration = $adapter->getVideoDuration();
                //update image
                $this->database()->update($this->_sTable, ['image_path' => $vImagePath, 'duration' => $vDuration], 'video_id=' . $aVideo['video_id']);

                // delete temporary file
                unlink($originalPath);


                try {
                    Phpfox::getService('notification.process')->add('ultimatevideo_videoconvert', $aVideo['video_id'], $aVideo['user_id'], null, true);
                    if ($aVideo['video_id'] && (!Phpfox::getUserParam('ynuv_should_be_approve_before_display_video') || $aVideo['is_approved'] == 1)) {
                        if (isset($aVideo['module_id']) && Phpfox::isModule($aVideo['module_id']) && Phpfox::hasCallback($aVideo['module_id'], 'getFeedDetails')) {
                            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aVideo['module_id'] . '.getFeedDetails', $aVideo['item_id']))->add('ultimatevideo_video', $aVideo['video_id'], $aVideo['privacy'], (isset($aVideo['privacy_comment']) ? (int)$aVideo['privacy_comment'] : 0), $aVideo['callback_item_id'], $aVideo['user_id']) : null);
                        } else {
                            ((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? $iFeedId = Phpfox::getService('feed.process')->add('ultimatevideo_video', $aVideo['video_id'], $aVideo['privacy'], 0, 0, $aVideo['user_id']) : null);
                        }
                    }
                } catch (Exception $e) {
                    throw $e;
                }

            } catch (Exception $e) {
                unlink($originalPath);
                $this->updateStatusVideo(7, $aVideo['video_id']);
                throw $e;
            }
        }
    }
}