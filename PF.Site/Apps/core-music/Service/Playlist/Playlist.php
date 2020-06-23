<?php

namespace Apps\Core_Music\Service\Playlist;
defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

class Playlist extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('music_playlist');
    }

    public function checkPermission(&$aPlaylist)
    {
        $aPlaylist['canEdit'] = (Phpfox::getUserParam('music.can_edit_own_playlists') && (Phpfox::getUserId() == $aPlaylist['user_id'])) || (Phpfox::getUserParam('music.can_edit_other_music_playlists'));
        $aPlaylist['canDelete'] = (Phpfox::getUserParam('music.can_delete_own_music_playlist') && (Phpfox::getUserId() == $aPlaylist['user_id'])) || (Phpfox::getUserParam('music.can_delete_other_music_playlists'));

        $aPlaylist['canHasPermission'] = ($aPlaylist['canEdit'] || $aPlaylist['canDelete'] );
    }


    /**
     * @return int
     */
    public function getMyPlaylistTotal()
    {
        return db()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveField');
    }

    public function getForEdit($iId, $bForce = false)
    {
        $aItem = db()->select('mp.*')
                    ->from($this->_sTable,'mp')
                    ->join(':user', 'u', 'u.user_id = mp.user_id')
                    ->where('mp.playlist_id = '. (int)$iId)
                    ->execute('getSlaveRow');

        if (!$aItem) {
            return Phpfox_Error::set(_p('unable_to_find_the_playlist_you_want_to_edit'));
        }

        if (!$bForce && (($aItem['user_id'] != Phpfox::getUserId() || !Phpfox::getUserParam('music.can_edit_own_playlists')) && !Phpfox::getUserParam('music.can_edit_other_music_playlists'))) {
            return Phpfox_Error::set(_p('you_do_not_have_permission_to_edit_this_playlist'));
        }
        if (!empty($aItem['image_path'])) {
            $aItem['current_image'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aItem['server_id'],
                    'path' => 'music.url_image',
                    'file' => $aItem['image_path'],
                    'suffix' => '_200_square',
                    'return_url' => true
                )
            );
        }
        return $aItem;
    }

    public function getAllSongs($iId, $bIsDetail = false)
    {
        $aSongs = db()->select('ms.*, mpd.ordering, ' . Phpfox::getUserField())
            ->from(':music_playlist_data', 'mpd')
            ->join($this->_sTable, 'mp', 'mp.playlist_id = mpd.playlist_id')
            ->join(':user', 'u', 'u.user_id = mp.user_id')
            ->join(':music_song', 'ms', 'ms.song_id = mpd.song_id')
            ->order('mpd.ordering ASC')
            ->where('mpd.playlist_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        if (count($aSongs) && $bIsDetail) {
            foreach ($aSongs as $iKey => $aSong) {
                Phpfox::getService('music')->getPermissions($aSongs[$iKey]);
                $aSongs[$iKey]['song_path'] = Phpfox::getService('music')->getSongPath($aSong['song_path'],
                    $aSong['server_id']);
                $aSongs[$iKey]['genres'] = Phpfox::getService('music.genre')->getGenreDetailBySong($aSong['song_id']);
            }
        }
        return $aSongs;
    }

    public function getPlaylist($iId)
    {
        if (Phpfox::isModule('track')) {
            $sJoinQuery = Phpfox::isUser() ? 'pt.user_id = ' . Phpfox::getUserBy('user_id') : 'pt.ip_address = \'' . $this->database()->escape(Phpfox::getIp()) . '\'';
            $this->database()->select('pt.item_id AS is_viewed, ')
                ->leftJoin(Phpfox::getT('track'), 'pt',
                    'pt.item_id = mp.playlist_id AND pt.type_id=\'music_playlist\' AND ' . $sJoinQuery);
        }
        if (Phpfox::isModule('friend')) {
            db()->select('f.friend_id AS is_friend, ')
                ->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = mp.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }
        if (Phpfox::isModule('like')) {
            db()->select('l.like_id AS is_liked, ') ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'music_playlist\' AND l.item_id = mp.playlist_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aItem = db()->select('mp.*, ' . (Phpfox::getParam('core.allow_html') ? 'mp.description_parsed' : 'mp.description') . ' AS description, u.user_name, ' . Phpfox::getUserField())
            ->from($this->_sTable,'mp')
            ->join(':user', 'u', 'u.user_id = mp.user_id')
            ->where('mp.playlist_id = '. (int)$iId)
            ->execute('getSlaveRow');

        if (!$aItem) {
            return Phpfox_Error::set(_p('playlist_not_found'));
        }
        $aItem['bookmark'] = \Phpfox_Url::instance()->makeUrl('music.playlist.'.$aItem['playlist_id'].'.'.$aItem['name']);
        return $aItem;
    }

    public function getAllPlaylist($iUserId, $iSongId = null)
    {
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }
        $aPlaylists = db()->select('mp.*, mpd.id')
            ->from($this->_sTable, 'mp')
            ->join(':user', 'u', 'u.user_id = mp.user_id')
            ->leftJoin(':music_playlist_data','mpd', 'mpd.playlist_id = mp.playlist_id' .($iSongId != null ? ' AND mpd.song_id ='. (int)$iSongId : ''))
            ->where('mp.user_id =' . (int)$iUserId)
            ->order('mp.playlist_id ASC')
            ->execute('getSlaveRows');

        return $aPlaylists;
    }

    public function getPlaylists($aCondition = [], $iLimit = 4, $iPage = 1)
    {
        $aCondition[] = 'AND mp.user_id =' . Phpfox::getUserId();

        $aPlaylists = db()->select('mp.*')
            ->from($this->_sTable, 'mp')
            ->join(':user', 'u', 'u.user_id = mp.user_id')
            ->where($aCondition)
            ->limit($iPage, $iLimit)
            ->execute('getSlaveRows');

        return $aPlaylists;
    }
}