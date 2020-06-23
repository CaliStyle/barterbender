<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/11/16
 * Time: 5:01 PM
 */

namespace Apps\YouNet_UltimateVideos\Service\Playlist;

use Phpfox_Service;
use Phpfox;

class Browse extends Phpfox_Service
{

    public function sample($iLimit, $sOrder, $aWhere = [], $bIsUserRelatePlaylist = false)
    {
        $aWhere [] = 'playlist.is_approved=1';
        $aWhere [] = 'playlist.privacy IN (%PRIVACY%)';
        if (Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !$bIsUserRelatePlaylist) {
            $aWhere[] = 'playlist.user_id=' . Phpfox::getUserId();
        }
        $aWhere = implode(' AND ', $aWhere);

        $aBrowseParams = [
            'module_id' => 'ultimatevideo',
            'alias' => 'playlist',
            'field' => 'playlist_id',
            'table' => Phpfox::getT('ynultimatevideo_playlists'),
            'service' => 'ultimatevideo.playlist.browse'
        ];
        Phpfox::getService('privacy')->buildPrivacy($aBrowseParams, $sOrder, 0, $iLimit, ' AND ' . $aWhere, true);
        return $this->database()->execute('getSlaveRows');
    }

    public function getMostRecentPlaylists($iLimit)
    {
        return $this->sample($iLimit, 'playlist.time_stamp DESC');
    }

    public function getMostFeaturedPlaylists($iLimit)
    {
        // missing featured
        return $this->sample($iLimit, 'playlist.total_video DESC', []);
    }

    public function getMostRelatedPlaylists($iLimit)
    {
        if (!defined('ULTIMATE_PLAYLIST_CATEGORY_ID')) {
            return $this->getRandomPlaylists($iLimit);
        }

        return $this->getRandomPlaylists($iLimit, ['playlist.category_id=' . ULTIMATE_PLAYLIST_CATEGORY_ID, 'playlist.playlist_id !=' . ULTIMATE_PLAYLIST_ID]);

    }

    public function getMostViewedPlaylists($iLimit)
    {
        return $this->sample($iLimit, 'playlist.total_view DESC', ['playlist.total_view > 0']);
    }

    public function getMostCommentedPlaylists($iLimit)
    {
        return $this->sample($iLimit, 'playlist.total_comment DESC', ['playlist.total_comment > 0']);
    }

    public function getMostLikedPlaylists($iLimit)
    {
        return $this->sample($iLimit, 'playlist.total_like DESC', ['playlist.total_like > 0']);
    }

    public function getMostRecommendedPlaylists($iLimit)
    {
        return $this->sample($iLimit, 'playlist.total_like DESC', ['playlist.total_video > 0']);
    }

    public function getSlideshowPlaylists($iLimit)
    {
        return $this->sample($iLimit, 'playlist.playlist_id DESC', ['playlist.is_featured = 1']);
    }

    public function getUserPostedPlaylists($iLimit, $iUserId = 1)
    {
        return $this->sample($iLimit, 'playlist.time_stamp DESC', ['playlist.user_id=' . intval($iUserId), 'playlist.playlist_id !=' . ULTIMATE_PLAYLIST_ID], true);
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        $view = $this->request()->get('view');
        if (Phpfox::isModule('friend') && $view == 'friendplaylist') {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = playlist.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }
    }

    public function getTagCloud($mMaxDisplay)
    {

        return get_from_cache(['ultimatevideo.playlist', 'tags'], function () use ($mMaxDisplay) {
            return array_map(function ($row) {
                return [
                    'text' => $row['tag'],
                    'link' => \Phpfox_Url::instance()->makeUrl('ultimatevideo.playlist', ['tag' => $row['tag']])
                ];
            }, $this->database()->select('category_id, tag_text AS tag, tag_url, COUNT(item_id) AS total')
                ->from(Phpfox::getT('tag'))
                ->where('category_id=\'ynultimatevideo_playlist\'')
                ->group('tag_text, tag_url')
                ->order('total DESC')
                ->limit($mMaxDisplay)
                ->execute('getSlaveRows'));

        }, 1);

    }

    public function query()
    {
        $view = $this->request()->get('view');

        switch ($this->request()->get('req3')) {
            case 'my':
                $view = 'myplaylist';
                break;
                break;
        }

        switch ($view) {
            case 'myplaylist':
                $this->search()->setCondition('playlist.user_id=' . intval(Phpfox::getUserId()));
                break;
            case 'historyplaylist':
                $mParam = 'history.item_id=playlist.playlist_id AND history.item_type=\'1\' AND history.user_id=' . intval(Phpfox::getUserId());
                $this->database()->join(Phpfox::getT('ynultimatevideo_history'), 'history', $mParam);
                break;
        }

        if (null != ($userId = $this->request()->get('user'))) {
            $this->database()->where('playlist.user_id=' . intval($userId));
        }
    }

    public function getRandomPlaylists($iLimit, $where = [])
    {
        $where [] = 'playlist.is_approved=1';
        $where = implode(' AND ', $where);

        $this->database()
            ->select('playlist.* ,' . Phpfox::getUserField())
            ->from(Phpfox::getT('ynultimatevideo_playlists'), 'playlist')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id=playlist.user_id')
            ->where($where)
            ->order('RAND()')
            ->limit(0, intval($iLimit));

        if (Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = playlist.user_id AND friends.friend_user_id = ' . Phpfox::getUserId())
                ->union();
            $this->database()->select('playlist.* ,' . Phpfox::getUserField())
                ->from(Phpfox::getT('ynultimatevideo_playlists'), 'playlist')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id=playlist.user_id')
                ->where($where . ' AND playlist.user_id =' . Phpfox::getUserId())
                ->order('RAND()')
                ->limit(0, intval($iLimit))
                ->union();
        }

        return $this->database()->execute('getSlaveRows');
    }

    public function getSomeVideoOfPlaylist($iPlaylistId)
    {
        return $this->database()->select('v.title,v.video_id,v.duration')
            ->from(Phpfox::getT('ynultimatevideo_playlist_data'), 'pd')
            ->join(Phpfox::getT('ynultimatevideo_playlists'), 'p', 'p.playlist_id = pd.playlist_id')
            ->join(Phpfox::getT('ynultimatevideo_videos'), 'v', 'v.video_id = pd.video_id')
            ->where('pd.playlist_id = ' . (int)$iPlaylistId)
            ->limit(2)
            ->execute('getSlaveRows');
    }

    public function processRows(&$aRows)
    {
        if (empty($aRows))
            return;
        foreach ($aRows as $index => $aRow) {
            $aRows[$index]['video_list'] = Phpfox::getService('ultimatevideo.playlist.browse')->getSomeVideoOfPlaylist($aRow['playlist_id']);
            if ($aRow['category_id']) {
                $sCategory = Phpfox::getService('ultimatevideo.category')->getCategoriesBreadcrumbByPlaylistId($aRow['playlist_id']);
                $aRows[$index]['sCategory'] = $sCategory;
            }
            if (isset($aRow['user_id']) && !isset($aRow['user_name'])) {
                $sUserCacheId = $this->cache()->set(array('ynultimatevideo_user_profile', 'user_id_' . $aRow['user_id']));
                if (!($aUser = $this->cache()->get($sUserCacheId)) || !is_array($aUser)) {
                    $aUser = Phpfox::getService('user')->getUser($aRow['user_id'], Phpfox::getUserField());
                }
                if (!empty($aUser)) {
                    $aRows[$index] = array_merge($aRows[$index], $aUser);
                } else {
                    unset($aRows[$index]);
                }
            }
        }
    }
}