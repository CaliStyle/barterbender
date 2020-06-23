<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/10/16
 * Time: 2:11 PM
 */

namespace Apps\YouNet_UltimateVideos\Service;

use Phpfox;
use Phpfox_Search;

class Browse extends \Phpfox_Service
{

    public function sample($iLimit, $sOrder, $aWhere = [], $bIsUserRelateVideo = false)
    {
        $aWhere [] = 'video.status=1';
        $aWhere [] = 'video.is_approved=1';
        $aWhere [] = 'video.module_id !=\'pages\'';
        $aWhere [] = 'video.module_id !=\'groups\'';
        $aWhere [] = 'video.privacy IN (%PRIVACY%)';
        if (Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !$bIsUserRelateVideo) {
            $aWhere[] = 'video.user_id=' . Phpfox::getUserId();
        }
        $aWhere = implode(' AND ', $aWhere);

        $aBrowseParams = [
            'module_id' => 'ultimatevideo',
            'alias' => 'video',
            'field' => 'video_id',
            'table' => Phpfox::getT('ynultimatevideo_videos'),
            'service' => 'ultimatevideo.browse'
        ];
        Phpfox::getService('privacy')->buildPrivacy($aBrowseParams, $sOrder, 0, $iLimit, ' AND ' . $aWhere, true);
        return $this->database()->execute('getSlaveRows');
    }

    public function getMostCommentedVideos($iLimit)
    {
        return $this->sample($iLimit, 'video.total_comment DESC', ['video.total_comment > 0']);
    }

    public function getMostLikedVideos($iLimit)
    {
        return $this->sample($iLimit, 'video.total_like DESC', ['video.total_like > 0']);
    }

    public function getMostFavouriteVideos($iLimit)
    {
        return $this->sample($iLimit, 'video.total_favorite DESC', ['video.total_favorite > 0']);
    }

    public function getMostViewedVideos($iLimit)
    {
        return $this->sample($iLimit, 'video.total_view DESC', ['video.total_view > 0']);
    }

    public function getMostRecentVideos($iLimit)
    {
        return $this->sample($iLimit, 'video.time_stamp DESC');
    }

    public function getTopRatedVideos($iLimit)
    {
        return $this->sample($iLimit, 'video.rating DESC', ['video.total_rating > 0']);
    }

    public function getMostFeaturedVideos($iLimit)
    {
        return $this->sample($iLimit, 'video.time_stamp DESC', ['video.is_featured = 1']);
    }

    public function getRecommendedCategoryIdList()
    {
        return array_map(function ($tmp) {
            return $tmp['cid'];
        },
            $this->database()
                ->select('distinct(v.category_id) as cid')
                ->from(Phpfox::getT('ynultimatevideo_videos'), 'v')
                ->join(Phpfox::getT('ynultimatevideo_history'), 'h', 'h.item_id=v.video_id AND h.item_type=0 AND h.user_id=' . intval(Phpfox::getUserId()))
                ->where('v.category_id <> 0')
                ->execute('getSlaveRows'));

    }

    public function getRandomVideos($iLimit, $where = [])
    {
        $where [] = 'video.status=1';
        $where [] = 'video.is_approved=1';
        $where [] = 'video.module_id !=\'pages\'';
        $where [] = 'video.module_id !=\'groups\'';
        $where [] = 'video.privacy IN (%PRIVACY%)';
        $where = implode(' AND ', $where);

        $aBrowseParams = [
            'module_id' => 'ultimatevideo',
            'alias' => 'video',
            'field' => 'video_id',
            'table' => Phpfox::getT('ynultimatevideo_videos'),
            'service' => 'ultimatevideo.browse'
        ];
        Phpfox::getService('privacy')->buildPrivacy($aBrowseParams, null, 0, $iLimit, ' AND ' . $where, true);
        $result = $this->database()->execute('getSlaveRows');
        shuffle($result);
        return $result;
    }

