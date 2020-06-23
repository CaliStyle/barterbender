<?php

/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Service;

use Apps\YouNet_UltimateVideos\Adapter;
use Phpfox;
use Phpfox_Service;
use Phpfox_Pages_Category;
use Phpfox_Plugin;
use Phpfox_Error;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright      YouNet Company
 * @author         HaiNM
 * @package        Module_UltimateVideo
 * @version        4.01
 */
class Ultimatevideo extends Phpfox_Service
{
    private $_aCallback = null;
    private $_aAllowedTypes;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_videos');
        $this->_aAllowedTypes = [
            '3gp',
            'aac',
            'ac3',
            'ec3',
            'flv',
            'm4f',
            'mov',
            'mj2',
            'mkv',
            'mp4',
            'mxf',
            'ogg',
            'ts',
            'webm',
            'wmv',
            'avi'
        ];
    }

    public function getSimpleVideo($videoId, $fields = 'title') {
        if(empty($videoId) || empty($fields)) {
            return false;
        }

        return db()->select($fields)
                    ->from(Phpfox::getT('ynultimatevideo_videos'))
                    ->where('video_id = '. (int)$videoId)
                    ->execute('getSlaveRow');

    }

    public function getSponsoredItems($limit = 3, $cacheTime = 5) {
        $cacheId = $this->cache()->set('ultimatevideo_video_sponsored_items');
        if((($rows = $this->cache()->get($cacheId)) === false) || !$cacheTime) {
            $rows = db()->select('v.video_id')
                    ->from($this->_sTable, 'v')
                    ->where('v.is_sponsor = 1')
                    ->execute('getSlaveRows');
            if(!empty($rows)) {
                $rows = array_column($rows, 'video_id');
                if($cacheTime) {
                    $this->cache()->saveBoth($cacheId, $rows);
                }
            }
            else {
                return [];
            }
        }

        shuffle($rows);
        $videoIds = array_slice($rows, 0, round($limit * Phpfox::getParam('core.cache_rate')));
        if(!empty($videoIds)) {
            $results = db()->select('v.video_id, v.title, v.image_path, v.image_server_id, v.user_id, v.time_stamp, v.is_approved, v.total_view, v.total_like, v.total_comment, v.total_favorite, v.total_favorite , s.sponsor_id, '. Phpfox::getUserField())
                ->from($this->_sTable, 'v')
                ->join(Phpfox::getT('better_ads_sponsor'), 's', 's.item_id = v.video_id AND s.is_custom = 3 AND s.is_active = 1')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
                ->where('v.video_id IN ('. implode(',', $videoIds) .')')
                ->execute('getSlaveRows');
            return $results;
        }
        return [];
    }

    private function canPurchaseSponsorItem($iItemId)
    {
        $sCacheId = $this->cache()->set('ultimatevideo_video_pending_sponsor');
        if(false === ($aItems = $this->cache()->get($sCacheId)))
        {
            $aRows = db()->select('v.video_id')
                ->from(Phpfox::getT('ynultimatevideo_videos'),'v')
                ->join(Phpfox::getT('better_ads_sponsor'),'s','s.item_id = v.video_id')
                ->where('v.is_sponsor = 0 AND s.is_custom = 2 AND s.module_id = "ultimatevideo_video"')
                ->execute('getSlaveRows');
            $aItems = array_column($aRows,'video_id');
            $this->cache()->save($sCacheId, $aItems);
        }
        return !in_array($iItemId,$aItems);
    }

    /**
     * @param $video
     */
    public function getPermissions(&$video) {
        $video['canSponsor'] = $video['canPurchaseSponsor'] = $video['canSponsorInFeed'] = false;
        if(Phpfox::isAppActive('Core_BetterAds') && (int)$video['is_approved'] == 1) {
            $video['canSponsor'] = Phpfox::getUserParam('ultimatevideo.can_sponsor_video');

            $canPurchaseSponsorItem = $this->canPurchaseSponsorItem($video['video_id']);
            $video['canPurchaseSponsor'] = (Phpfox::getUserId() == $video['user_id']) && Phpfox::getUserParam('ultimatevideo.can_purchase_sponsor_video') && $canPurchaseSponsorItem;
            $video['sponsorInFeedId'] = Phpfox::isModule('feed') && (Phpfox::getService('feed')->canSponsoredInFeed('ultimatevideo_video', $video['video_id']) === true);
            $video['canSponsorInFeed'] = (int)$video['is_approved'] == 1 &&  (Phpfox::isModule('feed') && (($video['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('feed.can_purchase_sponsor')) || Phpfox::getUserParam('feed.can_sponsor_feed')) && Phpfox::getService('feed')->canSponsoredInFeed('ultimatevideo_video', $video['video_id']));
        }
        $video['canApprove'] = (int)$video['is_approved'] == 0 && Phpfox::getUserParam('ultimatevideo.ynuv_can_approve_video');
        $video['canFeature'] = Phpfox::getUserParam('ultimatevideo.ynuv_can_feature_video');
        $video['canEdit'] = Phpfox::getUserParam('ultimatevideo.ynuv_can_edit_video_of_other_user') || (Phpfox::getUserId() == $video['user_id'] && Phpfox::getUserParam('ultimatevideo.ynuv_can_edit_own_video'));
        $video['canDelete'] = Phpfox::getUserParam('ultimatevideo.ynuv_can_delete_video_of_other_user') || (Phpfox::getUserId() == $video['user_id'] && Phpfox::getUserParam('ultimatevideo.ynuv_can_delete_own_video'));
        $video['canDoAction'] = $video['canApprove'] || $video['canFeature'] || $video['canEdit'] || $video['canDelete'] || $video['canSponsor'] || $video['canPurchaseSponsor'] || $video['canSponsorInFeed'];
    }

    /**
     * Get Number of videos in page my videos
     * @param $iUserId
     * @return int
     */
    public function countMyVideosOfUser($iUserId)
    {
        $iCnt = db()->select('COUNT(*)')
            ->from($this->_sTable, 'video')
            ->where('video.module_id != "pages" AND video.module_id != "groups" AND video.user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');
        return (int)$iCnt;
    }

    public function getCustomFieldByCategoryId($iCategoryId)
    {
        $sWhere = '';
        $sWhere .= ' AND ccd.category_id = ' . (int)$iCategoryId;
        $aFields = $this->database()
            ->select('cfd.*')
            ->from(Phpfox::getT("ynultimatevideo_category_customgroup_data"), 'ccd')
            ->join(Phpfox::getT('ynultimatevideo_custom_group'), 'cgr', ' ( cgr.group_id = ccd.group_id AND cgr.is_active = 1 ) ')
            ->join(Phpfox::getT('ynultimatevideo_custom_field'), 'cfd', ' ( cfd.group_id = cgr.group_id ) ')
            ->where('1=1' . $sWhere)
            ->order('cgr.group_id ASC , cfd.ordering ASC, cfd.field_id ASC')
            ->execute("getSlaveRows");

        $aHasOption = Phpfox::getService('ultimatevideo.custom')->getHasOption();
        if (is_array($aFields) && count($aFields)) {
            foreach ($aFields as $k => $aField) {
                if (in_array($aField['var_type'], $aHasOption)) {
                    $aOptions = $this->database()->select('*')->from(Phpfox::getT('ynultimatevideo_custom_option'))->where('field_id = ' . $aField['field_id'])->order('option_id ASC')->execute('getSlaveRows');
                    if (is_array($aOptions) && count($aOptions)) {
                        foreach ($aOptions as $k2 => $aOption) {
                            $aFields[$k]['option'][$aOption['option_id']] = $aOption['phrase_var_name'];
                        }
                    }
                }
            }
        }

        return $aFields;
    }

    public function getClass($cName)
    {
        $name = 'Apps\\YouNet_UltimateVideos\\Adapter\\' . $cName;
        if (class_exists($name)) {
            return new $name;
        }

        return null;
    }

    public function getSourTypeIdFromName($sName)
    {
        $sSoureId = array(
            "Youtube" => "1",
            "Vimeo" => "2",
            "Uploaded" => "3",
            "Dailymotion" => "4",
            "VideoURL" => "5",
            "Embed" => "6",
            "Facebook" => "7",
        );
        $sId = $sSoureId[$sName];
        return $sId;
    }

    public function getSourTypeNameFromId($sId)
    {
        $sSoureId = array(
            "1" => "Youtube",
            "2" => "Vimeo",
            "3" => "Uploaded",
            "4" => "Dailymotion",
            "5" => "VideoURL",
            "6" => "Embed",
            "7" => "Facebook",
        );
        $sName = $sSoureId[$sId];
        return $sName;
    }

    public function getManageVideo($aConds = array(), $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {
        $sWhere = '';
        $sWhere .= '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = $this->database()
            ->select("COUNT(uvid.video_id)")
            ->from($this->_sTable, 'uvid')
            ->join(Phpfox::getT("user"), 'u', 'uvid.user_id =  u.user_id')
            ->leftJoin(Phpfox::getT('ynultimatevideo_category'), 'uvc', 'uvc.category_id = uvid.category_id')
            ->where($sWhere)
            ->execute("getSlaveField");
        $aVideos = array();
        if ($iCount) {
            $aVideos = $this->database()
                ->select("uvid.*,uvc.title as category_title," . Phpfox::getUserField())
                ->from($this->_sTable, 'uvid')
                ->join(Phpfox::getT("user"), 'u', 'uvid.user_id =  u.user_id')
                ->leftJoin(Phpfox::getT('ynultimatevideo_category'), 'uvc', 'uvc.category_id = uvid.category_id')
                ->where($sWhere)
                ->order('uvid.video_id DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
            foreach ($aVideos as $key => $aVideo) {
                $aVideos[$key]['source'] = $this->getSourTypeNameFromId($aVideo['type']);
            }
        }

        return array($iCount, $aVideos);
    }

    public function getVideoOwnerId($iVideoId)
    {
        return $this->database()->select('user_id')->from($this->_sTable)->where("video_id = {$iVideoId}")->execute('getSlaveField');
    }

    public function getOwnerEmail($iUserId)
    {
        return $this->database()->select('email')->from(Phpfox::getT('user'))->where('user_id = ' . $iUserId)->execute('getField');
    }

    public function countVideoOfUserId($iUserId = 0)
    {
        $sWhere = '';
        $sWhere .= ' AND uvid.user_id = ' . (int)$iUserId;
        $iCount = $this->database()
            ->select("COUNT(uvid.video_id)")
            ->from($this->_sTable, 'uvid')
            ->where('1=1' . $sWhere)
            ->innerJoin(Phpfox::getT('user'), 'u', 'u.user_id = uvid.user_id')
            ->execute("getSlaveField");

        return $iCount;
    }

    public function getVideoForEdit($iVideoId)
    {
        $aVideo = $this->database()->select("v.*")
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.video_id =' . (int)$iVideoId)
            ->execute('getSlaveRow');

        if ($aVideo) {
            $aCategories = array();
            $aCategories[] = $aVideo['category_id'];
            $iParent = Phpfox::getService('ultimatevideo.category')->getParentCategoryId($aVideo['category_id']);
            while ($iParent != 0) {
                $aCategories[] = $iParent;
                $iParent = Phpfox::getService('ultimatevideo.category')->getParentCategoryId($iParent);
            }
            $aVideo['categories'] = json_encode($aCategories);
            return $aVideo;
        } else {
            return false;
        }
    }

    public function getPlaylist($iPlaylistId)
    {
        if (Phpfox::isModule('track')) {
            if (Phpfox::getUserId()) {
                $this->database()->select("track.item_id AS playlist_is_viewed, ")->leftJoin(Phpfox::getT('track'), 'track',
                    'track.item_id = playlist.playlist_id AND track.user_id = ' . Phpfox::getUserId() . ' AND track.type_id = "ultimatevideo_playlist"');
            } else {
                $this->database()->select("track.item_id AS playlist_is_viewed, ")->leftJoin(Phpfox::getT('track'), 'track',
                    'track.item_id = playlist.playlist_id AND track.ip_address = \'' . db()->escape(Phpfox::getIp(true)) . '\' AND track.type_id = "ultimatevideo_playlist"');
            }
        }

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = playlist.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ultimatevideo_playlist\' AND l.item_id = playlist.playlist_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select("playlist.*, " . Phpfox::getUserField())
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'playlist')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = playlist.user_id')
            ->where('playlist.playlist_id = ' . (int)$iPlaylistId)
            ->execute('getSlaveRow');
        if (!$aRow) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_video_getplaylist__end')) ? eval($sPlugin) : false);

        if (!isset($aRow['is_friend'])) {
            $aRow['is_friend'] = 0;
        }
        $aRow['bookmark_url'] = Phpfox::permalink('ultimatevideo.playlist', $aRow['playlist_id'], $aRow['title']);
        return $aRow;
    }

    public function getVideo($iVideoId, $bView = true)
    {
        if (Phpfox::isModule('track')) {
            if (Phpfox::getUserId()) {
                $this->database()->select("track.item_id AS video_is_viewed, ")->leftJoin(Phpfox::getT('track'), 'track',
                    'track.item_id = video.video_id AND track.user_id = ' . Phpfox::getUserId() . ' AND track.type_id = "ultimatevideo_video"');
            } else {
                $this->database()->select("track.item_id AS video_is_viewed, ")->leftJoin(Phpfox::getT('track'), 'track',
                    'track.item_id = video.video_id AND track.ip_address = \'' . db()->escape(Phpfox::getIp(true)) . '\' AND track.type_id = "ultimatevideo_video"');
            }
        }

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = video.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ultimatevideo_video\' AND l.item_id = video.video_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select("video.*, " . Phpfox::getUserField())
            ->from($this->_sTable, 'video')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = video.user_id')
            ->where('video.video_id = ' . (int)$iVideoId)
            ->execute('getSlaveRow');
        if (!$aRow) {
            return false;
        }
        (($sPlugin = Phpfox_Plugin::get('ultimatevideo.component_service_video_getvideo__end')) ? eval($sPlugin) : false);

        if (!isset($aRow['is_friend'])) {
            $aRow['is_friend'] = 0;
        }

        if (isset($aRow['type'])) {
            $sVideoPath = ($aRow['video_server_id'] == -1 ? Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') : Phpfox::getParam('core.path_actual') . 'PF.Base/file/ynultimatevideo/') . sprintf($aRow['video_path'], '');
            if ($aRow['video_server_id'] > 0 && $aRow['type'] == 3) {
                $sVideoPath = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('core.path_file') . 'file/ynultimatevideo/' . sprintf($aRow['video_path'], ''), $aRow['video_server_id']);
                if(empty($sVideoPath)) {
                    $sVideoPath = Phpfox::getParam('core.path_actual') . 'PF.Base/file/ynultimatevideo/' . sprintf($aRow['video_path'], '');
                }
            }

            $sSourceType = Phpfox::getService('ultimatevideo')->getSourTypeNameFromId($aRow['type']);
            $adapter = Phpfox::getService('ultimatevideo')->getClass($sSourceType);
            $aParams = array(
                'video_id' => $aRow['video_id'],
                'code' => $aRow['code'],
                'view' => $bView,
                'mobile' => Phpfox::getService('ultimatevideo')->isMobile(),
                'count_video' => 0,
                'location' => $aRow['code'],
                'location1' => $sVideoPath,
                'duration' => $aRow['duration']
            );

            $embedCode = $adapter->compileVideo($aParams);

        }
        if (Phpfox::getParam('core.allow_html')) {
            $oFilter = Phpfox::getLib('parse.input');
            $aRow['description'] = $oFilter->prepare(htmlspecialchars_decode($aRow['description']));
        }
        $aRow['embed_code'] = $embedCode;

        $aRow['sTags'] = $this->getHtmlTagString($aRow['video_id'], 'ynultimatevideo');

        $aRow['bookmark_url'] = Phpfox::permalink('ultimatevideo', $aRow['video_id'], $aRow['title']);
        return $aRow;
    }

    /**
     * @param $iVideoId
     * @return string
     */
    public function getHtmlTagString($iVideoId, $type)
    {

        return implode(', ', array_map(function ($temp) {
            return strtr('<a href=":link">:text</a>', [
                ':text' => $temp['tag_text'],
                ':link' => url('ultimatevideo', ['tag' => '']) . $temp['tag_text']]);
        }, $this->database()
            ->select('tag_text')
            ->from(Phpfox::getT('tag'))
            ->where(strtr("category_id=':type' AND item_id=:id", [
                ':type' => $type,
                ':id' => intval($iVideoId)
            ]))
            ->execute('getSlaveRows')));

    }

    public function isMobile()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/(android|iphone|ipad|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $user_agent)) {
                return true;
            }

        } else {
            return false;
        }

        return false;
    }

    public function getParentCategoryOfVideo($iVideoId)
    {
        $aVideo = $this->database()->select('vi.category_id as category_id,ct.parent_id as parent_id')
            ->from($this->_sTable, 'vi')
            ->join(Phpfox::getT('ynultimatevideo_category'), 'ct', 'vi.category_id = ct.category_id')
            ->where('vi.video_id =' . (int)$iVideoId)
            ->execute('getSlaveRow');

        $iParentCategoryId = $aVideo['category_id'];
        if ($aVideo) {
            if (isset($aVideo['parent_id']) && (int)$aVideo['parent_id'] > 0) {
                $iParentCategoryId = (int)$aVideo['parent_id'];
            }
            $iNewParentId = $iParentCategoryId;
            while ($iNewParentId != 0) {
                $iNewParentId = Phpfox::getService('ultimatevideo.category')->getParentCategoryId($iParentCategoryId);
                if ($iNewParentId != 0) {
                    $iParentCategoryId = $iNewParentId;
                }
            }
        }
        return $iParentCategoryId;
    }

    public function getInfoConfig($sDataSource = 'latest', $location = 2)
    {
        $aInfo = array(
            'view' => 1,
            'like' => 0,
            'comment' => 0,
            'rating' => 0,
            'featured' => 1,
        );

        if ($this->bIsSideLocation($location)) {
            switch ($sDataSource) {
                case 'latest':
                case 'most_view':
                case 'recommended':
                case 'related':
                case 'more_from_user':
                case 'watch_it_again':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 0,
                        'comment' => 0,
                        'rating' => 0,
                        'featured' => 1,
                    );
                    break;
                case 'most_liked':
                    $aInfo = array(
                        'view' => 0,
                        'like' => 1,
                        'comment' => 0,
                        'rating' => 0,
                        'featured' => 1,
                    );
                    break;
                case 'most_commented':
                    $aInfo = array(
                        'view' => 0,
                        'like' => 0,
                        'comment' => 1,
                        'rating' => 0,
                        'featured' => 1,
                    );
                    break;
                case 'top_rated':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 0,
                        'comment' => 0,
                        'rating' => 1,
                        'featured' => 1,
                    );
                    break;
                case 'featured':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 0,
                        'comment' => 0,
                        'rating' => 0,
                        'featured' => 0,
                    );
                    break;
            }
        } else {
            switch ($sDataSource) {
                case 'latest':
                case 'most_view':
                case 'recommended':
                case 'related':
                case 'more_from_user':
                case 'watch_it_again':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 1,
                        'comment' => 0,
                        'rating' => 1,
                        'featured' => 1,
                    );
                    break;
                case 'most_liked':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 1,
                        'comment' => 0,
                        'rating' => 1,
                        'featured' => 1,
                    );
                    break;
                case 'most_commented':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 0,
                        'comment' => 1,
                        'rating' => 1,
                        'featured' => 1,
                    );
                    break;
                case 'top_rated':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 1,
                        'comment' => 0,
                        'rating' => 1,
                        'featured' => 1,
                    );
                    break;
                case 'featured':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 1,
                        'comment' => 1,
                        'rating' => 1,
                        'featured' => 0,
                    );
                    break;
            }
        }
        return $aInfo;
    }

    public function getPlaylistInfoConfig($sDataSource = 'latest', $location = 2)
    {
        $aInfo = array(
            'view' => 1,
            'like' => 0,
            'comment' => 0,
            'featured' => 1,
        );

        if ($this->bIsSideLocation($location)) {
            switch ($sDataSource) {
                case 'latest':
                case 'most_view':
                case 'recommended':
                case 'related':
                case 'more_from_user':
                case 'watch_it_again':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 0,
                        'comment' => 0,
                        'featured' => 1,
                    );
                    break;
                case 'most_liked':
                    $aInfo = array(
                        'view' => 0,
                        'like' => 1,
                        'comment' => 0,
                        'featured' => 1,
                    );
                    break;
                case 'most_commented':
                    $aInfo = array(
                        'view' => 0,
                        'like' => 0,
                        'comment' => 1,
                        'featured' => 1,
                    );
                    break;
                case 'top_rated':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 0,
                        'comment' => 0,
                        'featured' => 1,
                    );
                    break;
                case 'featured':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 0,
                        'comment' => 0,
                        'featured' => 0,
                    );
                    break;
            }
        } else {
            switch ($sDataSource) {
                case 'latest':
                case 'most_view':
                case 'recommended':
                case 'related':
                case 'more_from_user':
                case 'watch_it_again':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 0,
                        'comment' => 0,
                        'featured' => 1,
                    );
                    break;
                case 'most_liked':
                    $aInfo = array(
                        'view' => 0,
                        'like' => 1,
                        'comment' => 0,
                        'featured' => 1,
                    );
                    break;
                case 'most_commented':
                    $aInfo = array(
                        'view' => 0,
                        'like' => 0,
                        'comment' => 1,
                        'featured' => 1,
                    );
                    break;
                case 'featured':
                    $aInfo = array(
                        'view' => 1,
                        'like' => 1,
                        'comment' => 1,
                        'featured' => 0,
                    );
                    break;
            }
        }
        return $aInfo;
    }

    public function bIsSideLocation($location = 2)
    {
        return in_array($location, array(1, 9, 3, 10));
    }

    public function getLastestVideoByCategoryId($iCategoryId)
    {
        return $this->database()->select('*')
            ->from($this->_sTable)
            ->where('category_id = ' . $iCategoryId)
            ->executeRow();
    }

    public function getUploadVideoParams()
    {
        $iMaxFileSize = Phpfox::getUserParam('ultimatevideo.ynuv_file_size_limit_in_megabytes');
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $sPreviewTemplate =
            '<div class="dz-preview dz-file-preview">
                <input class="dz-form-file-id" type="hidden" id="js_upload_form_file_v" />
                <div class="dz-upload-successfully-icon"><i class="ico ico-check-circle-alt"></i></div>
                <div class="dz-uploading-message">' . _p('your_video_is_being_uploaded_please_dont_close_this_tab') . '</div>
                <div class="dz-filename"><span data-dz-name ></span></div>
                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                <div class="dz-upload-successfully">' . _p('your_video_is_being_processed_and_will_be_available_soon_after_shared') . '</div>
                <div class="dz-upload-again btn btn-primary">' . _p('browse_three_dot') . '</div>
                <div class="dz-error-message"><span data-dz-errormessage></span></div>
            </div>';

        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => $this->_aAllowedTypes,
            'style' => '',
            'type' => 'v',
            'label' => '',
            'first_description' => _p('drag_and_drop_video_file_here'),
            'type_description' => _p('you_can_upload_a_extensions'),
            'max_size_description' => _p('maximum_file_size_is_file_size',
                ['file_size' => Phpfox::getLib('file')->filesize($iMaxFileSize * 1048576)]),
            'upload_url' => Phpfox::getLib('url')->makeUrl('ultimatevideo.form-upload'),
            'param_name' => 'ajax_upload',
            'type_list_string' => '',
            'upload_icon' => 'ico ico-videocam-o',
            'keep_form' => true,
            'preview_template' => $sPreviewTemplate,
            'use_browse_button' => true,
            'js_events' => [
                'success' => 'UltimateVideo.processUploadSuccess',
                'addedfile' => 'UltimateVideo.processAddedFile',
                'error' => 'UltimateVideo.processError',
            ],
            'extra_data' => [
                'not-show-remove-icon' => 'true',
                'remove-button-action' => 'UltimateVideo.processRemoveButton',
                'single-mode' => 'true',
                'error-message-outside' => 'true'
            ]
        ];
    }
}