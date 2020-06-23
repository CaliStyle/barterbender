<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/16/16
 * Time: 11:58 AM
 */

namespace Apps\YouNet_UltimateVideos\Service\Playlist;


use Phpfox;
use Phpfox_Service;
use Phpfox_Url;
use Phpfox_Plugin;

class Process extends Phpfox_Service
{
    private $_aCategories = array();
    protected $_pTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynultimatevideo_playlists');
        $this->_pTable = Phpfox::getT('ynultimatevideo_playlist_data');
    }

    public function updateViewCount($iId)
    {
        $this->database()->query("
			UPDATE " . $this->_sTable . "
			SET total_view = total_view + 1
			WHERE playlist_id = " . (int)$iId . "
		");
        return true;
    }

    public function feature($iPlaylistId, $iIsFeatured)
    {
        if (!user('ynuv_can_feature_playlist') && !Phpfox::isAdmin()) {
            return false;
        }
        $oPlaylist = Phpfox::getService('ultimatevideo.playlist');
        $this->database()->update($this->_sTable, array('is_featured' => $iIsFeatured), "playlist_id = {$iPlaylistId}");
        $oPlaylistTitle = $this->database()->select('title')->from($this->_sTable)->where("playlist_id = {$iPlaylistId}")->execute('getSlaveField');
        if ($iIsFeatured) {
            $iOwnerId = $oPlaylist->getPlaylistOwnerId($iPlaylistId);
            if ($iOwnerId) {
                // Add notification
                $iSenderUserId = $iOwnerId;
                if ((int)Phpfox::getUserId() > 0) {
                    $iSenderUserId = Phpfox::getUserId();
                }
                Phpfox::getService("notification.process")->add("ultimatevideo_playlistfeature", $iPlaylistId, $iOwnerId, $iSenderUserId);

                // Send mail
                $text = _p('your_playlist_space') . '<a href="' . Phpfox_Url::instance()->permalink('ultimatevideo.playlist', $iPlaylistId, $oPlaylistTitle) . '">' . $oPlaylistTitle . '</a>' . _p(' is featured.');
                $aOwnerEmail = array(Phpfox::getService('ultimatevideo')->getOwnerEmail($iOwnerId));
                //Phpfox::getLib('mail')->to($aOwnerEmail)
                //    ->subject(_p('Your playlist is featured'))
                //    ->message($text)
                //    ->send();
            }
        }
        return true;
    }

    public function approved($iPlaylistId, $iIsApproved)
    {
        if (!user('ynuv_can_approve_playlist') && !Phpfox::isAdmin()) {
            return false;
        }
        $oPlaylist = Phpfox::getService('ultimatevideo.playlist');
        $this->database()->update($this->_sTable, array('is_approved' => $iIsApproved), "playlist_id = {$iPlaylistId}");
        $oPlaylistTitle = $this->database()->select('title')->from($this->_sTable)->where("playlist_id = {$iPlaylistId}")->execute('getSlaveField');
        if ($iIsApproved) {
            $iOwnerId = $oPlaylist->getPlaylistOwnerId($iPlaylistId);
            if ($iOwnerId) {
                // Add notification
                $iSenderUserId = $iOwnerId;
                if ((int)Phpfox::getUserId() > 0) {
                    $iSenderUserId = Phpfox::getUserId();
                }
                Phpfox::getService("notification.process")->add("ultimatevideo_playlistapprove", $iPlaylistId, $iOwnerId, $iSenderUserId);

                // Send mail
                $text = _p('your_playlist_space') . '<a href="' . Phpfox_Url::instance()->permalink('ultimatevideo.view', $iPlaylistId, $oPlaylistTitle) . '">' . $oPlaylistTitle . '</a>' . _p(' is approved.');
                $aOwnerEmail = array(Phpfox::getService('ultimatevideo')->getOwnerEmail($iOwnerId));
                //Phpfox::getLib('mail')->to($aOwnerEmail)
                //    ->subject(_p('Your playlist is approved'))
                //    ->message($text)
                //    ->send();
            }

            Phpfox::getService('user.activity')->update($oPlaylist->getPlaylistOwnerId($iPlaylistId), 'ultimatevideo_playlist', '+');
        }

        return true;
    }

    public function delete($iPlaylistId, $bForce = false)
    {
        $aPlaylist = $this->database()->select('*')
            ->from($this->_sTable)
            ->where("playlist_id =" . (int)$iPlaylistId)
            ->execute('getRow');
        if (!$aPlaylist || (Phpfox::getUserId() != $aPlaylist['user_id'] && !user('ynuv_can_delete_playlist_of_other_user')) || (Phpfox::getUserId() == $aPlaylist['user_id'] && !user('ynuv_can_delete_own_playlists') && !user('ynuv_can_delete_playlist_of_other_user'))) {
            if (!$bForce) {
                return false;
            }
        }
        if (!empty($aPlaylist['image_path'])) {
            $sImagePath = $aPlaylist['image_path'];
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
            }
        }

        $this->database()->delete($this->_pTable, "playlist_id = " . (int)$iPlaylistId);
        $this->database()->delete(Phpfox::getT('ynultimatevideo_history'), "item_id = " . (int)$iPlaylistId . " AND item_type ='1'");
        $this->database()->delete($this->_sTable, "playlist_id = " . (int)$iPlaylistId);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('ultimatevideo_playlist', (int)$iPlaylistId) : null);
        Phpfox::getService('ultimatevideo.process')->updateCountVideoForCategory($aPlaylist['category_id'], "delete");
        $variable = user('ultimatevideo.points_ultimatevideo_playlist');
        $checkInt = filter_var($variable, FILTER_VALIDATE_INT);
        if ($checkInt && $variable > 0) {
            Phpfox::getService('user.activity')->update($aPlaylist['user_id'], 'ultimatevideo_playlist', '-');
        }
        return true;
    }

    public function add($aVals, $isQuickAdd = false, $iVideoId = 0)
    {
        $oFilter = Phpfox::getLib('parse.input');
        $this->getCategoriesFromForm($aVals);
        // Check callback.
        $aCallback = null;
        if (isset($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'playlist')) {
            $aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadVideo', $aVals);
        }
        $description = $aVals['description'];
        $description = $oFilter->prepare($description);
        $aSql = array(
            'user_id' => Phpfox::getUserId(),
            'title' => (empty($aVals['title'])) ? "" : $oFilter->clean($aVals['title'], 255),
            'privacy' => (int)(isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
            'time_stamp' => PHPFOX_TIME,
            'category_id' => (isset($this->_aCategories) && end($this->_aCategories)) ? end($this->_aCategories) : 0,
            'description' => (isset($aVals['description']) && !empty($aVals['description'])) ? $description : "",
            'image_server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'is_approved' => (user('ynuv_should_be_approve_before_display_playlist')) ? 0 : 1,
            'view_mode' => (isset($aVals['view_mode'])) ? $aVals['view_mode'] : 1,
        );

        $iPlaylistId = $this->database()->insert($this->_sTable, $aSql);

        if ($iPlaylistId && !user('ynuv_should_be_approve_before_display_playlist')) {
            ((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? $iFeedId = Phpfox::getService('feed.process')->add('ultimatevideo_playlist', $iPlaylistId, (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0), 0, 0) : null);
            Phpfox::getService('ultimatevideo.process')->updateCountVideoForCategory(end($this->_aCategories), "add");
            $variable = user('ultimatevideo.points_ultimatevideo_playlist');
            $checkInt = filter_var($variable, FILTER_VALIDATE_INT);
            if ($checkInt && $variable > 0) {
                Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'ultimatevideo_playlist', '+');
            }
        }
        //add privacy
        if (isset($aVals['privacy']) && $aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('ultimatevideo_playlist', $iPlaylistId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }
        // plugin call
        if (!$isQuickAdd) {
            (($sPlugin = Phpfox_Plugin::get('ultimatevideo.service_playlist_process_add__end')) ? eval($sPlugin) : false);
        }
        return $iPlaylistId;
    }

    public function update($aVals, $iPlaylistId)
    {
        $oFilter = Phpfox::getLib('parse.input');
        $this->getCategoriesFromForm($aVals);
        // Check callback.
        $aCallback = null;
        if (isset($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'uploadVideo')) {
            $aCallback = Phpfox::callback($aVals['callback_module'] . '.playlist', $aVals);
        }
        $description = $aVals['description'];
        $description = $oFilter->prepare($description);
        $aSql = array(
            'title' => (empty($aVals['title'])) ? "" : $oFilter->clean($aVals['title'], 255),
            'privacy' => (int)(isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
            'category_id' => end($this->_aCategories),
            'description' => (empty($aVals['description'])) ? "" : $description,
            'view_mode' => (isset($aVals['view_mode'])) ? $aVals['view_mode'] : 1,
        );
        $this->database()->update($this->_sTable, $aSql, 'playlist_id = ' . (int)$iPlaylistId);
        //update privacy
        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == '4') {
                Phpfox::getService('privacy.process')->update('ultimatevideo_playlist', $iPlaylistId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            } else {
                Phpfox::getService('privacy.process')->delete('ultimatevideo_playlist', $iPlaylistId);
            }
        }

        if(Phpfox::isModule('feed')) {
            Phpfox::getService('feed.process')->update('ultimatevideo_playlist', $iPlaylistId, $aVals['privacy'], 0);
        }

        return $iPlaylistId;
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

        return true;
    }

    public function processuploadImage($aVals, $iPlaylistId)
    {
        if ($iPlaylistId) {
            $aVideoImage = $this->database()->select('image_path, image_server_id')
                ->from($this->_sTable)
                ->where('playlist_id = ' . $iPlaylistId)
                ->execute('getSlaveRow');
        }

        if (!empty($aVals['temp_file']) || !empty($aVals['remove_logo'])) {
            $aVideoImage['image_path'] = null;
            $aVideoImage['image_server_id'] = 0;
        }

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aVideoImage['image_path'] = 'ynultimatevideo' . PHPFOX_DS . $aFile['path'];
                $aVideoImage['image_server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }
        $this->database()->update($this->_sTable, array('image_server_id' => $aVideoImage['image_server_id'], 'image_path' => $aVideoImage['image_path']), 'playlist_id=' . $iPlaylistId);
        return true;
    }

    public function uploadImage($iPlaylistId)
    {
        if ($iPlaylistId) {
            $imagePath = $this->database()->select('image_path')
                ->from($this->_sTable)
                ->where('playlist_id = ' . $iPlaylistId)
                ->execute('getSlaveField');
        }
        if (isset($_FILES['imageUpload']['name']) && empty($_FILES['imageUpload']['name'])) {
            return \Phpfox_Error::set(_p('please_select_a_file_to_upload'));
        } else {
            $maxFileSize = ((int)user('ynuv_max_file_size_photos_upload') > 0) ? ((int)user('ynuv_max_file_size_photos_upload')) / 1024 : null;
            $aImage = Phpfox::getLib('file')->load('imageUpload', array(
                'jpg', 'gif', 'png'
            ), $maxFileSize);
            if ($aImage) {
                if ($imagePath) {

                    $aImages = array(
                        Phpfox::getParam('core.dir_pic') . sprintf($imagePath, '_120'),
                        Phpfox::getParam('core.dir_pic') . sprintf($imagePath, '_250'),
                        Phpfox::getParam('core.dir_pic') . sprintf($imagePath, '_500'),
                        Phpfox::getParam('core.dir_pic') . sprintf($imagePath, '_1024')
                    );
                    foreach ($aImages as $sImage) {
                        if (file_exists($sImage)) {
                            @unlink($sImage);
                        }
                    }
                }
                $sPicStorage = Phpfox::getParam('core.dir_pic') . 'ynultimatevideo/';

                if (!is_dir($sPicStorage)) {
                    @mkdir($sPicStorage, 0777, 1);
                    @chmod($sPicStorage, 0777);
                }
                $sNewFileName = Phpfox::getLib('file')->upload('imageUpload', $sPicStorage, PHPFOX_TIME);
                Phpfox::getLib('image')->createThumbnail($sPicStorage . sprintf($sNewFileName, ''), $sPicStorage . sprintf($sNewFileName, '_' . 120), 120, 120);
                Phpfox::getLib('image')->createThumbnail($sPicStorage . sprintf($sNewFileName, ''), $sPicStorage . sprintf($sNewFileName, '_' . 250), 250, 250);
                Phpfox::getLib('image')->createThumbnail($sPicStorage . sprintf($sNewFileName, ''), $sPicStorage . sprintf($sNewFileName, '_' . 500), 500, 500);
                Phpfox::getLib('image')->createThumbnail($sPicStorage . sprintf($sNewFileName, ''), $sPicStorage . sprintf($sNewFileName, '_' . 1024), 1024, 1024);

                $this->database()->update($this->_sTable, array('image_path' => 'ynultimatevideo/' . $sNewFileName), 'playlist_id=' . (int)$iPlaylistId);
                $sTempFile = $sPicStorage . sprintf($sNewFileName, '');
                if (file_exists($sTempFile)) {
                    @unlink($sTempFile);
                }
                return true;
            }
        }

        return true;
    }

    public function addVideo($iVideoId, $iPlaylistId)
    {
        $aVideoDetail = $this->database()->select('status,is_approved')
            ->from(Phpfox::getT('ynultimatevideo_videos'))
            ->where('video_id =' . (int)$iVideoId)
            ->execute('getRow');
        if (!$aVideoDetail || $aVideoDetail['status'] != 1 || $aVideoDetail['is_approved'] == 0) {
            return false;
        }
        $aVideo = $this->database()->select('pd.*,p.title as playlist_title')
            ->from($this->_pTable, 'pd')
            ->join($this->_sTable, 'p', 'p.playlist_id = pd.playlist_id')
            ->where('pd.video_id =' . (int)$iVideoId . ' AND pd.playlist_id=' . (int)$iPlaylistId)
            ->execute('getSlaveRow');
        $aPlaylist = Phpfox::getService('ultimatevideo.playlist')->getPlaylistById($iPlaylistId);

        if (!empty($aVideo)) {
            $this->database()->update($this->_pTable, array('time_stamp' => PHPFOX_TIME), 'video_id =' . (int)$iVideoId . ' AND playlist_id=' . (int)$iPlaylistId);
            return false;
        } elseif (!empty($aPlaylist) && ((int)$aPlaylist['total_video'] < (int)user('ynuv_how_many_video_user_can_add_to_playlist'))) {
            $aPlaylist = $this->database()->select('image_path,total_video')
                ->from($this->_sTable)
                ->where('playlist_id =' . $iPlaylistId)
                ->execute('getRow');
            if ($aPlaylist['total_video'] == 0 && empty($aPlaylist['image_path'])) {
                $aImagePath = $this->database()->select('image_path,image_server_id')
                    ->from(Phpfox::getT('ynultimatevideo_videos'))
                    ->where('video_id =' . $iVideoId)
                    ->execute('getSlaveRow');
                if (!empty($aImagePath['image_path'])) {
                    $sImagePath = $aImagePath['image_path'];
                    $iServerId = $aImagePath['image_server_id'];
                    $iToken = rand();
                    $sThumbNail = Phpfox::getLib('file')->getBuiltDir(Phpfox::getParam('core.dir_pic') . 'ynultimatevideo' . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '%s.jpg';
                    $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $sThumbNail);
                    $sFileName = str_replace("\\", "/", $sFileName);
                    $aPlaylistImages = array(
                        Phpfox::getParam('core.dir_pic') . sprintf($sFileName, '_120'),
                        Phpfox::getParam('core.dir_pic') . sprintf($sFileName, '_250'),
                        Phpfox::getParam('core.dir_pic') . sprintf($sFileName, '_500'),
                        Phpfox::getParam('core.dir_pic') . sprintf($sFileName, '_1024')
                    );
                    $aVideoImages = array(
                        Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_120'),
                        Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_250'),
                        Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_500'),
                        Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_1024')
                    );
                    $sVPath = $iServerId == -1 ? (Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') . $sImagePath) : Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('core.dir_pic'), Phpfox::getParam('core.url_pic'), $aVideoImages[3]), $iServerId);
                    $sFileName = Phpfox::getService('ultimatevideo.process')->downloadImage($sVPath);
                    foreach ($aPlaylistImages as $key => $aPlaylistImage) {
                        if (file_exists($aVideoImages[$key]))
                            Phpfox::getLib('file')->copy($aVideoImages[$key], $aPlaylistImages[$key]);
                    }
                    $this->database()->update($this->_sTable, array('image_path' => $sFileName, 'image_server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')), 'playlist_id = ' . $iPlaylistId);
                }
            }

            $iDataId = $this->database()->insert($this->_pTable, array(
                'video_id' => $iVideoId,
                'playlist_id' => $iPlaylistId,
                'time_stamp' => PHPFOX_TIME,
            ));
            $this->updateCountVideoForPlaylist($iPlaylistId, "add");
            return true;
        }
        return false;
    }

    public function removeVideo($iVideoId, $iPlaylistId)
    {
        $aVideo = $this->database()->select('pd.*,p.title as playlist_title')
            ->from($this->_pTable, 'pd')
            ->join($this->_sTable, 'p', 'p.playlist_id = pd.playlist_id')
            ->where('pd.video_id =' . (int)$iVideoId . ' AND pd.playlist_id=' . (int)$iPlaylistId)
            ->execute('getSlaveRow');
        if ($aVideo) {
            $this->database()->delete($this->_pTable, 'video_id =' . (int)$iVideoId . ' AND playlist_id=' . (int)$iPlaylistId);
            $this->updateCountVideoForPlaylist($iPlaylistId, "delete");
            return true;
        }
        return false;
    }

    public function updateOrder($aVals, $iPlaylistId)
    {
        foreach ($aVals as $iId => $iOrder) {
            $this->database()->update($this->_pTable, array('ordering' => $iOrder), 'video_id = ' . (int)$iId . ' AND playlist_id =' . $iPlaylistId);
        }

        return true;
    }

    public function updateCountVideoForPlaylist($iPlaylistId, $sType)
    {
        $totalCount = $this->database()->select('total_video')
            ->from($this->_sTable)
            ->where('playlist_id = ' . (int)$iPlaylistId)
            ->execute('getSlaveField');

        switch ($sType) {
            case "add":
                $totalCount = (int)$totalCount + 1;
                $this->database()->update($this->_sTable, array('total_video' => $totalCount), 'playlist_id = ' . (int)$iPlaylistId);
                break;
            case "delete":
                $totalCount = (int)$totalCount - 1;
                $this->database()->update($this->_sTable, array('total_video' => $totalCount), 'playlist_id = ' . (int)$iPlaylistId);
                break;
        }
    }
}