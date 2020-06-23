<?php

defined('PHPFOX') or exit('NO DICE!');
require_once dirname(dirname(__file__)).'/item/item_abstract.class.php';

class Contest_Service_Entry_Item_Music extends Phpfox_service implements Contest_Service_Entry_Item_Item_Abstract
{
    private $_aImageSize = array(30, 50, 120, 200);

    /**
     * [$_sOriginalSuffix string of the original images
     * @var string
     */
    private $_sOriginalSuffix = '_120';

    public function __construct()
    {
        if (Phpfox::isModule('musicsharing'))
        {
            $this->_sModuleName = 'musicsharing';
            $this->_sTable = Phpfox::getT('m2bmusic_album_song');
            $this->_sTableAlbum = Phpfox::getT('m2bmusic_album');
        }
        else
        {
            $this->_sModuleName = 'music';
            $this->_sTable = Phpfox::getT('music_song');
            $this->_sTableAlbum = Phpfox::getT('music_album');
        }
    }

    public function getAddNewItemLink($iContestId, $iSourceId = 1)
    {
        $sAddParamName = Phpfox::getService('contest.constant')->getYnAddParamForNavigateBack();

        if ($this->_sModuleName == 'musicsharing')
        {
            $sLink = Phpfox::getLib('url')->makeUrl('musicsharing.upload', array($sAddParamName => $iContestId));
        }
        else
        {
            $sLink = Phpfox::getLib('url')->makeUrl('music.upload', array($sAddParamName => $iContestId));
        }

        return $sLink;
    }

    public function getItemsOfCurrentUser($iLimit = 5, $iPage = 0,$iSourceId = 2)
    {
        if ($this->_sModuleName == 'musicsharing')
        {
            $sConds = 'a.user_id = '.Phpfox::getUserId().' ';
            $sOrder = 's.song_id DESC';
            $sFieldImage = 'a.album_image as image_path';
        }
        else
        {
            $sConds = 's.user_id = '.Phpfox::getUserId().' ';
            $sOrder = 's.time_stamp DESC';
            $sFieldImage = 'a.image_path as image_path, s.image_path as song_image_path';
        }
        
        //in case we encounter a post form, we know it is a search request
        if ($iSearchId = Phpfox::getLib('request')->get('search-id'))
        {
            $sKeyword = Phpfox::getService('contest.helper')->getSearchKeyword($iSearchId);
            $sConds .= $this->database()->search($sType = 'like%', $mField = array('s.title'), $sSearch = $sKeyword);
        }

        // by default we only get items of current user
        $iCnt = $this->database()->select('COUNT(*)')
        ->from($this->_sTable, 's')
        ->leftJoin($this->_sTableAlbum, 'a', 'a.album_id = s.album_id')
        ->where($sConds)
        ->execute('getSlaveField');

        if ($iCnt > 0)
        {
            $aItems = $this->database()->select('s.*, s.server_id as song_server_id, a.user_id, a.server_id as image_server_id,' . $sFieldImage)
            ->from($this->_sTable, 's')
            ->leftJoin($this->_sTableAlbum, 'a', 'a.album_id = s.album_id')
            ->where($sConds)
            ->limit($iPage, $iLimit, $iCnt)
            ->order($sOrder)
            ->execute('getSlaveRows');
            foreach ($aItems as $key => &$aItem) {
                if (isset($aItem['server_id'])) {
                    $aItem['image_server_id'] = $aItem['server_id'];
                }
                if(isset($aItem['song_image_path']) && !empty($aItem['song_image_path'])) {
                    $aItem['image_path'] = $aItem['song_image_path'];
                }
                if (!empty($aItem['image_path'])) {
                    $aItem['image_path'] = $this->_sModuleName . DIRECTORY_SEPARATOR . $aItem['image_path'];
                }
            }
        }
        else
        {
            $aItems = array();
        }

        return array($iCnt, $aItems);
    }

    public function getItemFromFox($iItemId)
    {
        if (!$iItemId)
        {
            return false;
        }

        if ($this->_sModuleName == 'musicsharing')
        {
            $sFieldImage = 'a.album_image as image_path';
        }
        else
        {
            $sFieldImage = 'a.image_path as image_path, s.image_path as song_image_path';
        }
        
        $aItem = $this->database()->select('s.*, s.server_id as song_server_id, a.user_id, a.server_id as image_server_id, ' . $sFieldImage)
        ->from($this->_sTable, 's')
        ->leftJoin($this->_sTableAlbum, 'a', 'a.album_id = s.album_id')
        ->where('s.song_id = '.$iItemId)
        ->execute('getSlaveRow');

        if (!empty($aItem))
        {
            if ($this->_sModuleName == 'musicsharing')
            {
                $aItem['song_path'] = 'musicsharing'.PHPFOX_DS.$aItem['url'];
            }
            else
            {
                if(isset($aItem['song_image_path']) && !empty($aItem['song_image_path'])) {
                    $aItem['image_path'] = $aItem['song_image_path'];
                }
                $aItem['song_path'] = 'music'.PHPFOX_DS.sprintf($aItem['song_path'], '');
            }
        }
        
        return $aItem;
    }

    public function getTemplateViewPath()
    {
        return 'contest.entry.content.music';
    }

    public function getDataToInsertIntoEntry($iItemId, $iSourceId = 2)
    {
        $aItem = $this->getItemFromFox($iItemId);

        $sFullSourcePath = Phpfox::getParam('core.dir_pic') . $this->_sModuleName . DIRECTORY_SEPARATOR . $aItem['image_path'];
        if (Phpfox::getParam('pf_cdn_enabled') && !Phpfox::getParam('core.keep_files_in_server')) {
            $sOriginalSource = sprintf($sFullSourcePath, '');

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
            $sImagePath = Phpfox::getService('contest.entry.process')->copyImageToContest($sFullSourcePath, '', $this->_aImageSize);
        }

        $aReturn = array(
            'image_path' => $sImagePath,
            'song_path' => $aItem['song_path'],
            'song_server_id' => $aItem['song_server_id'],
            'blog_content' => "",
            'blog_content_parsed' => "",
            'total_attachment' => 0,
            'embed_code' => '<audio class="yncontest-audio-skin" class="mejs" width="493" src="'.Phpfox::getService('contest.entry.item.music')->getSongPath($aItem['song_path'], $aItem['song_server_id']).'" type="audio/mp3" controls="controls" preload="none"></audio>'
        );

        return $aReturn;
    }

    public function getDataFromFoxAdaptedWithContestEntryData($iItemId, $iSourceId = 2)
    {
        $aItem = $this->getItemFromFox($iItemId);
        return $aItem;
    }

    public function getSongPath($sSong, $iServerId = null)
    {
        if (Phpfox::getParam('pf_cdn_enabled') && !Phpfox::getParam('core.keep_files_in_server') && !empty($iServerId)) {
            $sSong = 'file' . PHPFOX_DS . $sSong;
            $sTempSong = Phpfox::getLib('cdn')->getUrl($sSong, $iServerId);
            if (!empty($sTempSong)) {
                $sSong = $sTempSong;
            }
        } else {
            $sSong = Phpfox::getParam('core.path_file') . 'file' . PHPFOX_DS . $sSong;
        }

        return $sSong;
    }
}
