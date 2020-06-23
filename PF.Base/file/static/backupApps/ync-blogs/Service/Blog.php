<?php

namespace Apps\YNC_Blogs\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Service;

class Blog extends Phpfox_Service
{
    /**
     * @return string
     */
    public function getSTable()
    {
        return $this->_sTable;
    }

    private $_iCacheTime = 3;

    private $_aBlockNoCache = ['featured_blog', 'same_author', 'latest_post'];

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynblog_blogs');
    }

    /**
     * Get manage blog in admincp
     * @param array $aConds
     * @param int $iPage
     * @param null $iLimit
     * @param null $iCount
     * @return array|int|string
     */
    public function getManageBlog($aConds = array(), $iPage = 0, $iLimit = NULL, &$iCount = NULL)
    {
        $sWhere = '1=1';
        $aRows = array();

        if (count($aConds) > 0) {
            $sCond = implode(' ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = $this->database()
            ->select("COUNT(blog.blog_id)")
            ->from($this->_sTable, 'blog')
            ->join(Phpfox::getT("user"), 'u', 'blog.user_id =  u.user_id')
            ->leftJoin(Phpfox::getT('ynblog_category_data'), 'acd', 'acd.blog_id = blog.blog_id AND ((acd.category_id <> 0 AND acd.is_main = 1) OR (acd.category_id IS NULL))')
            ->where($sWhere)
            ->execute("getSlaveField");

        if ($iCount) {
            $aRows = $this->database()
                ->select("blog.*, ac.name as category_name," . Phpfox::getUserField())
                ->from($this->_sTable, 'blog')
                ->join(Phpfox::getT("user"), 'u', 'blog.user_id =  u.user_id')
                ->leftJoin(Phpfox::getT('ynblog_category_data'), 'acd', 'acd.blog_id = blog.blog_id AND ((acd.category_id <> 0 AND acd.is_main = 1) OR (acd.category_id IS NULL))')
                ->leftJoin(Phpfox::getT('ynblog_category'), 'ac', 'acd.category_id = ac.category_id')
                ->where($sWhere)
                ->order('blog.blog_id DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');

            foreach ($aRows as &$aRow) {
                $this->retrievePermissionForBlog($aRow);
            }
        }
        return $aRows;
    }

    /**
     * Get all imported blogs from phpFox
     * @return array|int|mixed|string
     */
    public function getImportedCoreBlog()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getimportedcoreblog__start')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_total_imported');
        if (!$sImportedBlog = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $sImportedBlog = $this->database()->select('GROUP_CONCAT(blog_id)')->from(Phpfox::getT('ynblog_imported_blogs'))->execute('getSlaveField');
            $this->cache()->save($sCacheId, $sImportedBlog);
        }

        return $sImportedBlog;
    }

    /**
     * Get all blog from phpFox which all of those are not yet imported
     * @param array $aConds
     * @param int $iPage
     * @param null $iLimit
     * @param null $iCount
     * @return array|int|string
     */
    public function getCoreBlog($aConds = array(), $iPage = 0, $iLimit = NULL, &$iCount = NULL)
    {
        $sWhere = '1=1';
        $aRows = array();

        if (count($aConds) > 0) {
            $sCond = implode(' ', $aConds);
            $sWhere .= ' ' . $sCond;
        }

        $sImportedBlog = $this->getImportedCoreBlog();

        if ($sImportedBlog) {
            $sWhere .= (' AND blog.blog_id NOT IN (' . $sImportedBlog . ')');
        }

        $iCount = $this->database()
            ->select("COUNT(blog.blog_id)")
            ->from(Phpfox::getT('blog'), 'blog')
            ->join(Phpfox::getT("user"), 'u', 'blog.user_id =  u.user_id')
            ->where($sWhere)
            ->execute("getSlaveField");

        if ($iCount) {
            $aRows = $this->database()
                ->select("blog.*, " . Phpfox::getUserField())
                ->from(Phpfox::getT('blog'), 'blog')
                ->join(Phpfox::getT("user"), 'u', 'blog.user_id =  u.user_id')
                ->where($sWhere)
                ->order('blog.blog_id DESC')
                ->limit($iPage, $iLimit, $iCount)
                ->execute('getSlaveRows');
        }

        return $aRows;
    }

    /**
     * @param $iBlogId
     * @return array|int|mixed|string
     */
    public function getBlogForEdit($iBlogId)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getblogsforedit__start')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_detail_edit_' . (int)$iBlogId);
        if (!$aBlog = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $aBlog = $this->database()->select("blog.*, u.user_name")
                ->from($this->_sTable, 'blog')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = blog.user_id')
                ->where('blog.blog_id = ' . (int)$iBlogId)
                ->execute('getSlaveRow');

            if (isset($aBlog['blog_id'])) {
                if (Phpfox::isModule('tag')) {
                    $aTags = Phpfox::getService('tag')->getTagsById('ynblog', $aBlog['blog_id']);
                    if (isset($aTags[$aBlog['blog_id']])) {
                        $aBlog['tag_list'] = $aTags[$aBlog['blog_id']];
                    }
                }

                if (!empty($aBlog['image_path'])) {
                    if(filter_var($aBlog['image_path'], FILTER_VALIDATE_URL)) {
                        $aBlog['current_image'] = $aBlog['image_path'];
                    }
                    else {
                        $aBlog['current_image'] = Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aBlog['server_id'],
                            'path' => 'core.url_pic',
                            'file' => 'ynadvancedblog/' . $aBlog['image_path'],
                            'suffix' => '_grid',
                            'return_url' => true,
                        ));
                    }
                }
            }



            $this->cache()->save($sCacheId, $aBlog);
        }
        return $aBlog;
    }

    /**
     * Get quick blog
     * @param $iBlogId
     * @return array|bool|int|string
     */
    public function getBlog($iBlogId)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.service_blog_getblog')) ? eval($sPlugin) : false);

        if (Phpfox::isModule('track')) {
            $this->database()->select("blog_track.item_id AS is_viewed, ")
                ->leftJoin(Phpfox::getT('track'), 'blog_track', 'blog_track.item_id = advblog.blog_id AND blog_track.user_id = ' . Phpfox::getUserBy('user_id') . ' AND type_id=\'ynblog\'');
        }

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')
                ->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = advblog.user_id AND f.friend_user_id = " . Phpfox::getUserId());
        }

        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ynblog\' AND l.item_id = advblog.blog_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $this->database()->select('af.favorite_id AS is_favorite, ')
            ->leftJoin(Phpfox::getT('ynblog_favorite'), 'af', 'af.blog_id = advblog.blog_id AND af.user_id = ' . Phpfox::getUserId());

        $aRow = $this->database()
            ->select("advblog.*, " . Phpfox::getUserField())
            ->from($this->_sTable, 'advblog')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = advblog.user_id')
            ->where('advblog.blog_id = ' . intval($iBlogId))
            ->execute('getSlaveRow');

        if (empty($aRow))
            return false;

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getblog__end')) ? eval($sPlugin) : false);

        if (!empty($aRow['blog_id']) && !isset($aRow['is_friend'])) {
            $aRow['is_friend'] = 0;
        }

        $aRow['text'] = Phpfox::getLib('parse.output')->replaceHashTags($aRow['text']);
        return $aRow;
    }

    /**
     * @param $iUserId
     * @param $iBlogId
     * @return int
     */
    public function findFavoriteId($iUserId, $iBlogId)
    {
        return (int)$this->database()->select('favorite_id')
            ->from(Phpfox::getT('ynblog_favorite'))
            ->where(strtr('user_id=:user and blog_id=:blog', [
                ':user' => intval($iUserId),
                ':blog' => intval($iBlogId),
            ]))
            ->execute('getSlaveField');
    }

    /**
     * Check whether if we have already favorited this blog or not
     * @param $iUserId
     * @param $iBlogId
     * @return int
     */
    public function isFavorite($iUserId, $iBlogId)
    {
        return $this->findFavoriteId($iUserId, $iBlogId);
    }

    /**
     * @param $iFollowerId
     * @param $iBloggerId
     * @return int
     */
    public function findFollowId($iFollowerId, $iBloggerId)
    {
        return (int)$this->database()->select('follow_id')
            ->from(Phpfox::getT('ynblog_following'))
            ->where(strtr('blogger_id=:blogger and follower_id=:user', [
                ':blogger' => intval($iBloggerId),
                ':user' => intval($iFollowerId),
            ]))
            ->execute('getSlaveField');
    }

    /**
     * Check whether if we have already followed this blogger or not
     * @param $iFollowerId
     * @param $iBloggerId
     * @return int
     */
    public function isFollowing($iFollowerId, $iBloggerId)
    {
        return $this->findFollowId($iFollowerId, $iBloggerId);
    }

    /**
     * @param $iUserId
     * @param $iBlogId
     * @return int
     */
    public function findSavedBlogId($iUserId, $iBlogId)
    {
        return (int)$this->database()->select('saved_blog_id')
            ->from(Phpfox::getT('ynblog_saved'))
            ->where(strtr('blog_id=:blog and user_id=:user', [
                ':blog' => intval($iBlogId),
                ':user' => intval($iUserId),
            ]))
            ->execute('getSlaveField');
    }

    /**
     * Check whether if we have already saved this blog or not
     * @param $iUserId
     * @param $iBlogId
     * @return int
     */
    public function isSaved($iUserId, $iBlogId)
    {
        return $this->findSavedBlogId($iUserId, $iBlogId);
    }

    /**
     * @param int $iUserId
     * @return int|mixed
     */
    public function getTotalDrafts($iUserId = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_gettotaldrafts')) ? eval($sPlugin) : false);

        if (!$iUserId) {
            $iUserId = Phpfox::getUserId();
        }

        $sCacheId = $this->cache()->set('ynblog_draft_total_' . (int)$iUserId);
        if (!$iTotalDrafts = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $iTotalDrafts = (int)$this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('module_id = \'ynblog\' AND user_id = ' . (int)$iUserId . ' AND post_status = \'draft\'')
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalDrafts);
        }
        return $iTotalDrafts;
    }

    /**
     * @return int|mixed
     */
    public function getPendingTotal()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getpendingtotal')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_pending_total');
        if (!$iTotalPending = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $iTotalPending = (int)$this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('post_status = \'public\' AND is_approved = 0')
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalPending);
        }

        return $iTotalPending;
    }

    /**
     * @return int|mixed
     */
    public function getMyTotal()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getmytotal')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_my_total_' . Phpfox::getUserId());
        if (!$iTotalPending = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $iTotalPending = (int)$this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalPending);
        }

        return $iTotalPending;
    }

    /**
     * @return int|mixed
     */
    public function getSavedTotal()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getsavedtotal')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_saved_total_' . Phpfox::getUserId());
        if (!$iTotalSaved = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $iTotalSaved = (int)$this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('ynblog_saved'))
                ->where('user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalSaved);
        }

        return $iTotalSaved;
    }

    /**
     * @return int|mixed
     */
    public function getFollowingTotal()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getfollowingtotal')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_following_total_' . Phpfox::getUserId());
        if (!$iTotalFollowing = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $iTotalFollowing = (int)$this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('ynblog_following'))
                ->where('follower_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalFollowing);
        }

        return $iTotalFollowing;
    }

    /**
     * @return int|mixed
     */
    public function getFavoriteTotal()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getfavoritetotal')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_favorite_total_' . Phpfox::getUserId());
        if (!$iTotalFavorite = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $iTotalFavorite = (int)$this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('ynblog_favorite'))
                ->where('user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');
            $this->cache()->save($sCacheId, $iTotalFavorite);
        }

        return $iTotalFavorite;
    }

    /**
     * @param $iBlogId
     * @param $iLimit
     * @return array|int|null|string
     */
    public function getRelatedBlogs($iBlogId, $iLimit)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getrelatedblogs')) ? eval($sPlugin) : false);

        $sCategory = Phpfox::getService('ynblog.category')->getStringCategoryByBlogId($iBlogId);
        if (empty($sCategory))
            return null;

        $aRelatedBlogs = $this->database()->select('ab.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'ab')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ab.user_id')
            ->join(Phpfox::getT('ynblog_category_data'), 'acd', 'acd.blog_id = ab.blog_id AND acd.is_main = 1 AND acd.blog_id <> ' . $iBlogId)
            ->where('ab.is_approved = 1 AND ab.post_status = \'public\' AND acd.category_id IN (' . $sCategory . ')')
            ->limit($iLimit)
            ->group('ab.blog_id')
            ->execute('getSlaveRows');

        return $aRelatedBlogs;
    }

    /**
     * @param $aItems
     * @return null
     */
    public function getTagListForBlogs(&$aItems)
    {
        if (empty($aItems))
            return null;

        foreach ($aItems as $key => &$aItem) {
            if (Phpfox::isModule('tag')) {
                $aTags = Phpfox::getService('tag')->getTagsById('ynblog', $aItem['blog_id']);
                if (isset($aTags[$aItem['blog_id']])) {
                    $aItem['tag_list'] = $aTags[$aItem['blog_id']];
                }
            }
        }
    }

    /**
     * @param $aBlog
     */
    public function retrieveMoreInfoForBlog(&$aBlog)
    {
        $aBlog['is_saved'] = $this->findSavedBlogId(Phpfox::getUserId(), $aBlog['blog_id']);
    }

    /**
     * @param $aBlog
     */
    public function retrievePermissionForBlog(&$aBlog)
    {
        $aBlog['canPublish'] = Phpfox::getService('ynblog.permission')->canPublishBlog($aBlog['blog_id']);
        $aBlog['canApprove'] = Phpfox::getService('ynblog.permission')->canApproveBlog($aBlog['blog_id']);
        $aBlog['canDeny'] = Phpfox::getService('ynblog.permission')->canDenyBlog($aBlog['blog_id']);
        $aBlog['canFeature'] = Phpfox::getService('ynblog.permission')->canFeatureBlog($aBlog['blog_id']);
        $aBlog['canDelete'] = Phpfox::getService('ynblog.permission')->canDeleteBlog($aBlog['blog_id']);
        $aBlog['canEdit'] = Phpfox::getService('ynblog.permission')->canEditBlog($aBlog['blog_id']);
        $aBlog['permission_enable'] = ($aBlog['canPublish'] || $aBlog['canApprove'] || $aBlog['canDeny'] || $aBlog['canFeature'] || $aBlog['canDelete'] || $aBlog['canEdit']);
    }

    /**
     * @param string $sNameBlock
     * @param $iLimit
     * @param string $sOrder
     * @param string $sCond
     * @return array|int|mixed|string
     */
    public function getRecentPosts($sNameBlock = 'recent_post', $iLimit, $sOrder = 'ab.time_stamp DESC', $sCond = '')
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getrecentposts')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_' . $sNameBlock . '_' . $iLimit);
        if (in_array($sNameBlock, $this->_aBlockNoCache) || (!$aBlogs = $this->cache()->get($sCacheId, $this->_iCacheTime))) {
            $aBlogs = $this->database()->select('ab.*, ac.name, ac.category_id, ac.parent_id, ac.used, u.email as user_email, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'ab')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ab.user_id')
                ->leftJoin(Phpfox::getT('ynblog_category_data'), 'acd', 'acd.blog_id = ab.blog_id AND acd.is_main = 1')
                ->leftJoin(Phpfox::getT('ynblog_category'), 'ac', 'ac.category_id = acd.category_id')
                ->where('ab.module_id = \'ynblog\' AND ab.privacy IN (0) AND ab.item_id = 0 AND ab.is_approved = 1 AND ((acd.category_id <> 0 AND ac.is_active = 1) OR (acd.category_id IS NULL)) AND ab.post_status = \'public\' ' . $sCond)
                ->order($sOrder)
                ->limit($iLimit)
                ->execute('getSlaveRows');

            $this->cache()->save($sCacheId, $aBlogs);
        }
        foreach ($aBlogs as &$aBlog) {
            $this->retrieveMoreInfoForBlog($aBlog);
        }
        $this->getTagListForBlogs($aBlogs);

        return $aBlogs;
    }

    /**
     * @param $iLimit
     * @return array|int|mixed|string
     */
    public function getHotTags($iLimit)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_gethottags')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_hot_tags');
        if (!$aHotTags = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $aHotTags = $this->database()->select('t.tag_text, t.tag_url, COUNT(*) as used')
                ->from(Phpfox::getT('tag'), 't')
                ->where('t.category_id = \'ynblog\'')
                ->group('t.tag_text')
                ->order('used DESC')
                ->limit($iLimit)
                ->execute('getSlaveRows');

            foreach ($aHotTags as &$aHotTag) {
                $aHotTag['tag_url'] = Phpfox::getService('ynblog.callback')->getTagLink() . $aHotTag['tag_url'];
            }

            $this->cache()->save($sCacheId, $aHotTags);
        }

        return $aHotTags;
    }

    /**
     * @param $iUserId
     * @return array|int|mixed|string
     */
    public function getTagBelongToAuthor($iUserId)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_gettagbelongtoauthor')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_tag_author_' . $iUserId);
        if (!$aTagAuthor = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $aTagAuthor = $this->database()->select('t.tag_text, t.tag_url, COUNT(*) as used')
                ->from(Phpfox::getT('tag'), 't')
                ->where('t.category_id = \'ynblog\' AND user_id = ' . $iUserId)
                ->group('t.tag_text')
                ->order('used DESC')
                ->execute('getSlaveRows');

            $this->cache()->save($sCacheId, $aTagAuthor);
        }

        foreach ($aTagAuthor as &$aItem) {
            $aItem['tag_url'] = Phpfox::getService('ynblog.callback')->getTagLink() . $aItem['tag_url'];
        }

        return $aTagAuthor;
    }

    /**
     * @param $iBlogId
     * @param $iTimeStamp
     * @return array
     */
    public function getLastCommentByBlogId($iBlogId, $iTimeStamp)
    {
        list(, $aRows) = Phpfox::getService('comment')->get('cmt.*', ['AND cmt.item_id = ' . $iBlogId . ' AND cmt.time_stamp = ' . $iTimeStamp], 'cmt.time_stamp DESC', 0, 1, 1);

        return (isset($aRows[0]['comment_id']) ? $aRows[0] : []);
    }

    /**
     * @param string $sBlockName
     * @param $iLimit
     * @param array $aConds
     * @param string $sOrder
     * @return array|int|mixed|string
     */
    public function getHotBloggers($sBlockName = 'hot_bloggers', $iLimit, $aConds = [], $sOrder = '')
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_gethotbloggers')) ? eval($sPlugin) : false);


        $sCacheId = $this->cache()->set('ynblog_' . $sBlockName);
        if (!$aHotBloggers = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $sWhere = implode(' ', $aConds);

            $aHotBloggers = $this->database()->select('SUM(ab.total_view) as viewed_total_entries, COUNT(ab.blog_id) as total_entries, uc.cf_about_me, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'ab')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ab.user_id')
                ->leftJoin(Phpfox::getT('user_custom'), 'uc', 'uc.user_id = u.user_id')
                ->where('ab.module_id = \'ynblog\' AND ab.is_approved = 1 AND ab.post_status = \'public\'' . $sWhere)
                ->limit($iLimit)
                ->group('u.user_id')
                ->order($sOrder)
                ->execute('getSlaveRows');

            $this->cache()->save($sCacheId, $aHotBloggers);
        }

        foreach ($aHotBloggers as &$aHotBlogger) {
            $aHotBlogger['total_follower'] = count($this->getAllFollowingByBloggerId($aHotBlogger['user_id']));
            $aHotBlogger['is_followed'] = $this->findFollowingId($aHotBlogger['user_id']);
            $aHotBlogger['canFollow'] = Phpfox::getUserId() && $aHotBlogger['user_id'] != Phpfox::getUserId() && user('yn_advblog_follow');
        }

        return $aHotBloggers;
    }

    /**
     * @param $iBloggerId
     * @return array|int|string
     */
    public function getAllFollowingByBloggerId($iBloggerId)
    {
        $aRows = $this->database()->select('abf.follower_id, u.email, u.full_name, u.user_id')
            ->from(Phpfox::getT('ynblog_following'), 'abf')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = abf.blogger_id')
            ->order('abf.time_stamp DESC')
            ->where('abf.blogger_id = ' . $iBloggerId)
            ->execute('getSlaveRows');

        if (empty($aRows)) {
            return array();
        }

        return $aRows;
    }

    /**
     * @param $iBloggerId
     * @param int $iFollowerId
     * @return int
     */
    public function findFollowingId($iBloggerId, $iFollowerId = 0)
    {
        if (!$iFollowerId) {
            $iFollowerId = Phpfox::getUserId();
        }
        return (int)$this->database()->select('follow_id')
            ->from(Phpfox::getT('ynblog_following'))
            ->where(strtr('follower_id=:follower and blogger_id=:blogger_id', [
                ':follower' => intval($iFollowerId),
                ':blogger_id' => intval($iBloggerId),
            ]))
            ->execute('getSlaveField');
    }

    /**
     * @param $iPage
     * @param $iLimit
     * @param string $sConds
     * @param string $sOrder
     * @return array
     */
    public function getFollowingBlogger($iPage, $iLimit, $sConds = '', $sOrder = 'abf.time_stamp DESC')
    {
        $aFollowIds = $this->database()->select('blogger_id')
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('ynblog_following'), 'abf', 'abf.blogger_id = u.user_id')
            ->where("abf.follower_id = " . Phpfox::getUserId() . ' ' . $sConds)
            ->order($sOrder)
            ->limit($iPage, $iLimit)
            ->execute('getSlaveRows');

        $aRows = [];
        if (!empty($aFollowIds)) {
            foreach ($aFollowIds as $iKey => $aFollowId) {
                $aRow = $this->getCurrentAuthor($aFollowId['blogger_id']);
                $aRow['aLatestPost'] = $this->getRecentPosts('latest_post_' . $aRow['user_id'], 1, null, 'AND u.user_id = ' . $aRow['user_id']);

                $aRows[$iKey] = $aRow;
            }
        }

        return $aRows;
    }

    /**
     * @param $iUserId
     * @return array|int|mixed|string
     */
    public function getCurrentAuthor($iUserId)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_service_blog_getcurrentauthor')) ? eval($sPlugin) : false);

        $sCacheId = $this->cache()->set('ynblog_current_author_' . $iUserId);
        if (!$aCurrentAuthor = $this->cache()->get($sCacheId, $this->_iCacheTime)) {
            $aCurrentAuthor = $this->database()->select('SUM(ab.total_view) as viewed_total_entries, COUNT(DISTINCT ab.blog_id) as total_entries, COUNT(DISTINCT abf.follow_id) as total_follower, uc.cf_about_me, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'ab')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = ab.user_id')
                ->leftJoin(Phpfox::getT('user_custom'), 'uc', 'uc.user_id = u.user_id')
                ->leftJoin(Phpfox::getT('ynblog_following'), 'abf', 'abf.blogger_id = ab.user_id')
                ->where('ab.module_id = \'ynblog\' AND ab.is_approved = 1 AND ab.post_status = \'public\' AND u.user_id = ' . $iUserId)
                ->order('viewed_total_entries DESC')
                ->execute('getSlaveRow');

            $aCurrentAuthor['is_follow'] = $this->isFollowing(Phpfox::getUserId(), $aCurrentAuthor['user_id']);

            $this->cache()->save($sCacheId, $aCurrentAuthor);
        }

        return $aCurrentAuthor;
    }

    /**
     * @param $iBlogId
     * @return array|int|string
     */
    public function getBlogOwnerId($iBlogId)
    {
        return $this->database()->select('user_id')->from($this->_sTable)->where("blog_id = {$iBlogId}")->execute('getSlaveField');
    }

    /**
     * @param int $iLimit
     * @param string $sOrder
     * @param string $sCond
     * @return array|int|string
     */
    public function getRSS($iLimit = 100, $sOrder = 'ab.time_stamp DESC', $sCond = '')
    {
        return $this->database()->select('ab.blog_id, ab.title, ab.text, ab.time_stamp')
            ->from($this->_sTable, 'ab')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ab.user_id')
            ->leftJoin(Phpfox::getT('ynblog_category_data'), 'acd', 'acd.blog_id = ab.blog_id AND acd.is_main = 1')
            ->leftJoin(Phpfox::getT('ynblog_category'), 'ac', 'ac.category_id = acd.category_id')
            ->where('ab.module_id = \'ynblog\' AND ab.privacy IN (0) AND ab.is_approved = 1 AND ((acd.category_id <> 0 AND ac.is_active = 1) OR (acd.category_id IS NULL)) AND ab.post_status = \'public\' ' . $sCond)
            ->order($sOrder)
            ->limit($iLimit)
            ->execute('getSlaveRows');
    }

    /**
     * @param string $sName
     * @param array $aParams
     * @param bool $bIsLoaded
     * @return array|bool
     */
    public function upload($sName = '', $aParams = [], $bIsLoaded = false) {

        $iUserId = isset($aParams['user_id']) ? $aParams['user_id'] : Phpfox::getUserId();
        if (empty($sName) || empty($iUserId) || empty($aParams['type']) || empty($aParams['upload_dir'])) {
            return false;
        }

        if (empty($bIsLoaded)) {
            $aImage = Phpfox::getService('user.file')->load($sName, $aParams);
            if (empty($aImage) || !empty($aImage['error'])) {
                return $aImage;
            }
        }

        $oImage = Phpfox::getLib('image');
        $oFile = Phpfox::getLib('file');
        $bModifyName = isset($aParams['modify_name']) ? $aParams['modify_name'] : true;
        $sFileName = $oFile->upload($sName, $aParams['upload_dir'], !empty($aParams['file_name']) ? $aParams['file_name'] : uniqid(), $bModifyName);
        $sFilePath = $aParams['upload_dir'] . sprintf($sFileName, '');

        // crop max width
        if (Phpfox::isModule('photo')) {
            Phpfox::getService('photo')->cropMaxWidth($sFilePath);
        }
        $iFileSize = filesize($sFilePath);

        $aParams['thumbnail_sizes']  = array(
            ['iSuffix' => '_big', 'iWidth' => 840, 'iHeight' => 472],
            ['iSuffix' => '_list', 'iWidth' => 310, 'iHeight' => 147],
            ['iSuffix' => '_grid', 'iWidth' => 410, 'iHeight' => 230],
        );

        if (!empty($aParams['thumbnail_sizes'])) {
            foreach ($aParams['thumbnail_sizes'] as $iSize) {
                $oImage->createThumbnail($aParams['upload_dir'] . sprintf($sFileName, ''), $aParams['upload_dir'] . sprintf($sFileName, $iSize['iSuffix']), $iSize['iWidth'], $iSize['iHeight'], true);
            }
        }

        return [
            'name' => $sFileName,
            'size' => $iFileSize
        ];

    }
}
