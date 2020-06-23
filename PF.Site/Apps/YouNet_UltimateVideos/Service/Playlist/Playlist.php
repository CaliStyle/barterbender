<?php

namespace Apps\YouNet_UltimateVideos\Service\Playlist;

use Phpfox_Service;
use Phpfox;

class Playlist extends Phpfox_Service
{
    protected $_pTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_playlists');
        $this->_pTable = Phpfox::getT('ynultimatevideo_playlist_data');
    }

    /**
     * Get number of playlists of user
     * @param $iUserId
     * @return int
     */
    public function countMyPlaylistsOfUser($iUserId)
    {
        $iCnt = db()->select('COUNT(*)')
            ->from($this->_sTable, 'playlist')
            ->where('playlist.user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');
        return (int)$iCnt;
    }

    /**
     * @param $aVals
     */
    public function add($aVals)
    {
        $data = [
            'title' => 'Untitled Title',
            'user_id' => 1,
            'category_id' => 1,
            'time_stamp' => time(),
            'description' => '',
            'image_path' => 'ynultimatevideo/2016/08/890d586341c66ea62bc39949a3e86e4c%s.jpg',
            'image_server_id' => 0,
        ];


        $this->database()->insert($this->_sTable, $data);
    }

    public function getQueryJoins()
    {

    }

    public function getManagePlaylist($aConds = array(), $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {
        $sWhere = '';
        $sWhere .= '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = $this->database()
            ->select("COUNT(uvp.playlist_id)")
            ->from($this->_sTable, 'uvp')
            ->join(Phpfox::getT("user"), 'u', 'uvp.user_id =  u.user_id')
            ->leftjoin(Phpfox::getT('ynultimatevideo_category'), 'uvc', 'uvc.category_id = uvp.category_id')
            ->where($sWhere)
            ->execute("getSlaveField");
        $aPlaylists = array();
        if ($iCount) {
            $aPlaylists = $this->database()
                ->select("uvp.*,uvc.title as category_title," . Phpfox::getUserField())
                ->from($this->_sTable, 'uvp')
                ->join(Phpfox::getT("user"), 'u', 'uvp.user_id =  u.user_id')
                ->leftjoin(Phpfox::getT('ynultimatevideo_category'), 'uvc', 'uvc.category_id = uvp.category_id')
                ->where($sWhere)
                ->order('uvp.playlist_id DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }

        return array($iCount, $aPlaylists);
    }

    public function getPlaylistOwnerId($iPlaylistId)
    {
        return $this->database()->select('user_id')->from($this->_sTable)->where("playlist_id = {$iPlaylistId}")->execute('getSlaveField');
    }

    public function getForEdit($iPlaylistId)
    {
        $aPlaylist = $this->database()->select("p.*")
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.playlist_id =' . $iPlaylistId)
            ->execute('getSlaveRow');

        if ($aPlaylist) {
            $aCategories = array();
            $aCategories[] = $aPlaylist['category_id'];
            $iParent = Phpfox::getService('ultimatevideo.category')->getParentCategoryId($aPlaylist['category_id']);
            while ($iParent != 0) {
                $aCategories[] = $iParent;
                $iParent = Phpfox::getService('ultimatevideo.category')->getParentCategoryId($iParent);
            }
            $aPlaylist['categories'] = json_encode($aCategories);
            return $aPlaylist;
        } else {
            return false;
        }
    }

    public function getAllPlaylistOfUser($iVideoId, $bIncludePending = true)
    {
        $sWhere = 'p.user_id =' . (int)Phpfox::getUserId();
        if (!$bIncludePending) {
            $sWhere .= ' AND is_approved = 1';
        }
        $aPlaylists = $this->database()->select('p.*')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where($sWhere)
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');
        if (count($aPlaylists)) {
            foreach ($aPlaylists as $key => $aPlaylist) {
                $aData = $this->database()->select('video_id,playlist_id')
                    ->from($this->_pTable)
                    ->where('video_id=' . (int)$iVideoId . ' AND playlist_id=' . (int)$aPlaylist['playlist_id'])
                    ->execute('getSlaveRow');
                if ($aData) {
                    $aPlaylists[$key]['added_video'] = 1;
                } else {
                    $aPlaylists[$key]['added_video'] = 0;
                }
            }
        }
        return $aPlaylists;
    }

    public function getPlaylistById($iPlaylistId)
    {
        return $aPlaylist = $this->database()->select("*")
            ->from($this->_sTable)
            ->where('playlist_id =' . (int)$iPlaylistId)
            ->execute('getSlaveRow');

    }

    public function getVideosManage($iPlaylistId)
    {
        return $this->database()->select("v.*,pd.ordering as ordering," . Phpfox::getUserField())
            ->from($this->_pTable, 'pd')
            ->join($this->_sTable, 'p', 'p.playlist_id = pd.playlist_id')
            ->join(Phpfox::getT('ynultimatevideo_videos'), 'v', 'pd.video_id = v.video_id')
            ->join(Phpfox::getT("user"), 'u', 'v.user_id =  u.user_id')
            ->where('pd.playlist_id = ' . (int)$iPlaylistId)
            ->order('pd.ordering ASC')
            ->execute('getSlaveRows');
    }

    public function getVideosListing($iPlaylistId, $iPage = 0, $iLimit = NULL, $iCount = NULL)
    {
        $iCount = $this->database()
            ->select("COUNT(uvid.video_id)")
            ->from(Phpfox::getT('ynultimatevideo_videos'), 'uvid')
            ->join(Phpfox::getT("user"), 'u', 'uvid.user_id =  u.user_id')
            ->join($this->_pTable, 'pd', 'pd.video_id = uvid.video_id')
            ->where('pd.playlist_id = ' . (int)$iPlaylistId)
            ->execute("getSlaveField");
        $aVideos = array();
        if ($iCount) {
            $aVideos = $this->database()
                ->select("uvid.*,pd.ordering," . Phpfox::getUserField())
                ->from(Phpfox::getT('ynultimatevideo_videos'), 'uvid')
                ->join(Phpfox::getT("user"), 'u', 'uvid.user_id =  u.user_id')
                ->join($this->_pTable, 'pd', 'pd.video_id = uvid.video_id')
                ->where('pd.playlist_id = ' . (int)$iPlaylistId . ' AND uvid.status = 1 AND uvid.is_approved = 1')
                ->order('pd.ordering ASC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute("getSlaveRows");

            Phpfox::getService('ultimatevideo.browse')->processRows($aVideos);

        }
        return array($iCount, $aVideos);
    }

    public function getVideosSlideShow($iPlaylistId)
    {
        $aVideos = $this->database()->select("v.*, pd.ordering as ordering," . Phpfox::getUserField())
            ->from($this->_pTable, 'pd')
            ->join($this->_sTable, 'p', 'p.playlist_id = pd.playlist_id')
            ->join(Phpfox::getT('ynultimatevideo_videos'), 'v', 'pd.video_id = v.video_id')
            ->join(Phpfox::getT("user"), 'u', 'v.user_id =  u.user_id')
            ->where('pd.playlist_id = ' . (int)$iPlaylistId . ' AND v.status = 1 AND v.is_approved = 1')
            ->order('pd.ordering ASC')
            ->execute('getSlaveRows');
        if ($aVideos) {
            foreach ($aVideos as $key => $aVideo) {
                if (isset($aVideo['type'])) {

                    $sSourceType = Phpfox::getService('ultimatevideo')->getSourTypeNameFromId($aVideo['type']);
                    $adapter = Phpfox::getService('ultimatevideo')->getClass($sSourceType);
                    $aParams = array(
                        'video_id' => $aVideo['video_id'],
                        'code' => $aVideo['code'],
                        'view' => true,
                        'mobile' => Phpfox::getService('ultimatevideo')->isMobile(),
                        'count_video' => 0,
                        'location' => $aVideo['code'],
                        'location1' => ($aVideo['video_server_id'] == -1 ? Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') :  Phpfox::getParam('core.path_actual') . 'PF.Base/file/ynultimatevideo/') . sprintf($aVideo['video_path'], ''),
                        'duration' => $aVideo['duration'],
                        'slide' => true,
                    );
                    $aVideos[$key]['embed_code'] = $adapter->extractVideo($aParams);
                    if($aVideo['image_server_id'] == -1) {
                        $aVideos[$key]['image_path'] = Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') . $aVideo['image_path'];
                    }
                }
            }
        }
        return $aVideos;
    }

    public function getAllPlaylistOfVideo($iVideoId)
    {
        if (!$iVideoId)
            return false;
        return $aPlaylists = $this->database()->select('p.playlist_id')
            ->from($this->_pTable, 'pd')
            ->join($this->_sTable, 'p', 'pd.playlist_id = p.playlist_id')
            ->join(Phpfox::getT('ynultimatevideo_videos'), 'v', 'pd.video_id = v.video_id')
            ->where('pd.video_id =' . (int)$iVideoId)
            ->execute('getSlaveRows');
    }

    public function getPlaylistsOfCurrentUser($iLimit = 0)
    {
        $aPlaylists = $this->database()
            ->select('p.*')
            ->from($this->_sTable, 'p')
            ->where('p.user_id =' . (int)Phpfox::getUserId())
            ->limit($iLimit)
            ->execute('getSlaveRows');

        return $aPlaylists;
    }
}