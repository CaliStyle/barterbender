<?php

defined('PHPFOX') or exit('NO DICE!');
require_once dirname(dirname(__file__)) . '/item/item_abstract.class.php';

class Contest_Service_Entry_Item_Video extends Phpfox_service implements Contest_Service_Entry_Item_Item_Abstract
{
    const CONTEST_VIDEO_ULTIMATEVIDEO = 1;
    const CONTEST_VIDEO_VIDEOCHANNEL = 2;
    const CONTEST_VIDEO_COREVIDEO = 3;

    private $_aImageSize = array(
        30, 50, 120, 480, 500
    );

    /**
     * [$_sOriginalSuffix string of the original images
     * @var string
     */
    private $_sOriginalSuffix = '_480';

    public function __construct()
    {
    }

    /**
     * @param $iContestId
     * @param int $iSourceId
     * @return mixed
     */
    public function getAddNewItemLink($iContestId, $iSourceId = self::CONTEST_VIDEO_ULTIMATEVIDEO)
    {
        $sAddParamName = Phpfox::getService('contest.constant')->getYnAddParamForNavigateBack();
        $sLink = '';

        switch ($iSourceId) {
            // Case Ultimate Videos
            case self::CONTEST_VIDEO_ULTIMATEVIDEO:
                if (Phpfox::isModule('ultimatevideo')) {
                    $sLink = Phpfox::getLib('url')->makeUrl('ultimatevideo.add', array($sAddParamName => $iContestId));
                }
                break;
            case self::CONTEST_VIDEO_VIDEOCHANNEL:
                if (Phpfox::isModule('videochannel')) {
                    $sLink = Phpfox::getLib('url')->makeUrl('videochannel.add', array($sAddParamName => $iContestId));
                }
                break;
            case self::CONTEST_VIDEO_COREVIDEO:
            default:
                if (Phpfox::isModule('v')) {
                    $sLink = Phpfox::getLib('url')->makeUrl('v.share', array($sAddParamName => $iContestId));
                }
                break;
        }

        return $sLink;
    }

    /**
     * @param int $iLimit
     * @param int $iPage
     * @param int $iSourceId
     * @return array
     */
    public function getItemsOfCurrentUser($iLimit = 5, $iPage = 0, $iSourceId = self::CONTEST_VIDEO_ULTIMATEVIDEO)
    {
        $iCnt = 0;
        $aItems = array();
        switch ($iSourceId) {
            // Case Ultimate Videos
            case self::CONTEST_VIDEO_ULTIMATEVIDEO:
                if (Phpfox::isModule('ultimatevideo')) {
                    list($iCnt, $aItems) = $this->getUltVideosOfCurrentUser($iLimit, $iPage);
                }
                break;
            case self::CONTEST_VIDEO_VIDEOCHANNEL:
                if (Phpfox::isModule('videochannel')) {
                    list($iCnt, $aItems) = $this->getChannelVideosOfCurrentUser($iLimit, $iPage);
                }
                break;
            case self::CONTEST_VIDEO_COREVIDEO:
            default:
                if (Phpfox::isModule('v')) {
                    list($iCnt, $aItems) = $this->getCoreVideosOfCurrentUser($iLimit, $iPage);
                }
                break;
        }

        return array($iCnt, $aItems);
    }

    /**
     * @param int $iLimit
     * @param int $iPage
     * @return array
     */
    public function getCoreVideosOfCurrentUser($iLimit = 5, $iPage = 0)
    {
        $sConds = 'view_id = 0 AND user_id = ' . Phpfox::getUserId() . ' ';
        //in case we encounter a post form, we know it is a search request
        if ($iSearchId = Phpfox::getLib('request')->get('search-id')) {
            $sKeyword = Phpfox::getService('contest.helper')->getSearchKeyword($iSearchId);
            if ($sKeyword) {
                $sConds .= $this->database()->search($sType = 'like%', $mField = array('title'), $sSearch = $sKeyword);
            }
        }

        // by default we only get items of current user
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('video'))
            ->where($sConds)
            ->execute('getSlaveField');