    public function getMostRecommendedVideos($iLimit)
    {
        if (!Phpfox::getUserId()) {
            return $this->getRandomVideos($iLimit);
        }

        $aCategoryIds = $this->getRecommendedCategoryIdList();

        if (empty($aCategoryIds)) {
            return $this->getRandomVideos($iLimit);
        }

        $sub_sql = strtr('select item_id from :history as h where h.item_type=0 and h.user_id=:user', [
            ':history' => Phpfox::getT('ynultimatevideo_history'),
            ':user' => intval(Phpfox::getUserId()),
        ]);

        $eachLimit = ceil($iLimit / count($aCategoryIds));
        $aBrowseParams = [
            'module_id' => 'ultimatevideo',
            'alias' => 'video',
            'field' => 'video_id',
            'table' => Phpfox::getT('ynultimatevideo_videos'),
            'service' => 'ultimatevideo.browse'
        ];
        $where [] = 'video.status=1';
        $where [] = 'video.is_approved=1';
        $where [] = 'video.module_id !=\'pages\'';
        $where [] = 'video.module_id !=\'groups\'';
        $where [] = 'video.privacy IN (%PRIVACY%)';
        $where [] = 'video.video_id NOT IN (' . $sub_sql . ')';
        $where = implode(' AND ', $where);
        foreach ($aCategoryIds as $id) {
            Phpfox::getService('privacy')->buildPrivacy($aBrowseParams, null, 0, $eachLimit, ' AND ' . $where . ' AND video.category_id = ' . intval($id), true);
        }
        $result = $this->database()->execute('getSlaveRows');

        $total = count($result);

        if ($total < $iLimit) {
            $ids = array_map(function ($tmp) {
                return $tmp['video_id'];
            }, $result);
            $ids[] = 0;
            $rows = $this->getRandomVideos($iLimit - $total, ['video.video_id NOT IN(' . implode(',', $ids) . ') ']);

            foreach ($rows as $row) {
                $result[] = $row;
            }
        } else if ($total > $iLimit) {
            $result = array_splice($result, 0, $iLimit);

        }
        return $result;
    }

    public function getSlideshowVideos($iLimit)
    {
        return $this->sample($iLimit, 'video.video_id DESC', ['video.is_featured = 1']);
    }

    public function getUserPostedVideos($iLimit, $iUserId)
    {
        return $this->sample($iLimit, 'video.total_view DESC', ['video.user_id=' . intval($iUserId), 'video.video_id !=' . ULTIMATE_VIDEO_ID], true);
    }

    public function getWatchItAgainVideos($iLimit)
    {
        $sub_sql = strtr('select item_id from :history as h where h.item_type=0 and h.user_id=:user', [
            ':history' => Phpfox::getT('ynultimatevideo_history'),
            ':user' => intval(Phpfox::getUserId()),
        ]);
        $where [] = 'video.is_approved=1';
        $where [] = 'video.privacy IN (%PRIVACY%)';
        $where [] = 'video.video_id IN (' . $sub_sql . ')';
        $where = implode(' AND ', $where);

        $aBrowseParams = [
            'module_id' => 'ultimatevideo',
            'alias' => 'video',
            'field' => 'video_id',
            'table' => Phpfox::getT('ynultimatevideo_videos'),
            'service' => 'ultimatevideo.browse'
        ];
        Phpfox::getService('privacy')->buildPrivacy($aBrowseParams, null, 0, $iLimit, ' AND ' . $where, true);
        $result = $this->database()->execute('getSlaveRows');
        shuffle($result);
        return $result;
    }

    public function getMostRelatedVideos($iLimit)
    {
        if (!defined('ULTIMATE_VIDEO_CATEGORY_ID')) {
            return $this->getRandomVideos($iLimit);
        }

        return $this->getRandomVideos($iLimit, ['video.category_id=' . ULTIMATE_VIDEO_CATEGORY_ID, 'video.video_id !=' . ULTIMATE_VIDEO_ID]);

    }

