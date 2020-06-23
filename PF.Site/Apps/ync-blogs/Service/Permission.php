<?php

namespace Apps\YNC_Blogs\Service;

use Phpfox;
use Phpfox_Service;

class Permission extends Phpfox_Service
{
    /**
     * @param $iBlogId
     * @param bool $bRedirect
     * @return bool
     */
    public function canPublishBlog($iBlogId, $bRedirect = false)
    {
        $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);

        return ($this->canEditBlog($iBlogId) && $aBlog['post_status'] == 'draft');
    }

    /**
     * @param $iBlogId
     * @param bool $bRedirect
     * @return bool
     */
    public function canApproveBlog($iBlogId, $bRedirect = false)
    {
        $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);

        if ($aBlog['blog_id'] && user('yn_advblog_approve', null, null, $bRedirect) && $aBlog['post_status'] == 'public' && $aBlog['is_approved'] == 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $iBlogId
     * @param bool $bRedirect
     * @return bool
     */
    public function canDenyBlog($iBlogId, $bRedirect = false)
    {
        $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);

        if ($aBlog['blog_id'] && user('yn_advblog_approve', null, null, $bRedirect) && $aBlog['post_status'] == 'public' && $aBlog['is_approved'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canCreateBlog()
    {
        $iTotalBlog = Phpfox::getService('ynblog.blog')->getMyTotal();
        $iLimit = user('yn_advblog_max_blogs');

        if (!$iLimit || $iLimit > $iTotalBlog) {
            return true;
        }

        return false;
    }

    /**
     * @param $iBlogId
     * @param bool $bRedirect
     * @return bool
     */
    public function canEditBlog($iBlogId, $bRedirect = false)
    {
        $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);

        if (($aBlog['user_id'] == Phpfox::getUserId() && user('yn_advblog_edit', null, null, $bRedirect)) || ($aBlog['user_id'] != Phpfox::getUserId() && user('yn_advblog_edit_other', null, null, $bRedirect))) {
            return true;
        }

        return false;
    }

    /**
     * @param $iBlogId
     * @param bool $bRedirect
     * @return bool
     */
    public function canDeleteBlog($iBlogId, $bRedirect = false)
    {
        $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);

        if (($aBlog['user_id'] == Phpfox::getUserId() && user('yn_advblog_delete', null, null, $bRedirect)) || ($aBlog['user_id'] != Phpfox::getUserId() && user('yn_advblog_delete_other', null, null, $bRedirect))) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canFeatureBlog($iBlogId)
    {
        $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);

        if (user('yn_advblog_feature') && $aBlog['post_status'] == 'public' && $aBlog['is_approved'] == 1 && $aBlog['module_id'] == 'ynblog') {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canCommentOnBlog()
    {
        if (user('yn_advblog_comment')) {
            return true;
        }

        return false;
    }

}