        $aItems = $this->database()->select('* ')
            ->from(Phpfox::getT('video'))
            ->where($sConds)
            ->limit($iPage, $iLimit, $iCnt)
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');

        return array($iCnt, $aItems);
    }

    /**
     * @param int $iLimit
     * @param int $iPage
     * @return array
     */
    public function getChannelVideosOfCurrentUser($iLimit = 5, $iPage = 0)
    {
        $sConds = 'user_id = ' . Phpfox::getUserId() . ' AND destination IS NULL ';
        //in case we encounter a post form, we know it is a search request
        if ($iSearchId = Phpfox::getLib('request')->get('search-id')) {
            $sKeyword = Phpfox::getService('contest.helper')->getSearchKeyword($iSearchId);
            if ($sKeyword) {
                $sConds .= $this->database()->search($sType = 'like%', $mField = array('title'), $sSearch = $sKeyword);
            }
        }

        // by default we only get items of current user
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('channel_video'))
            ->where($sConds)
            ->execute('getSlaveField');


        $aItems = $this->database()->select('* ')
            ->from(Phpfox::getT('channel_video'))
            ->where($sConds)
            ->limit($iPage, $iLimit, $iCnt)
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');

        return array($iCnt, $aItems);
    }

    /**
     * @param int $iLimit
     * @param int $iPage
     * @return array
     */
    public function getUltVideosOfCurrentUser($iLimit = 5, $iPage = 0)
    {
        $sConds = 'user_id = ' . Phpfox::getUserId() . ' ';
        //in case we encounter a post form, we know it is a search request
        if ($iSearchId = Phpfox::getLib('request')->get('search-id')) {
            $sKeyword = Phpfox::getService('contest.helper')->getSearchKeyword($iSearchId);
            if ($sKeyword) {
                $sConds .= $this->database()->search($sType = 'like%', $mField = array('title'), $sSearch = $sKeyword);
            }
        }

        // by default we only get items of current user
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('ynultimatevideo_videos'))
            ->where($sConds)
            ->execute('getSlaveField');


        $aItems = $this->database()->select('v.*,v.image_server_id as server_id ')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->where($sConds)
            ->limit($iPage, $iLimit, $iCnt)
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');

        return array($iCnt, $aItems);

    }

    /**
     * @param $iItemId
     * @return array|bool|int|string
     */
    public function getItemFromFoxVideoChannel($iItemId)
    {
        if (!$iItemId && Phpfox::isModule('videochannel')) {
            return false;
        }

        $aItem = $this->database()->select('v.*, ve.* ')
            ->from(Phpfox::getT('channel_video'), 'v')
            ->leftJoin(Phpfox::getT('channel_video_embed'), 've', 'v.video_id = ve.video_id')
            ->where('v.video_id = ' . $iItemId)
            ->execute('getSlaveRow');
        return $aItem;
    }

    /**
     * @param $iItemId
     * @return array|bool|int|string
     */
    public function getItemFromFox($iItemId)
    {
        if (!$iItemId && !Phpfox::isModule('v')) {
            return false;
        }

        $aItem = db()->select('v.*')
            ->from(Phpfox::getT('video'), 'v')
            ->where('v.video_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if ($aItem['is_stream']) {
            $aEmbedVideo = $this->database()->select('video_url, embed_code')
                ->from(Phpfox::getT('video_embed'))
                ->where('video_id = ' . $aItem['video_id'])
                ->execute('getslaveRow');
            $aItem['embed_code'] = $aEmbedVideo['embed_code'];
            $aItem['video_url'] = $aEmbedVideo['video_url'];
        } else {
            $sVideoPath = Phpfox::getParam('core.path_actual') . 'PF.Base/file/video/' . sprintf($aItem['destination'], '');
            if (Phpfox::getParam('pf_cdn_enabled') && $aItem['server_id'] > 0) {
                $sVideoPath = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('core.path_file') . 'file/video/' . sprintf($aItem['destination'], ''), $aItem['server_id']);
            }
            $imagePath = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aItem['image_server_id'],
                    'title' => $aItem['title'],
                    'path' => 'core.url_pic',
                    'file' => $aItem['image_path'],
                    'suffix' => '_500',
                    'return_url' => true
                )
            );

            $aItem['embed_code'] = '
			 <video id="player_' . $iItemId . '" class="video-player" controls
				 preload="auto" poster="' . $imagePath . '"
				 data-setup="{}">
				  <source src="' . $sVideoPath . '" type="video/mp4">
				</video>';
        }
        if(!empty($aItem['image_path']) && strpos($aItem['image_path'], 'video/') !== 0) {
            $aItem['image_path'] = 'video' . DIRECTORY_SEPARATOR . $aItem['image_path'];
        }
        return $aItem;
    }

    /**
     * @param $iItemId
     * @return array|bool|int|string
     */
    public function getItemFromFoxUltVideo($iItemId)
    {
        if (!$iItemId && !Phpfox::isModule('ultimatevideo')) {
            return false;
        }

        $aItem = $this->database()->select('v.*,v.code as video_url')
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
            ->where('v.video_id = ' . $iItemId)
            ->execute('getSlaveRow');

        if (isset($aItem['type'])) {
            $sVideoPath = Phpfox::getParam('core.path_actual') . 'PF.Base/file/ynultimatevideo/' . sprintf($aItem['video_path'], '');
            if (Phpfox::getParam('pf_cdn_enabled') && $aItem['video_server_id'] > 0 && $aItem['type'] == 3) {
                $sVideoPath = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('core.path_file') . 'file/ynultimatevideo/' . sprintf($aItem['video_path'], ''), $aItem['video_server_id']);
            }
            $sSourceType = Phpfox::getService('ultimatevideo')->getSourTypeNameFromId($aItem['type']);
            $adapter = Phpfox::getService('ultimatevideo')->getClass($sSourceType);
            $aParams = array(
                'video_id' => $aItem['video_id'],
                'code' => $aItem['code'],
                'view' => false,
                'mobile' => Phpfox::isModule('ultimatevideo') ? Phpfox::getService('ultimatevideo')->isMobile() : false,
                'count_video' => 0,
                'location' => $aItem['code'],
                'location1' => $sVideoPath,
                'duration' => $aItem['duration']
            );
            $embedCode = $adapter->compileVideo($aParams);
            $aItem['embed_code'] = $embedCode;
        } else {
            $aItem['embed_code'] = "";
        }
        return $aItem;
    }

    /**
     * @return string
     */
    public function getTemplateViewPath()
    {
        return 'contest.entry.content.video';
    }

    /**
     * @param $iItemId
     * @param int $iSourceId
     * @return array
     */
    public function getDataToInsertIntoEntry($iItemId, $iSourceId = self::CONTEST_VIDEO_ULTIMATEVIDEO)
    {
        switch ($iSourceId) {
            case self::CONTEST_VIDEO_ULTIMATEVIDEO:
                $aItem = $this->getItemFromFoxUltVideo($iItemId);
                break;
            case self::CONTEST_VIDEO_VIDEOCHANNEL:
                $aItem = $this->getItemFromFoxVideoChannel($iItemId);
                break;
            case self::CONTEST_VIDEO_COREVIDEO:
            default:
                $aItem = $this->getItemFromFox($iItemId);
                break;

        }
        $sFullSourcePath = Phpfox::getParam('core.dir_pic') . $aItem['image_path'];
        if (Phpfox::getParam('pf_cdn_enabled') && !Phpfox::getParam('core.keep_files_in_server')) {
            switch ($iSourceId) {
                // Case Ultimate Videos
                case self::CONTEST_VIDEO_ULTIMATEVIDEO:
                    $sOriginalSource = sprintf($sFullSourcePath, '_500');
                    break;
                case self::CONTEST_VIDEO_VIDEOCHANNEL:
                    $sOriginalSource = sprintf($sFullSourcePath, $this->_sOriginalSuffix);
                    break;
                case self::CONTEST_VIDEO_COREVIDEO:
                default:
                    $sOriginalSource = sprintf($sFullSourcePath, '_500');
                    break;
            }

            $sOriginalSource = str_replace(PHPFOX_DIR, Phpfox::getParam('core.actual_path'), $sOriginalSource);
            $sSrc = Phpfox::getLib('cdn')->getUrl($sOriginalSource, $aItem['image_server_id']);
            $p = PHPFOX_DIR_FILE . PHPFOX_DS . 'pic' . PHPFOX_DS . 'contest' . PHPFOX_DS;
            if (!is_dir($p)) {
                if (!@mkdir($p, 0777, 1)) {
                }
            }
            $sImage = $sSrc;

            $downloadImage = Phpfox::getService('contest.helper')->processImage(
                $sImage
                , Phpfox::getUserId()
                , Phpfox::getParam('core.dir_pic') . 'contest/'
            );

            $sNewImageFullPath = $downloadImage;
            $sOriginalDes = sprintf($sNewImageFullPath, '');

            $oImage = Phpfox::getLib('image');
            $sImagePath = str_replace(Phpfox::getParam('core.dir_pic'), '', $downloadImage);
            foreach ($this->_aImageSize as $iSize) {
                //copy images
                $oImage->createThumbnail($sOriginalDes, sprintf($sNewImageFullPath, '_' . $iSize), $iSize, $iSize);

            }
        } else {
            switch ($iSourceId) {
                // Case Ultimate Videos
                case self::CONTEST_VIDEO_ULTIMATEVIDEO:
                    $sImagePath = Phpfox::getService('contest.entry.process')->copyImageToContest($sFullSourcePath, '_500', $this->_aImageSize);
                    break;
                case self::CONTEST_VIDEO_VIDEOCHANNEL:
                    $sImagePath = Phpfox::getService('contest.entry.process')->copyImageToContest($sFullSourcePath, $this->_sOriginalSuffix, $this->_aImageSize);
                    break;
                case self::CONTEST_VIDEO_COREVIDEO:
                default:
                    if(!($sImagePath = Phpfox::getService('contest.entry.process')->copyImageToContest($sFullSourcePath, '_500', $this->_aImageSize))) {
                        $sImagePath = Phpfox::getService('contest.entry.process')->copyImageToContest($sFullSourcePath, '', $this->_aImageSize);
                    }
                    break;
            }
        }

        //copy db
        // column name here must comply with column in db
        $aReturn = array(
            'embed_code' => $aItem['embed_code'],
            'video_url' => $aItem['video_url'],
            'image_path' => $sImagePath,
            'blog_content' => "",
            'blog_content_parsed' => "",
            'total_attachment' => 0,
        );

        return $aReturn;
        //copy file

    }

    /**
     * @param $iItemId
     * @param int $iSourceId
     * @return array|bool|int|string
     */
    public function getDataFromFoxAdaptedWithContestEntryData($iItemId, $iSourceId = 1)
    {
        $aItem = array();

        switch ($iSourceId) {
            // Case Ultimate Videos
            case self::CONTEST_VIDEO_ULTIMATEVIDEO:
                $aItem = $this->getItemFromFoxUltVideo($iItemId);
                break;
            case self::CONTEST_VIDEO_VIDEOCHANNEL:
                $aItem = $this->getItemFromFoxVideoChannel($iItemId);
                break;
            case self::CONTEST_VIDEO_COREVIDEO:
            default:
                $aItem = $this->getItemFromFox($iItemId);
                break;
        }

        return $aItem;
    }
}