    public function getTagCloud($mMaxDisplay)
    {
        return get_from_cache(['ultimatevideo.video', 'tags'], function () use ($mMaxDisplay) {
            return array_map(function ($row) {
                return [
                    'text' => $row['tag'],
                    'link' => \Phpfox_Url::instance()->makeUrl('ultimatevideo', ['tag' => '']) . $row['tag'],
                    'total' => $row['total']
                ];
            }, $this->database()->select('category_id, tag_text AS tag, tag_url, COUNT(item_id) AS total')
                ->from(Phpfox::getT('tag'))
                ->where('category_id=\'ynultimatevideo\'')
                ->group('tag_text, tag_url')
                ->order('total DESC')
                ->limit($mMaxDisplay)
                ->execute('getSlaveRows'));

        }, 1);

    }

    public function query()
    {
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = video.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        if (null != ($sTag = $this->request()->get('tag'))) {
            $sTag = $this->database()->escape($sTag);
            $this->database()->join(Phpfox::getT('tag'), 'tag', "(tag.category_id='ynultimatevideo' AND tag.item_id=video.video_id AND tag.tag_text='{$sTag}')");
        }

        $view = $this->request()->get('view');
        switch ($view) {
            case 'history':
                $mParam = 'history.item_id=video.video_id AND history.item_type=0 AND history.user_id=' . intval(Phpfox::getUserId());
                $this->database()->join(Phpfox::getT('ynultimatevideo_history'), 'history', $mParam);
                break;
            case 'later':
                $mParam = 'wl.video_id=video.video_id AND wl.user_id=' . intval(Phpfox::getUserId());
                $this->database()->join(Phpfox::getT('ynultimatevideo_watchlaters'), 'wl', $mParam);
                break;
            case 'my':

                break;
            case 'favorite':
                $this->database()->join(Phpfox::getT('ynultimatevideo_favorites'), 'fa', 'fa.video_id=video.video_id AND fa.user_id=' . intval(Phpfox::getUserId()));
                break;
        }
    }

    public function processRows(&$aRows)
    {
        if (empty($aRows))
            return;

        $sVideoIds = implode(',', array_map(function ($video) {
            return $video['video_id'];
        }, $aRows));

        $aWatchlaters = array_map(function ($temp) {
            return $temp['video_id'];
        }, $this->database()->select('video_id')
            ->from(Phpfox::getT('ynultimatevideo_watchlaters'), 'wl')
            ->where('video_id IN (' . $sVideoIds . ') AND user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveRows'));

        foreach ($aRows as $index => $aRow) {
            if ($aRow['type'] == '1' && empty($aRow['image_path'])) {
                $adapter = Phpfox::getService('ultimatevideo')->getClass('Youtube');;
                $adapter->setParams(array(
                    'code' => $aRow['code'],
                    'video_id' => $aRow['video_id']
                ));
                $vImagePath = "";
                if ($adapter->getVideoLargeImage())
                    $vOriginalImagePath = $adapter->getVideoLargeImage();
                $vImagePath = Phpfox::getService('ultimatevideo.process')->downloadImage($vOriginalImagePath);
                $vDuration = 0;
                if ($adapter->getVideoDuration())
                    $vDuration = $adapter->getVideoDuration();
                //update image
                if (!empty($aRow['image_path'])) {
                    $sImagePath = $aRow['image_path'];
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
                $this->database()->update(Phpfox::getT('ynultimatevideo_videos'), ['image_path' => $vImagePath, 'duration' => $vDuration], 'video_id=' . $aRow['video_id']);
                $aRows[$index]['image_path'] = $vImagePath;
            }

            if($aRow['image_server_id'] == -1) {
                $aRows[$index]['image_path'] = Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') . $aRow['image_path'];
            }

            $aRows[$index]['watchlater'] = in_array($aRow['video_id'], $aWatchlaters);
            $aRows[$index]['ranking'] = $index + 1;
            $sCategories = Phpfox::getService('ultimatevideo.category')->getCategoriesBreadcrumbByVideoId($aRow['video_id']);
            $aRows[$index]['sCategory'] = $sCategories;

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