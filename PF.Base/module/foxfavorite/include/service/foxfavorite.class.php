<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        YouNetCo Company
 * @author        MinhNTK
 * @package        FoxFavorite_Module
 */
class FoxFavorite_Service_FoxFavorite extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('foxfavorite');
    }

    public function getTotalFavoriteBlog($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('blog'), 'b', 'b.blog_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "blog" and b.privacy IN (0,1) and b.post_status = 1 ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteEvent($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('event'), 'b', 'b.event_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "event" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteMarketplace($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('marketplace'), 'b', 'b.listing_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "marketplace" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteMusic($iUserId, $aCond)
    {

        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('music_song'), 'b', 'b.song_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "music" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoritePhoto($iUserId, $aCond)
    {

        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('photo'), 'b', 'b.photo_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "photo" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteVideo($iUserId, $aCond)
    {
        return db()->select('COUNT(v.video_id)')->from(Phpfox::getT('video'), 'v')->join(Phpfox::getT('foxfavorite'), 'f', 'f.item_id = v.video_id AND f.type_id = "video"')->where('f.user_id = ' . (int)$iUserId . ' AND v.privacy IN (0,1) ' . (!empty($aCond) ? $aCond : ''))->execute('getSlaveField');
    }

    public function getTotalFavoritePages($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('pages'), 'b', 'b.page_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "pages" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteQuiz($iUserId, $aCond)
    {

        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('quiz'), 'b', 'b.quiz_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "quiz" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoritePoll($iUserId, $aCond)
    {

        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('poll'), 'b', 'b.poll_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "poll" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteProfile($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('user'), 'b', 'b.user_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "profile" ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteKaraoke($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('karaoke_favorite'), 'kf', 'kf.favorite_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "karaoke" ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteVideochannel($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('channel_video'), 'b', 'b.video_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "videochannel" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteDocument($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('document'), 'b', 'b.document_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "document" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteFEvent($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('fevent'), 'b', 'b.event_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "fevent" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteAdvancedMarketplace($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('advancedmarketplace'), 'b', 'b.listing_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "advancedmarketplace" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteAdvancedPhoto($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('photo'), 'b', 'b.photo_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "advancedphoto" and b.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteDirectory($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('directory_business'), 'c', 'c.business_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "directory" and c.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteAuction($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('ecommerce_product'), 'c', 'c.product_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "auction" and c.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }


    public function getTotalFavoriteCoupon($iUserId, $aCond)
    {
        $iCnt = 0;
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('coupon'), 'c', 'c.coupon_id = f.item_id')->where('f.user_id = ' . $iUserId . ' and f.type_id = "coupon" and c.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteContest($iUserId, $aCond)
    {
        $iCnt = phpfox::getLib('database')->select('count(*)')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('contest'), 'ct', 'ct.contest_id = f.item_id')->where('(ct.contest_status = 4 or ct.contest_status = 5)  and f.user_id = ' . $iUserId . ' and f.type_id = "contest" and ct.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteResume($iUserId, $aCond)
    {
        $iCnt = $this->database()->select('COUNT(*)')->from(Phpfox::getT('foxfavorite'), 'f')->join(Phpfox::getT('resume_basicinfo'), 'rbi', 'rbi.resume_id = f.item_id')->where('f.user_id = ' . $iUserId . ' AND f.type_id = "resume" AND rbi.privacy IN (0,1) AND rbi.is_published = 1 AND rbi.status = "approved" ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteJobposting($iUserId, $aCond)
    {
        $iCnt = $this->database()->select('COUNT(*)')->from(Phpfox::getT('foxfavorite'), 'f')->join(Phpfox::getT('jobposting_job'), 'j', 'j.job_id = f.item_id')->where('f.user_id = ' . $iUserId . ' AND f.type_id = "jobposting" AND j.privacy IN (0,1) AND j.post_status = 1 AND j.is_approved = 1 ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteFoxfeedspro($iUserId, $aCond)
    {
        $iCnt = $this->database()->select('COUNT(*)')->from(Phpfox::getT('foxfavorite'), 'f')->join(Phpfox::getT('ynnews_items'), 'ni', 'ni.item_id = f.item_id')->where('f.user_id = ' . $iUserId . ' AND f.type_id = "foxfeedspro" AND ni.is_active = 1 AND ni.is_approved = 1 ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoritePetition($iUserId, $aCond)
    {
        $iCnt = $this->database()->select('COUNT(*)')->from(Phpfox::getT('foxfavorite'), 'f')->join(Phpfox::getT('petition'), 'p', 'p.petition_id = f.item_id')->where('f.user_id = ' . $iUserId . ' AND f.type_id = "petition" AND p.privacy IN (0,1) AND p.is_approved = 1 ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getTotalFavoriteMusicsharing($iUserId, $aCond)
    {
        $iCnt = $this->database()->select('COUNT(*)')->from(Phpfox::getT('foxfavorite'), 'f')->join(Phpfox::getT('m2bmusic_album_song'), 's', 's.song_id = f.item_id')->join(Phpfox::getT('m2bmusic_album'), 'a', 'a.album_id = s.album_id')->where('f.user_id = ' . $iUserId . ' AND f.type_id = "musicsharing" AND a.privacy IN (0,1) ' . $aCond)->execute('getField');

        return $iCnt;
    }

    public function getFavoriteBlog($aFavorites, $iLimit = 4)
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getfavorite__start')) ? eval($sPlugin) : false);
        $aItems = $this->database()->select('i.blog_id, i.time_stamp, i.title, i.image_path, i.server_id as blog_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('blog'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.blog_id IN(' . implode(',', $aFavorites) . ') AND i.is_approved = 1 AND i.privacy IN(0,1) AND i.post_status = 1')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['blog_server_id'], 'path' => 'core.url_pic', 'file' => 'blog/' . $aItem['image_path'], 'suffix' => '_500', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'path' => 'core.path', 'file' => 'module/foxfavorite/static/image/noimage.png', 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('blog', $aItem['blog_id'], $aItem['title']);
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getfavorite__return')) ? eval($sPlugin) : false);
        return array('title' => _p('blog.blogs'), 'items' => $aItems);
        (($sPlugin = Phpfox_Plugin::get('blog.component_service_callback_getfavorite__end')) ? eval($sPlugin) : false);
    }

    private function getFavoriteEvent($aFavorites, $iLimit = 4)
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_service_callback_getfavorite__start')) ? eval($sPlugin) : false);
        $aItems = $this->database()->select('i.event_id, i.time_stamp, i.image_path, i.title, i.server_id  AS event_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('event'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.event_id IN(' . implode(',', $aFavorites) . ') AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = !empty($aItem['image_path']) ? Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['event_server_id'], 'path' => 'event.url_image', 'file' => $aItem['image_path'], 'suffix' => '_200_square', 'max_width' => 50, 'max_height' => 50)) : Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'path' => 'core.path', 'file' => 'module/foxfavorite/static/image/noimage.png', 'suffix' => '', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('event', $aItem['event_id'], $aItem['title']);
        }

        (($sPlugin = Phpfox_Plugin::get('event.component_service_callback_getfavorite__return')) ? eval($sPlugin) : false);
        return array('title' => _p('event.events'), 'items' => $aItems);
        (($sPlugin = Phpfox_Plugin::get('event.component_service_callback_getfavorite__end')) ? eval($sPlugin) : false);
    }


    public function getFavoritePhoto($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('i.photo_id, i.title, i.album_id, i.time_stamp, i.destination, i.server_id AS photo_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('photo'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.photo_id IN(' . implode(',', $aFavorites) . ') AND i.view_id = 0 AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['photo_server_id'], 'path' => 'photo.url_photo', 'file' => $aItem['destination'], 'suffix' => '_75', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('photo', $aItem['photo_id'], $aItem['title']);
        }

        return array('title' => _p('photo.photos'), 'items' => $aItems);
    }

    private function getFavoriteVideo($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('i.video_id, i.title,  i.time_stamp, i.image_path, i.image_server_id  AS video_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('video'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.video_id IN(' . implode(',', $aFavorites) . ') AND i.view_id = 0 AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['video_server_id'], 'path' => 'core.url_pic', 'file' => $aItem['image_path'], 'suffix' => '_500', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('video.play', $aItem['video_id'], $aItem['title']);
        }

        return ['title' => _p('v.videos'), 'items' => $aItems];
    }

    public function getFavoriteSong($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('i.song_id, i.title, i.album_id, i.time_stamp, i.image_path, i.server_id AS photo_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('music_song'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.song_id IN(' . implode(',', $aFavorites) . ') AND i.view_id = 0 AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['link'] = Phpfox::permalink('music', $aItem['song_id'], $aItem['title']);
            $aItems[$iKey]['image'] = !empty($aItem['image_path']) ? Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['photo_server_id'], 'path' => 'core.url_pic', 'file' => 'music/' . $aItem['image_path'], 'suffix' => '_200_square', 'max_width' => 50, 'max_height' => 50)) : Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'path' => 'core.path', 'file' => 'module/foxfavorite/static/image/noimage.png', 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
        }

        return array('title' => _p('music.songs'), 'items' => $aItems);
    }

    public function getFavoritePoll($aFavorites, $iLimit = 4)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getfoxfavorite__start')) ? eval($sPlugin) : false);
        $aItems = $this->database()->select('i.poll_id, i.time_stamp, i.image_path, i.question as title, i.server_id  AS poll_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('poll'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.poll_id IN(' . implode(',', $aFavorites) . ') AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['poll_server_id'], 'path' => 'core.url_pic', 'file' => 'poll/' . $aItem['image_path'], 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['poll_server_id'], 'path' => 'core.path', 'file' => '/module/foxfavorite/static/image/poll.png', 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('poll', $aItem['poll_id'], $aItem['title']);
        }

        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getfoxfavorite__return')) ? eval($sPlugin) : false);
        return array('title' => _p('poll.polls'), 'items' => $aItems);
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getfoxfavorite__end')) ? eval($sPlugin) : false);
    }

    public function getFavoriteQuiz($aFavorites, $iLimit = 4)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getfoxfavorite__start')) ? eval($sPlugin) : false);
        $aItems = $this->database()->select('i.quiz_id, i.time_stamp, i.image_path, i.title, i.server_id  AS quiz_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('quiz'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.quiz_id IN(' . implode(',', $aFavorites) . ') AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['quiz_server_id'], 'path' => 'core.url_pic', 'title' => $aItems[$iKey]['title'], 'file' => 'quiz/' . $aItem['image_path'], 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['quiz_server_id'], //'path' => 'quiz.url_image',
                    'path' => 'pages.url_image', 'title' => $aItems[$iKey]['title'], 'file' => $aItem['image_path'], 'suffix' => '_50', 'is_page_image' => true, 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('quiz', $aItem['quiz_id'], $aItem['title']);
        }

        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_getquiz__return')) ? eval($sPlugin) : false);
        return array('title' => _p('quiz.quizzes'), 'items' => $aItems);
    }

    public function getFavoriteProfile($aFavorites, $iLimit = 4)
    {
        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_getprofile__start')) ? eval($sPlugin) : false);
        $aItems = $this->database()->select('i.*,i.user_id, i.joined as time_stamp, i.full_name, i.user_name, i.user_image as image_path, i.full_name as title ')->from(phpfox::getT('user'), 'i')->where('i.user_id IN(' . implode(',', $aFavorites) . ')')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            //var_dump($aItems[$iKey]);
            //die();
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'user' => $aItems[$iKey], 'path' => 'core.url_user', 'file' => $aItem['image_path'], 'suffix' => '_50', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('profile', $aItem['user_name']);
        }

        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_getprofile__return')) ? eval($sPlugin) : false);
        return array('title' => _p('profile.profile'), 'items' => $aItems);
    }

    public function getFavoriteMarketplace($aFavorites, $iLimit = 4)
    {
        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_getpoll__start')) ? eval($sPlugin) : false);
        $aItems = $this->database()->select('i.listing_id, i.time_stamp, i.image_path, i.title, i.server_id  AS listing_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('marketplace'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.listing_id IN(' . implode(',', $aFavorites) . ') AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['listing_server_id'], 'path' => 'core.url_pic', 'file' => 'marketplace/' . $aItem['image_path'], 'suffix' => '_120', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['listing_server_id'], 'path' => 'core.path', 'file' => '/module/foxfavorite/static/image/noimage.png', 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('marketplace', $aItem['listing_id'], $aItem['title']);
        }

        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_getpoll__return')) ? eval($sPlugin) : false);
        return array('title' => _p('marketplace.marketplace'), 'items' => $aItems);
    }

    public function getFavoritePages($aFavorites, $iLimit = 4)
    {
        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_getpages__start')) ? eval($sPlugin) : false);
        $aItems = $this->database()->select('p.page_id, p.time_stamp, p.image_path, p.image_server_id as page_server_id, p.title, ' . Phpfox::getUserField() . ', pu.vanity_url')->from(phpfox::getT('pages'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')->where('p.page_id IN(' . implode(',', $aFavorites) . ') AND p.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['page_server_id'], 'path' => 'pages.url_image', 'file' => $aItem['image_path'], 'suffix' => '_50', 'title' => $aItems[$iKey]['title'], 'is_page_image' => true, 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = phpfox::getService('pages')->getUrl($aItem['page_id'], $aItem['title'], $aItem['vanity_url']);
        }

        (($sPlugin = Phpfox_Plugin::get('foxfavorite.component_service_callback_getpages__return')) ? eval($sPlugin) : false);
        return array('title' => _p('pages.pages'), 'items' => $aItems);
    }

    public function getFavoriteKaraoke($aFavorites, $iLimit = 4)
    {
        $aFavors = $this->database()->select('f.*')->from(Phpfox::getT('karaoke_favorite'), 'f')->where('f.favorite_id IN (' . implode(',', $aFavorites) . ')')->limit($iLimit)->execute('getSlaveRows');

        $aItems = array();
        foreach ($aFavors as $key => $aFavor) {
            if ($aFavor['item_type'] == 'song') {
                $aItem = $this->database()->select('i.song_id, i.title, i.time_stamp, i.image_path, ' . Phpfox::getUserField())->from(Phpfox::getT('karaoke_song'), 'i')->join(Phpfox::getT('karaoke_favorite'), 'f', 'f.item_id = i.song_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('f.favorite_id = ' . $aFavor['favorite_id'])->execute('getSlaveRow');
                $aItem['link'] = Phpfox::permalink('karaoke.songdetail', $aItem['song_id'], $aItem['title']);
            } elseif ($aFavor['item_type'] == 'recording') {
                $aItem = $this->database()->select('i.recording_id, i.title, i.time_stamp, i.image_path, ' . Phpfox::getUserField())->from(Phpfox::getT('karaoke_recording'), 'i')->join(Phpfox::getT('karaoke_favorite'), 'f', 'f.item_id = i.recording_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('f.favorite_id = ' . $aFavor['favorite_id'])->execute('getSlaveRow');
                $aItem['link'] = Phpfox::permalink('karaoke.recordingdetail', $aItem['recording_id'], $aItem['title']);
            }

            if (!empty($aItem['image_path'])) {
                $aItem['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'path' => 'core.url_file', 'file' => 'karaoke/image' . $aItem['image_path'], 'suffix' => '_thumb_120x120', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItem['image'] = '<img src="' . Phpfox::getParam('core.path') . 'module/karaoke/static/image/kara-icon.jpg" width="75" />';
            }

            $aItems[] = $aItem;
        }

        if (empty($aItems)) {
            return array();
        }

        return array('title' => _p('foxfavorite.karaoke_songs'), 'items' => $aItems);
    }

    public function getFavoriteVideochannel($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('i.video_id, i.title,  i.time_stamp, i.image_path, i.image_server_id  AS video_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('channel_video'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.video_id IN(' . implode(',', $aFavorites) . ') AND i.view_id = 0 AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['video_server_id'], 'path' => 'core.url_pic', 'file' => $aItem['image_path'], 'suffix' => '_120', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('videochannel', $aItem['video_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.video_channel'), 'items' => $aItems);
    }

    public function getFavoriteDocument($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('d.*, ' . Phpfox::getUserField())->from(phpfox::getT('document'), 'd')->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')->where('d.document_id IN(' . implode(',', $aFavorites) . ') AND d.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_url']) && $aItem['image_url'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['server_id'], 'path' => 'core.url_pic', 'title' => $aItems[$iKey]['title'], 'file' => 'document/' . $aItem['image_url'], 'suffix' => '_400', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'path' => 'core.path', 'file' => '/module/foxfavorite/static/image/document.png', 'title' => $aItems[$iKey]['title'], 'max_width' => 50, 'max_height' => 50));
            }


            $aItems[$iKey]['link'] = Phpfox::permalink('document', $aItem['document_id'], $aItem['title']);
        }

        return array('title' => _p('document.documents'), 'items' => $aItems);
    }

    public function getFavoriteFEvent($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('i.event_id, i.time_stamp, i.image_path, i.title, i.server_id  AS event_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('fevent'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.event_id IN(' . implode(',', $aFavorites) . ') AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['event_server_id'], 'path' => 'event.url_image', 'file' => $aItem['image_path'], 'suffix' => '_120', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('fevent', $aItem['event_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.advanced_events'), 'items' => $aItems,);
    }

    public function getFavoriteAdvancedMarketplace($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('i.listing_id, i.time_stamp, i.image_path, i.title, i.server_id AS listing_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('advancedmarketplace'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.listing_id IN(' . implode(',', $aFavorites) . ') AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }

        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['listing_server_id'], 'path' => 'core.url_pic', 'file' => 'advancedmarketplace/' . $aItem['image_path'], 'suffix' => '_120', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['listing_server_id'], 'path' => 'core.path', 'file' => '/module/foxfavorite/static/image/noimage.png', 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('advancedmarketplace.detail', $aItem['listing_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.advanced_marketplace'), 'items' => $aItems);
    }

    public function getFavoriteAdvancedPhoto($aFavorites, $iLimit = 4)
    {

        $aItems = $this->database()->select('i.photo_id, i.title, i.album_id, i.time_stamp, i.destination, i.server_id AS photo_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('photo'), 'i')->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')->where('i.photo_id IN(' . implode(',', $aFavorites) . ') AND i.view_id = 0 AND i.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['photo_server_id'], 'path' => 'photo.url_photo', 'file' => $aItem['destination'], 'suffix' => '_75', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('advancedphoto', $aItem['photo_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.advanced_photos'), 'items' => $aItems);
    }

    public function getFavoriteDirectory($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('c.business_id, c.time_stamp, c.logo_path as image_path, c.name as title, c.server_id  AS business_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('directory_business'), 'c')->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')->where('c.business_id IN(' . implode(',', $aFavorites) . ') AND c.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['business_server_id'], 'path' => 'core.url_pic', 'file' => $aItem['image_path'], 'suffix' => '_100', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['business_server_id'], 'path' => 'core.path', 'file' => '/module/foxfavorite/static/image/directory.png', 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('directory.detail', $aItem['business_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.businesses'), 'items' => $aItems);
    }

    public function getFavoriteAuction($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('e.product_id, e.product_creation_datetime as time_stamp, e.logo_path as image_path, e.name as title, e.server_id  AS product_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('ecommerce_product'), 'e')->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')->where('e.product_id IN(' . implode(',', $aFavorites) . ') AND e.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['product_server_id'], 'path' => 'core.url_pic', 'file' => $aItem['image_path'], 'suffix' => '_100', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('auction.detail', $aItem['product_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.auctions'), 'items' => $aItems);
    }


    public function getFavoriteCoupon($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('c.coupon_id, c.time_stamp, c.image_path, c.title, c.server_id  AS coupon_server_id, ' . Phpfox::getUserField())->from(phpfox::getT('coupon'), 'c')->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')->where('c.coupon_id IN(' . implode(',', $aFavorites) . ') AND c.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');
        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['coupon_server_id'], 'path' => 'core.url_pic', 'file' => $aItem['image_path'], 'suffix' => '_100', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['coupon_server_id'], 'path' => 'core.path', 'file' => '/module/foxfavorite/static/image/coupon.png', 'suffix' => '_100', 'max_width' => 50, 'max_height' => 50));

            }


            $aItems[$iKey]['link'] = Phpfox::permalink('coupon.detail', $aItem['coupon_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.coupons'), 'items' => $aItems);
    }

    public function getFavoriteContest($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('ct.contest_id, ct.time_stamp, ct.image_path, ct.server_id as item_server_id, ct.contest_name as title, ' . Phpfox::getUserField())->from(phpfox::getT('contest'), 'ct')->join(Phpfox::getT('user'), 'u', 'u.user_id = ct.user_id')->where('(ct.contest_status = 4 or ct.contest_status = 5) and ct.contest_id IN(' . implode(',', $aFavorites) . ') AND ct.privacy IN(0,1)')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }
        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['item_server_id'], 'path' => 'core.url_pic', 'file' => 'contest/' . $aItem['image_path'], 'suffix' => '_100', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('contest', $aItem['contest_id'], $aItem['title']);
        }

        return array('title' => _p('contest.contest'), 'items' => $aItems);
    }

    public function getFavoriteResume($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('rbi.*, rbi.headline as title, rbi.server_id as item_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('resume_basicinfo'), 'rbi')->join(Phpfox::getT('user'), 'u', 'u.user_id = rbi.user_id')->where('rbi.resume_id IN(' . implode(',', $aFavorites) . ') AND rbi.privacy IN(0,1) AND rbi.is_published = 1 AND rbi.status = "approved"')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }

        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['item_server_id'], 'path' => 'core.url_pic', 'file' => 'resume/' . $aItem['image_path'], 'suffix' => '_120', 'max_width' => 50, 'max_height' => 50));

            $aItems[$iKey]['link'] = Phpfox::permalink('resume.view', $aItem['resume_id'], $aItem['headline']);
        }

        return array('title' => _p('foxfavorite.resumes'), 'items' => $aItems);
    }

    public function getFavoriteJobposting($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('j.*, c.image_path, c.server_id as item_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('jobposting_job'), 'j')->join(Phpfox::getT('jobposting_company'), 'c', 'c.company_id = j.company_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = j.user_id')->where('j.job_id IN(' . implode(',', $aFavorites) . ') AND j.privacy IN(0,1) AND j.post_status = 1 AND j.is_approved = 1')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }

        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['item_server_id'], 'path' => 'core.url_pic', 'file' => 'jobposting/' . $aItem['image_path'], 'suffix' => '_120', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'path' => 'core.path', 'file' => '/module/foxfavorite/static/image/noimage.png', 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('jobposting', $aItem['job_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.jobs'), 'items' => $aItems);
    }

    public function getFavoriteFoxfeedspro($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('ni.*, ni.item_title as title, ni.item_pubDate as time_stamp, ni.server_id as item_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('ynnews_items'), 'ni')->join(Phpfox::getT('user'), 'u', 'u.user_id = ni.user_id')->where('ni.item_id IN(' . implode(',', $aFavorites) . ') AND ni.is_active = 1 AND ni.is_approved = 1')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }

        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['item_image']) && $aItem['item_image'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['item_server_id'], 'path' => 'core.url_pic', 'file' => str_replace(Phpfox::getParam('core.url_pic'), '', $aItem['item_image']), 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'path' => 'core.path', 'file' => str_replace(Phpfox::getParam('core.path'), '', '/module/foxfavorite/static/image/news.png'), 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('news.newsdetails', 'item_' . $aItem['item_id'], $aItem['item_title']);
        }

        return array('title' => _p('foxfavorite.news'), 'items' => $aItems);
    }

    public function getFavoritePetition($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('p.petition_id, p.title, p.time_stamp, p.image_path, p.server_id AS item_server_id, ' . Phpfox::getUserField())->from(Phpfox::getT('petition'), 'p')->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')->where('p.petition_id IN(' . implode(',', $aFavorites) . ') AND p.privacy IN (0,1) AND p.is_approved = 1')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }

        foreach ($aItems as $iKey => $aItem) {
            if (isset($aItem['image_path']) && $aItem['image_path'] != '') {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => $aItem['item_server_id'], 'path' => 'core.url_pic', 'file' => $aItem['image_path'], 'suffix' => '_120', 'max_width' => 50, 'max_height' => 50));
            } else {
                $aItems[$iKey]['image'] = Phpfox::getLib('image.helper')->display(array('server_id' => 0, 'path' => 'core.path', 'file' => str_replace(Phpfox::getParam('core.path'), '', '/module/foxfavorite/static/image/noimage.png'), 'suffix' => '', 'max_width' => 50, 'max_height' => 50));
            }

            $aItems[$iKey]['link'] = Phpfox::permalink('petition', $aItem['petition_id'], $aItem['title']);
        }

        return array('title' => _p('foxfavorite.petitions'), 'items' => $aItems);
    }

    public function getFavoriteMusicsharing($aFavorites, $iLimit = 4)
    {
        $aItems = $this->database()->select('s.song_id, s.title, UNIX_TIMESTAMP(a.creation_date) as time_stamp, ' . Phpfox::getUserField())->from(Phpfox::getT('m2bmusic_album_song'), 's')->join(Phpfox::getT('m2bmusic_album'), 'a', 'a.album_id = s.album_id')->join(Phpfox::getT('user'), 'u', 'u.user_id = a.user_id')->where('s.song_id IN(' . implode(',', $aFavorites) . ') AND a.privacy IN (0,1)')->limit($iLimit)->execute('getSlaveRows');

        if (empty($aItems)) {
            return array();
        }

        foreach ($aItems as $iKey => $aItem) {
            $aItems[$iKey]['link'] = Phpfox::permalink('musicsharing.listen', 'music_' . $aItem['song_id']);
        }

        return array('title' => _p('foxfavorite.music_sharing'), 'items' => $aItems);
    }

    public function getUserIdFromUserName($sUserName)
    {
        return phpfox::getLib('database')->select('user_id')->from(phpfox::getT('user'))->where('user_name = "' . $sUserName . '"')->execute('getField');
    }

    public function isAlreadyFavorite($sModule, $iItemId)
    {
        if ($sModule == 'profile') {
            $iItemId = $this->getUserIdFromUserName($iItemId);
            $aFavorite = phpfox::getLib('database')->select('*')->from(phpfox::getT('foxfavorite'))->where('type_id = "' . $sModule . '" and item_id = ' . (int)$iItemId . ' and user_id = ' . phpfox::getUserId())->execute('getRow');

        } else {
            if ($sModule == 'v') {
                $sModule = 'video';
            }
            $aFavorite = phpfox::getLib('database')->select('*')->from(phpfox::getT('foxfavorite'))->where('type_id = "' . $sModule . '" and item_id = ' . (int)$iItemId . ' and user_id = ' . phpfox::getUserId())->execute('getRow');
        }

        if (empty($aFavorite)) {
            return false;
        }
        return $aFavorite;
    }

    public function getUserInfoToSendNotification($iUserId = 0)
    {
        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }

        $aUsers = Phpfox::getLib('database')->select('f.user_id, un.user_notification')->from(Phpfox::getT('foxfavorite'), 'f')->leftjoin(Phpfox::getT('user_notification'), 'un', 'un.user_id = f.user_id and un.user_notification = "foxfavorite.add_new_favorites"')->where('type_id = "profile" and item_id = ' . $iUserId)->execute('getRows');

        return $aUsers;
    }

    public function get($aUser, $aCond, $sSort = 'fs.title ASC, f.time_stamp DESC')
    {
        $aFavorites = $this->database()->select('f.*')->from($this->_sTable, 'f')->join(phpfox::getT('foxfavorite_setting'), 'fs', 'fs.module_id = f.type_id')->where('f.user_id = ' . (int)$aUser['user_id'] . ' AND fs.is_active = 1  ' . $aCond)->order($sSort)->execute('getSlaveRows');

        if (!count($aFavorites)) {
            return array(0, array());
        }

        $aGroups = array();
        $aCache = array();
        $aCacheFavorite = array();
        $iOwnerUserId = 0;
        foreach ($aFavorites as $aFavorite) {
            $aGroups[$aFavorite['type_id']][] = $aFavorite['item_id'];
            $aCacheFavorite[$aFavorite['type_id']][] = $aFavorite['favorite_id'];
            $iOwnerUserId = $aFavorite['user_id'];
        }
        unset($aFavorites, $aFavorite);

        foreach ($aGroups as $sType => $aFavorites) {
            $sModule = $sType == 'video' ? 'v' : $sType;
            if (strpos($sModule, '_')) {
                $aParts = explode('_', $sModule);
                $sModule = $aParts[0];
            }

            if (!Phpfox::isModule($sModule)) {
                continue;
            }
            $aCallback = array();
            //$aCallback = Phpfox::callback($sType . '.getFavorite', $aFavorites, $iUserId);
            switch ($sType) {
                case 'blog':
                    $aCallback = $this->getFavoriteBlog($aFavorites);
                    break;
                case 'event':
                    $aCallback = $this->getFavoriteEvent($aFavorites);
                    break;
                case 'photo':
                    $aCallback = $this->getFavoritePhoto($aFavorites);
                    break;
                case 'video':
                    $aCallback = $this->getFavoriteVideo($aFavorites);
                    break;
                case 'music':
                    $aCallback = $this->getFavoriteSong($aFavorites);
                    break;
                case 'poll':
                    $aCallback = $this->getFavoritePoll($aFavorites);
                    break;
                case 'quiz':
                    $aCallback = $this->getFavoriteQuiz($aFavorites);
                    break;
                case 'profile':
                    $aCallback = $this->getFavoriteProfile($aFavorites);
                    break;
                case 'marketplace':
                    $aCallback = $this->getFavoriteMarketplace($aFavorites);
                    break;
                case 'pages':
                    $aCallback = $this->getFavoritePages($aFavorites);
                    break;
                case 'karaoke':
                    $aCallback = $this->getFavoriteKaraoke($aFavorites);
                    break;
                case 'videochannel':
                    $aCallback = $this->getFavoriteVideochannel($aFavorites);
                    break;
                case 'document':
                    $aCallback = $this->getFavoriteDocument($aFavorites);
                    break;
                case 'fevent':
                    $aCallback = $this->getFavoriteFEvent($aFavorites);
                    break;
                case 'advancedmarketplace':
                    $aCallback = $this->getFavoriteAdvancedMarketplace($aFavorites);
                    break;
                case 'advancedphoto':
                    $aCallback = $this->getFavoriteAdvancedPhoto($aFavorites);
                    break;
                case 'coupon':
                    $aCallback = $this->getFavoriteCoupon($aFavorites);
                    break;
                case 'directory':
                    $aCallback = $this->getFavoriteDirectory($aFavorites);
                    break;
                case 'auction':
                    $aCallback = $this->getFavoriteAuction($aFavorites);
                    break;
                case 'contest':
                    $aCallback = $this->getFavoriteContest($aFavorites);
                    break;
                case 'resume':
                    $aCallback = $this->getFavoriteResume($aFavorites);
                    break;
                case 'jobposting':
                    $aCallback = $this->getFavoriteJobposting($aFavorites);
                    break;
                case 'foxfeedspro':
                    $aCallback = $this->getFavoriteFoxfeedspro($aFavorites);
                    break;
                case 'petition':
                    $aCallback = $this->getFavoritePetition($aFavorites);
                    break;
                case 'musicsharing':
                    $aCallback = $this->getFavoriteMusicsharing($aFavorites);
                    break;
                default:
                    if (phpfox::isModule($sType)) {
                        $aCallback = Phpfox::callback($sType . '.getFoxFavorite', $aFavorites);
                    }
                    break;
            }

            if (empty($aCallback) || count($aCallback['items']) <= 0) {
                continue;
            }

            foreach ($aCacheFavorite[$sType] as $iKey => $iCacheFavId) {
                if (isset($aCallback['items'][$iKey])) {
                    $aCallback['items'][$iKey]['favorite_id'] = $iCacheFavId;
                }
            }

            foreach ($aCallback as $sKey => $aCallbackItem) {
                if ($sKey != 'items') {
                    continue;
                }

                foreach ($aCallbackItem as $iItemKey => $aSub) {
                    $aCallback['items'][$iItemKey]['time_stamp_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aSub['time_stamp']);
                    $aCallback['link'] = phpfox::getLib('url')->makeUrl($aUser['user_name'] . '.foxfavorite.view_' . $sType);
                }
            }

            $aCache[] = $aCallback;
        }

        return array($iOwnerUserId, $aCache);
    }


    public function getSearchFavorite($iUserId, $aCond, $sSort = 'fs.title ASC, f.time_stamp DESC', $iPage = '', $iLimit = '', $iCnt)
    {

        $aFavorites = $this->database()->select('f.*')->from($this->_sTable, 'f')->join(phpfox::getT('foxfavorite_setting'), 'fs', 'fs.module_id = f.type_id')->where('f.user_id = ' . (int)$iUserId . ' AND fs.is_active = 1  ' . $aCond)->order($sSort)->limit($iPage, $iLimit, $iCnt)->execute('getSlaveRows');

        if (!count($aFavorites)) {
            return array(0, array());
        }

        $aGroups = array();
        $aCache = array();
        $aCacheFavorite = array();
        $iOwnerUserId = 0;
        foreach ($aFavorites as $aFavorite) {
            $aGroups[$aFavorite['type_id']][] = $aFavorite['item_id'];
            $aCacheFavorite[$aFavorite['type_id']][] = $aFavorite['favorite_id'];
            $iOwnerUserId = $aFavorite['user_id'];
        }

        unset($aFavorites, $aFavorite);

        foreach ($aGroups as $sType => $aFavorites) {
            $sModule = $sType == 'video' ? 'v' : $sType;

            if (strpos($sModule, '_')) {
                $aParts = explode('_', $sModule);
                $sModule = $aParts[0];
            }

            if (!Phpfox::isModule($sModule)) {
                continue;
            }
            $aCallback = array();
            //$aCallback = Phpfox::callback($sType . '.getFavorite', $aFavorites, $iUserId);
            switch ($sType) {
                case 'blog':
                    $aCallback = $this->getFavoriteBlog($aFavorites, $iLimit);
                    break;
                case 'event':
                    $aCallback = $this->getFavoriteEvent($aFavorites, $iLimit);
                    break;
                case 'photo':
                    $aCallback = $this->getFavoritePhoto($aFavorites, $iLimit);
                    break;
                case 'video':
                    $aCallback = $this->getFavoriteVideo($aFavorites, $iLimit);
                    break;
                case 'music':
                    $aCallback = $this->getFavoriteSong($aFavorites, $iLimit);
                    break;
                case 'poll':
                    $aCallback = $this->getFavoritePoll($aFavorites, $iLimit);
                    break;
                case 'quiz':
                    $aCallback = $this->getFavoriteQuiz($aFavorites, $iLimit);
                    break;
                case 'profile':
                    $aCallback = $this->getFavoriteProfile($aFavorites, $iLimit);
                    break;
                case 'marketplace':
                    $aCallback = $this->getFavoriteMarketplace($aFavorites, $iLimit);
                    break;
                case 'pages':
                    $aCallback = $this->getFavoritePages($aFavorites, $iLimit);
                    break;
                case 'karaoke':
                    $aCallback = $this->getFavoriteKaraoke($aFavorites, $iLimit);
                    break;
                case 'videochannel':
                    $aCallback = $this->getFavoriteVideochannel($aFavorites, $iLimit);
                    break;
                case 'document':
                    $aCallback = $this->getFavoriteDocument($aFavorites, $iLimit);
                    break;
                case 'fevent':
                    $aCallback = $this->getFavoriteFEvent($aFavorites, $iLimit);
                    break;
                case 'advancedmarketplace':
                    $aCallback = $this->getFavoriteAdvancedMarketplace($aFavorites, $iLimit);
                    break;
                case 'advancedphoto':
                    $aCallback = $this->getFavoriteAdvancedPhoto($aFavorites, $iLimit);
                    break;
                case 'coupon':
                    $aCallback = $this->getFavoriteCoupon($aFavorites, $iLimit);
                    break;
                case 'directory':
                    $aCallback = $this->getFavoriteDirectory($aFavorites, $iLimit);
                    break;
                case 'auction':
                    $aCallback = $this->getFavoriteAuction($aFavorites, $iLimit);
                    break;
                case 'contest':
                    $aCallback = $this->getFavoriteContest($aFavorites, $iLimit);
                    break;
                case 'resume':
                    $aCallback = $this->getFavoriteResume($aFavorites, $iLimit);
                    break;
                case 'jobposting':
                    $aCallback = $this->getFavoriteJobposting($aFavorites, $iLimit);
                    break;
                case 'foxfeedspro':
                    $aCallback = $this->getFavoriteFoxfeedspro($aFavorites, $iLimit);
                    break;
                case 'petition':
                    $aCallback = $this->getFavoritePetition($aFavorites, $iLimit);
                    break;
                case 'musicsharing':
                    $aCallback = $this->getFavoriteMusicsharing($aFavorites, $iLimit);
                    break;
                default:
                    if (phpfox::isModule($sType)) {
                        $aCallback = Phpfox::callback($sType . '.getFoxFavorite', $aFavorites, $iLimit);
                    }
                    break;
            }
            if (empty($aCallback)) {
                continue;
            }
            foreach ($aCacheFavorite[$sType] as $iKey => $iCacheFavId) {
                if (isset($aCallback['items'][$iKey])) {
                    $aCallback['items'][$iKey]['favorite_id'] = $iCacheFavId;
                }
            }

            foreach ($aCallback as $sKey => $aCallbackItem) {
                if ($sKey != 'items') {
                    continue;
                }

                foreach ($aCallbackItem as $iItemKey => $aSub) {

                    $aCallback['items'][$iItemKey]['time_stamp_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aSub['time_stamp']);

                }
            }

            $aCache[] = $aCallback;
        }

        return array($iOwnerUserId, $aCache);
    }

    public function getSettings()
    {
        $aReturn = array();
        $aSettings = phpfox::getLib('database')->select('*')->from(phpfox::getT('foxfavorite_setting'))->order('title ASC')->execute('getRows');

        foreach ($aSettings as $iKey => $aSetting) {
            if (Phpfox::isModule($aSetting['module_id']) || ($aSetting['module_id'] == 'video' && Phpfox::isModule('v'))) {
                $aReturn[] = $aSetting;
            }
        }

        return $aReturn;
    }

    public function getFavoriteMembers($sModule, $iItemId)
    {
        if ($sModule == 'profile') {
            $iItemId = $this->getUserIdFromUserName($iItemId);
        }
        $aUsers = phpfox::getLib('database')->select(phpfox::getUserField())->from(phpfox::getT('user'), 'u')->join(phpfox::getT('foxfavorite'), 'f', 'u.user_id = f.user_id')->where('f.type_id ="' . $sModule . '" and item_id = ' . $iItemId)->order('f.favorite_id desc ')->execute('getRows');
        return $aUsers;

    }

    public function getMostFavorites($iOffset, $iTotalFeed)
    {
        $aFavorites = $this->database()->select('f.*, count(f.favorite_id) as total')->from($this->_sTable, 'f')->join(phpfox::getT('foxfavorite_setting'), 'fs', 'fs.module_id = f.type_id')->where('fs.is_active = 1')->order('total DESC, f.time_stamp DESC')->group('item_id, type_id')->limit($iOffset, $iTotalFeed)->execute('getSlaveRows');

        if (!count($aFavorites)) {

            return array(0, array());
        }

        $aCache = array();
        $iOwnerUserId = 0;
        $aFavoriteId = array();
        $i = 0;
        foreach ($aFavorites as $iKey => $aFavorite) {
            if (!phpfox::isModule($aFavorite['type_id'])) {
                continue;
            }

            $aFavoriteId[] = $aFavorite['item_id'];
            switch ($aFavorite['type_id']) {
                case 'blog':
                    $aCallback = $this->getFavoriteBlog($aFavoriteId);
                    break;
                case 'event':
                    $aCallback = $this->getFavoriteEvent($aFavoriteId);
                    break;
                case 'photo':
                    $aCallback = $this->getFavoritePhoto($aFavoriteId);
                    break;
                case 'video':
                    $aCallback = $this->getFavoriteVideo($aFavoriteId);
                    break;
                case 'music':
                    $aCallback = $this->getFavoriteSong($aFavoriteId);
                    break;
                case 'poll':
                    $aCallback = $this->getFavoritePoll($aFavoriteId);
                    break;
                case 'quiz':
                    $aCallback = $this->getFavoriteQuiz($aFavoriteId);
                    break;
                case 'profile':
                    $aCallback = $this->getFavoriteProfile($aFavoriteId);
                    break;
                case 'marketplace':
                    $aCallback = $this->getFavoriteMarketplace($aFavoriteId);
                    break;
                case 'pages':
                    $aCallback = $this->getFavoritePages($aFavoriteId);
                    break;
                case 'karaoke':
                    $aCallback = $this->getFavoriteKaraoke($aFavoriteId);
                    break;
                case 'videochannel':
                    $aCallback = $this->getFavoriteVideochannel($aFavoriteId);
                    break;
                case 'document':
                    $aCallback = $this->getFavoriteDocument($aFavoriteId);
                    break;
                case 'fevent':
                    $aCallback = $this->getFavoriteFEvent($aFavoriteId);
                    break;
                case 'advancedmarketplace':
                    $aCallback = $this->getFavoriteAdvancedMarketplace($aFavoriteId);
                    break;
                case 'advancedphoto':
                    $aCallback = $this->getFavoriteAdvancedPhoto($aFavoriteId);
                    break;
                case 'coupon':
                    $aCallback = $this->getFavoriteCoupon($aFavoriteId);
                    break;
                case 'directory':
                    $aCallback = $this->getFavoriteDirectory($aFavoriteId);
                    break;
                case 'auction':
                    $aCallback = $this->getFavoriteAuction($aFavoriteId);
                    break;
                case 'contest':
                    $aCallback = $this->getFavoriteContest($aFavoriteId);
                    break;
                case 'resume':
                    $aCallback = $this->getFavoriteResume($aFavoriteId);
                    break;
                case 'jobposting':
                    $aCallback = $this->getFavoriteJobposting($aFavoriteId);
                    break;
                case 'foxfeedspro':
                    $aCallback = $this->getFavoriteFoxfeedspro($aFavoriteId);
                    break;
                case 'petition':
                    $aCallback = $this->getFavoritePetition($aFavoriteId);
                    break;
                case 'musicsharing':
                    $aCallback = $this->getFavoriteMusicsharing($aFavoriteId);
                    break;
                default:
                    if (phpfox::isModule($aFavorite['type_id'])) {
                        $aCallback = Phpfox::callback($aFavorite['type_id'] . '.getFoxFavorite', $aFavoriteId);
                    }
                    break;
            }
            unset($aFavoriteId);

            if (!empty($aCallback) && count($aCallback['items'])) {
                $aCallback['items'][0]['time_stamp_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aCallback['items'][0]['time_stamp']);
                $aCallback['items'][0]['total'] = $aFavorite['total'];
                $aCache[] = $aCallback['items'][0];
                $aCache[$i]['type'] = $aCallback['title'];
                $i++;
            }
        }

        return array($iOwnerUserId, $aCache);
    }

    public function getRecentFavorites($iUserId, $aCond, $sSort = 'fs.title ASC, f.time_stamp DESC', $iOffset, $iTotalFeed)
    {
        $aFavorites = $this->database()->select('f.*')->from($this->_sTable, 'f')->join(phpfox::getT('foxfavorite_setting'), 'fs', 'fs.module_id = f.type_id')->where('f.user_id = ' . (int)$iUserId . ' AND fs.is_active = 1  ' . $aCond)->order($sSort)->limit($iOffset, $iTotalFeed)->execute('getSlaveRows');
        if (!count($aFavorites)) {
            return array(0, array());
        }
        $aCache = array();
        $iOwnerUserId = 0;
        $i = 0;
        foreach ($aFavorites as $iKey => $aFavorite) {
            $aFavoriteId = array();
            $aFavoriteId[] = $aFavorite['item_id'];
            if (!phpfox::isModule($aFavorite['type_id'])) {
                unset($aFavoriteId);
                continue;
            }
            switch ($aFavorite['type_id']) {
                case 'blog':
                    $aCallback = $this->getFavoriteBlog($aFavoriteId);
                    break;
                case 'event':
                    $aCallback = $this->getFavoriteEvent($aFavoriteId);
                    break;
                case 'photo':
                    $aCallback = $this->getFavoritePhoto($aFavoriteId);
                    break;
                case 'video':
                    $aCallback = $this->getFavoriteVideo($aFavoriteId);
                    break;
                case 'music':
                    $aCallback = $this->getFavoriteSong($aFavoriteId);
                    break;
                case 'poll':
                    $aCallback = $this->getFavoritePoll($aFavoriteId);
                    break;
                case 'quiz':
                    $aCallback = $this->getFavoriteQuiz($aFavoriteId);
                    break;
                case 'profile':
                    $aCallback = $this->getFavoriteProfile($aFavoriteId);
                    break;
                case 'marketplace':
                    $aCallback = $this->getFavoriteMarketplace($aFavoriteId);
                    break;
                case 'pages':
                    $aCallback = $this->getFavoritePages($aFavoriteId);
                    break;
                case 'karaoke':
                    $aCallback = $this->getFavoriteKaraoke($aFavoriteId);
                    break;
                case 'videochannel':
                    $aCallback = $this->getFavoriteVideochannel($aFavoriteId);
                    break;
                case 'document':
                    $aCallback = $this->getFavoriteDocument($aFavoriteId);
                    break;
                case 'fevent':
                    $aCallback = $this->getFavoriteFEvent($aFavoriteId);
                    break;
                case 'advancedmarketplace':
                    $aCallback = $this->getFavoriteAdvancedMarketplace($aFavoriteId);
                    break;
                case 'advancedphoto':
                    $aCallback = $this->getFavoriteAdvancedPhoto($aFavoriteId);
                    break;
                case 'coupon':
                    $aCallback = $this->getFavoriteCoupon($aFavoriteId);
                    break;
                case 'directory':
                    $aCallback = $this->getFavoriteDirectory($aFavoriteId);
                    break;
                case 'auction':
                    $aCallback = $this->getFavoriteAuction($aFavoriteId);
                    break;
                case 'contest':
                    $aCallback = $this->getFavoriteContest($aFavoriteId);
                    break;
                case 'resume':
                    $aCallback = $this->getFavoriteResume($aFavoriteId);
                    break;
                case 'jobposting':
                    $aCallback = $this->getFavoriteJobposting($aFavoriteId);
                    break;
                case 'foxfeedspro':
                    $aCallback = $this->getFavoriteFoxfeedspro($aFavoriteId);
                    break;
                case 'petition':
                    $aCallback = $this->getFavoritePetition($aFavoriteId);
                    break;
                case 'musicsharing':
                    $aCallback = $this->getFavoriteMusicsharing($aFavoriteId);
                    break;
                default:
                    if (phpfox::isModule($aFavorite['type_id'])) {
                        $aCallback = Phpfox::callback($aFavorite['type_id'] . '.getFoxFavorite', $aFavoriteId);
                    }
                    break;

            }

            unset($aFavoriteId);
            if (!empty($aCallback) && count($aCallback['items'])) {
                $aCallback['items'][0]['time_stamp_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aCallback['items'][0]['time_stamp']);
                $aCache[] = $aCallback['items'][0];
                $aCache[$i]['type'] = $aCallback['title'];
                $i++;
            }
        }
        return array($iOwnerUserId, $aCache);
    }

    public function getCount($sView, $iUserId, $aCond)
    {
        $iCnt = 0;
        $tempModuleId = $sView == 'video' ? 'v' : $sView;

        if (!phpfox::isModule($tempModuleId)) {
            return false;
        }

        switch ($sView) {
            case 'blog':
                $iCnt = $this->getTotalFavoriteBlog($iUserId, $aCond);
                break;
            case 'event':
                $iCnt = $this->getTotalFavoriteEvent($iUserId, $aCond);
                break;
            case 'marketplace':
                $iCnt = $this->getTotalFavoriteMarketplace($iUserId, $aCond);
                break;
            case 'music':
                $iCnt = $this->getTotalFavoriteMusic($iUserId, $aCond);
                break;
            case 'profile':
                $iCnt = $this->getTotalFavoriteProfile($iUserId, $aCond);
                break;
            case 'photo':
                $iCnt = $this->getTotalFavoritePhoto($iUserId, $aCond);
                break;
            case 'video':
                $iCnt = $this->getTotalFavoriteVideo($iUserId, $aCond);
                break;
            case 'pages':
                $iCnt = $this->getTotalFavoritePages($iUserId, $aCond);
                break;
            case 'quiz':
                $iCnt = $this->getTotalFavoriteQuiz($iUserId, $aCond);
                break;
            case 'poll':
                $iCnt = $this->getTotalFavoritePoll($iUserId, $aCond);
                break;
            case 'karaoke':
                $iCnt = $this->getTotalFavoriteKaraoke($iUserId, $aCond);
                break;
            case 'karaoke':
                $iCnt = $this->getTotalFavoriteKaraoke($iUserId, $aCond);
                break;
            case 'videochannel':
                $iCnt = $this->getTotalFavoriteVideochannel($iUserId, $aCond);
                break;
            case 'document':
                $iCnt = $this->getTotalFavoriteDocument($iUserId, $aCond);
                break;
            case 'fevent':
                $iCnt = $this->getTotalFavoriteFEvent($iUserId, $aCond);
                break;
            case 'advancedmarketplace':
                $iCnt = $this->getTotalFavoriteAdvancedMarketplace($iUserId, $aCond);
                break;
            case 'advancedphoto':
                $iCnt = $this->getTotalFavoriteAdvancedPhoto($iUserId, $aCond);
                break;
            case 'coupon':
                $iCnt = $this->getTotalFavoriteCoupon($iUserId, $aCond);
                break;
            case 'directory':
                $iCnt = $this->getTotalFavoriteDirectory($iUserId, $aCond);
                break;
            case 'auction':
                $iCnt = $this->getTotalFavoriteAuction($iUserId, $aCond);
                break;
            case 'contest':
                $iCnt = $this->getTotalFavoriteContest($iUserId, $aCond);
                break;
            case 'resume':
                $iCnt = $this->getTotalFavoriteResume($iUserId, $aCond);
                break;
            case 'jobposting':
                $iCnt = $this->getTotalFavoriteJobposting($iUserId, $aCond);
                break;
            case 'foxfeedspro':
                $iCnt = $this->getTotalFavoriteFoxfeedspro($iUserId, $aCond);
                break;
            case 'petition':
                $iCnt = $this->getTotalFavoritePetition($iUserId, $aCond);
                break;
            case 'musicsharing':
                $iCnt = $this->getTotalFavoriteMusicsharing($iUserId, $aCond);
                break;
            default:
                $iCnt = Phpfox::callback($sView . '.getCountFoxFavorite', $iUserId);
                break;
        }

        return $iCnt;
    }

    public function isAvailModule($sModule)
    {
        if ($sModule == 'v') {
            $sModule = 'video';
        }
        $iActive = phpfox::getLib('database')->select('is_active')->from(phpfox::getT('foxfavorite_setting'))->where('module_id = "' . $sModule . '"')->execute('getField');
        return $iActive == 1;
    }

    public function isFunctionedModule($sModule, $sType = 'favor_button')
    {
        switch ($sType) {
            case 'notify_owner':
                $aModule = array('coupon', // 'directory',
                    'contest', 'jobposting', 'resume', 'videochannel');
                break;
            case 'notify_follower':
                $aModule = array('foxfeedspro' //not need to notify
                );
            default:
                $aModule = array('coupon', // 'directory',
                    'contest', 'foxfeedspro', 'jobposting', 'karaoke', 'resume', 'videochannel');
        }

        if (in_array($sModule, $aModule)) {
            return true;
        }

        return false;
    }

    /**
     * Check is viewing item for modules use theme_template_body__end
     */
    public function isViewItem($sModule)
    {
        $sController = Phpfox::getLib('module')->getControllerName();
        $bIsItem = true;

        switch ($sModule) {
            case 'advancedmarketplace':
                $sView = 'detail';
                break;
            case 'musicsharing':
                $iItemId = $this->getItemId($sModule);
                if (empty($iItemId)) {
                    $bIsItem = false;
                }
                $sView = 'listen';
                break;
            case 'v':
                $sView = 'play';
                break;
            default:
                $sView = 'view';
        }

        return $bIsItem && strpos($sController, $sView) !== false;
    }

    /**
     * Get viewing item id
     */
    public function getItemId($sModule)
    {
        switch ($sModule) {
            case 'advancedmarketplace':
                $iItemId = (defined('PHPFOX_IS_USER_PROFILE')) ? Phpfox::getLib('request')->get('req2') : Phpfox::getLib('request')->getInt('req3');
                break;
            case 'musicsharing':
                $iItemId = Phpfox::getLib('request')->get('music');
                break;
            case 'v':
                $iItemId = Phpfox::getLib('request')->get('req3');
                break;
            default:
                $iItemId = (defined('PHPFOX_IS_USER_PROFILE')) ? Phpfox::getLib('request')->get('req1') : Phpfox::getLib('request')->getInt('req2');
        }

        return $iItemId;
    }

    public function getTopFavoriteMembers()
    {
        $aUsers = phpfox::getLib('database')->select(phpfox::getUserField() . ',count(item_id) as total')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('user'), 'u', 'u.user_id = f.item_id')->group('item_id')->order('total DESC')->where('type_id = "profile"')->limit(10)->execute('getRows');
        return $aUsers;

    }

    public function getTopFavoritePages()
    {
        if (!phpfox::isModule('pages')) {
            return false;
        }
        $aPages = phpfox::getLib('database')->select('p.page_id, p.image_path, p.image_server_id as page_server_id, p.title, count(item_id) as total, pu.vanity_url')->from(phpfox::getT('foxfavorite'), 'f')->join(phpfox::getT('pages'), 'p', 'p.page_id = f.item_id')->join(phpfox::getT('user'), 'us', 'p.user_id = us.user_id')->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')->group('item_id')->order('total DESC')->where('f.type_id = "pages"')->limit(10)->execute('getRows');

        foreach ($aPages as $iKey => $aPage) {
            $aPages[$iKey]['link'] = phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        }
        return $aPages;

    }

    public function isDefaultModules($sModule)
    {
        $sProduct = phpfox::getLib('database')->select('product')->from(phpfox::getT('foxfavorite_setting'))->where('module_id = "' . $sModule . '"')->execute('getSlaveField');
        if ($sProduct == 'phpfox') {
            return true;
        }
        return false;
    }

    public function getItemIdByFavoriteItemId(&$aItem)
    {
        $iFavoriteId = phpfox::getLib('database')->select('item_id')->from(phpfox::getT('foxfavorite'))->where('favorite_id = ' . $aItem['item_id'])->execute('getField');
        $aItem['item_id'] = $iFavoriteId;

    }

    public function getTotalAvailFavoritesOfUser($iUserId)
    {
        $iTotal = 0;
        $aSettings = phpfox::getService('foxfavorite')->getSettings();
        foreach ($aSettings as $iKey => $aSetting) {
            if ($aSetting['is_active'] != 1) {
                continue;
            }

            $iCnt = Phpfox::getService('foxfavorite')->getCount($aSetting['module_id'], $iUserId, '');
            $iTotal += $iCnt;
        }
        return $iTotal;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('favorite.service_favorite__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __class__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

?>