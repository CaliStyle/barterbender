<?php

namespace Apps\YNC_Blogs\Service;

use Language_Service_Language;
use Core;
use Phpfox;
use Phpfox_Service;

class Browse extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynblog_blogs');
    }

    public function query()
    {

    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        $sView = $this->request()->get('view');

        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend)) {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = advblog.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        if (defined('PHPFOX_IS_USER_PROFILE') || $sView != '' || $this->request()->get((defined('PHPFOX_IS_USER_PROFILE') ? 'req3' : 'req2')) == 'category' || $this->request()->get('category', null) || (null !== ($aSearch = $this->request()->getArray('search', null)) && !empty($aSearch['category_id']))) {
            $this->database()->select('ac.category_id, ac.name, ')
                ->leftJoin(Phpfox::getT('ynblog_category_data'), 'acd', 'acd.blog_id = advblog.blog_id')
                ->leftJoin(Phpfox::getT('ynblog_category'), 'ac', 'ac.category_id = acd.category_id  AND ac.is_active = 1')
                ->group('advblog.blog_id');
        } else {
            $this->database()->select('ac.category_id, ac.name, ')
                ->leftJoin(Phpfox::getT('ynblog_category_data'), 'acd', 'acd.blog_id = advblog.blog_id')
                ->join(Phpfox::getT('ynblog_category'), 'ac', 'ac.category_id = acd.category_id  AND ac.is_active = 1 AND is_main = 1');
        }

        if ($this->request()->get((defined('PHPFOX_IS_PAGES_VIEW') ? 'req4' : 'req2')) == 'tag' || $this->request()->get('tag', null)) {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = advblog.blog_id AND tag.category_id = \'ynblog\'');
        }

        if ($sView != '') {
            switch ($sView) {
                case 'favorite':
                    $this->database()
                        ->join(Phpfox::getT('ynblog_favorite'), 'abf', 'advblog.blog_id = abf.blog_id AND abf.user_id = ' . Phpfox::getUserId());
                    break;
                case 'saved':
                    $this->database()
                        ->join(Phpfox::getT('ynblog_saved'), 'abs', 'abs.blog_id = advblog.blog_id AND abs.user_id = ' . Phpfox::getUserId());
                    break;
            }
        }

        if (!in_array($sView, ['my', 'pending'])) {
            $this->database()->join(Phpfox::getT('user'), 'u', 'u.user_id = advblog.user_id');
        }
    }

    public function processRows(&$aRows)
    {
        foreach ($aRows as $key => &$aItem) {
            if (Phpfox::isModule('tag')) {
                $aTags = Phpfox::getService('tag')->getTagsById('ynblog', $aItem['blog_id']);
                if (isset($aTags[$aItem['blog_id']])) {
                    $aItem['tag_list'] = $aTags[$aItem['blog_id']];
                }
            }

            Phpfox::getService('ynblog.blog')->retrievePermissionForBlog($aItem);
            Phpfox::getService('ynblog.blog')->retrieveMoreInfoForBlog($aItem);
        }
    }
}
