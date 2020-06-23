<?php

namespace Apps\YNC_Blogs\Service;

use Core\Lib as Lib;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;


class Process extends Phpfox_Service
{
    /**
     * Add a category
     * @param $aVals
     * @return bool|int
     */
    public function addCategory($aVals)
    {
        //Add phrase for category
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_' . $aLanguages[0]['language_id']];
        $phrase_var_name = 'ynblog_category_' . md5('Advanced Blogs Category' . $name . PHPFOX_TIME);
        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage) {
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = strip_tags($aVals['name_' . $aLanguage['language_id']]);
            } else {
                return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
            }
        }
        $aValsPhrase = [
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];
        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        $iId = $this->database()->insert(Phpfox::getT('ynblog_category'), [
            'is_active' => '1',
            'name' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME,
            'ordering' => '0',
            'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
        ]);

        Lib::phrase()->clearCache();
        $this->cache()->remove('ynblog', 'substr');
        return $iId;
    }

    /**
     * Update a category
     * @param $iId
     * @param $aVals
     * @return bool
     */
    public function updateCategory($iId, $aVals)
    {
        if ($iId == $aVals['parent_id']) {
            return Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
        }
        $aOldCategory = Phpfox::getService('ynblog.category')->getCategory($iId);

        //Update phrase
        $aLanguages = Phpfox::getService('language')->getAll();
        if (Lib::phrase()->isPhrase($aVals['name'])) {
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);

                    $name = strip_tags($aVals['name_' . $aLanguage['language_id']]);
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
                } else {
                    return Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }
            }
        } else {
            //Add new phrase if before is not phrase
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'ynblog_category_' . md5('Advanced Blogs Category' . $name . PHPFOX_TIME);

            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);

                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    Phpfox_Error::set((_p('provide_a_language_name_name', ['language_name' => $aLanguage['title']])));
                }
            }
            $aValsPhrase = [
                'product_id' => 'ynblog',
                'module' => 'ynblog',
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];
            $aVals['name'] = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        }

        $this->database()->update(Phpfox::getT('ynblog_category'), array(
            'name' => $aVals['name'],
            'parent_id' => $aVals['parent_id'],
        ), 'category_id = ' . (int)$iId
        );

        /**
         * We have many blog belong to category A > B > C
         * But admin change B to D or None.
         *                  C to D or None.
         * **** Case 1: Change to D: We change all blogs belong to A > B > C into D > B > C OR D > C (if only belong to A we will not update)
         * **** Case 2: Change to None: We change all blogs belong to A > B > C into B > C (Another way is remove all A)
         */
        $sBlogIds = Phpfox::getService('ynblog.category')->getAllItemBelongToCategory($iId, true);
        if (!empty($sBlogIds)) {
            // Check whether if old parent category have parent category. We'll delete category data and update count
            $sCategories = Phpfox::getService('ynblog.category')->getBreadcrumCategory($aOldCategory['parent_id']);
            if ($sCategories) {
                $aParentCategories = explode(',', $sCategories);
                foreach ($aParentCategories as $aParentCategory) {
                    $this->database()->delete(Phpfox::getT('ynblog_category_data'), "blog_id IN ($sBlogIds) AND category_id = " . $aParentCategory);
                    $this->database()->updateCount('ynblog_category_data', 'category_id = ' . (int)$aParentCategory, 'used', 'ynblog_category', 'category_id = ' . (int)$aParentCategory);
                }
            }

            // Case 1: Change to D.
            if ($aVals['parent_id']) {
                // Check whether if old parent category have parent category. We'll insert category data and update count
                $sCategories = Phpfox::getService('ynblog.category')->getBreadcrumCategory($aVals['parent_id']);
                if ($sCategories) {
                    $aParentCategories = explode(',', $sCategories);
                    foreach ($aParentCategories as $aParentCategory) {
                        foreach (explode(',', $sBlogIds) as $sBlogId) {
                            $this->database()->insert(Phpfox::getT('ynblog_category_data'), array('blog_id' => $sBlogId, 'category_id' => $aParentCategory, 'is_main' => 0));
                        }
                        $this->database()->updateCount('ynblog_category_data', 'category_id = ' . (int)$aParentCategory, 'used', 'ynblog_category', 'category_id = ' . (int)$aParentCategory);
                    }
                }
            } // Case 2: Change to none
            else {
                $this->database()->delete(Phpfox::getT('ynblog_category_data'), "blog_id IN ($sBlogIds) AND category_id = " . $aOldCategory['parent_id']);
            }
        }

        Lib::phrase()->clearCache();
        //remove category cache
        $this->cache()->remove('ynblog', 'substr');

        return true;
    }

    /**
     * Delete a category
     * @param $iCategoryId
     * @return bool
     */
    public function deleteCategory($iCategoryId)
    {
        $aCategory = $this->database()->select('*')
            ->from(Phpfox::getT('ynblog_category'))
            ->where('category_id=' . intval($iCategoryId))
            ->execute('getSlaveRow');

        //Delete phrase of category
        if (isset($aCategory['name']) && Lib::phrase()->isPhrase($aCategory['name'])) {
            Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
        }

        $this->database()->update(Phpfox::getT('ynblog_category'), ['parent_id' => $aCategory['parent_id']], "parent_id = $iCategoryId");
        $this->database()->delete(Phpfox::getT('ynblog_category'), 'category_id = ' . intval($iCategoryId));
        $this->database()->delete(Phpfox::getT('ynblog_category_data'), 'category_id = ' . intval($iCategoryId));
        $this->cache()->remove('ynblog', 'substr');
        return true;
    }

    /**
     * Set active or inactive for category
     * @param $iId
     * @param $iType
     */
    public function updateActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);

        $this->database()->update((Phpfox::getT('ynblog_category')), array('is_active' => (int)($iType == '1' ? 1 : 0)), 'category_id = ' . (int)$iId);

        $this->cache()->remove('ynblog', 'substr');
    }

    /**
     * Add new blog entry
     * @param $aVals
     * @return int
     */
    public function add($aVals)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process__start')) ? eval($sPlugin) : false);
        $oFilter = Phpfox::getLib('parse.input');

        if (!empty($aVals['module_id']) && !empty($aVals['item_id'])) {
            if (Phpfox::isModule($aVals['module_id'])) {
                $aVals['privacy'] = 0;
                $aVals['privacy_comment'] = 0;
            } else {
                Phpfox_Error::set(_p('cannot_find_the_parent_item'));
                return false;
            }

        } else {
            $aVals['module_id'] = 'ynblog';
            $aVals['item_id'] = 0;
        }

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['text'] . ' ' . $aVals['title']);

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $sTitle = $oFilter->clean($aVals['title'], 255);
        $bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('attachment.can_attach_on_blog'));

        if (!isset($aVals['post_status'])) {
            $aVals['post_status'] = 'public';
        }

        $sPostStatus = $aVals['post_status'];
        $aInsert = [
            'user_id' => Phpfox::getUserId(),
            'title' => $sTitle,
            'time_stamp' => PHPFOX_TIME,
            'time_update' => PHPFOX_TIME,
            'latest_comment' => 0,
            'is_approved' => (user('yn_advblog_automatically_approve') ? 0 : 1),
            'text' => (user('yn_advblog_embed_to_blog') ? $aVals['text'] : preg_replace("/<iframe[^>]+\>|<img[^>]+\>/i", "(media) ", $aVals['text'])),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'post_status' => $sPostStatus,
            'is_hidden' => (isset($aVals['is_hidden']) ? $aVals['is_hidden'] : '0'),
        ];

        if (isset($aVals['item_id']) && isset($aVals['module_id'])) {
            $aInsert['item_id'] = (int)$aVals['item_id'];
            $aInsert['module_id'] = $oFilter->clean($aVals['module_id']);
        }

        $bIsSpam = false;

        if (user('yn_advblog_automatically_approve') && $sPostStatus != 'draft') {
            $aInsert['is_approved'] = '0';
            $bIsSpam = true;
            //Remove total pending blog
            $this->cache()->remove('ynblog_pending_total', 'substr');
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_add_start')) ? eval($sPlugin) : false);

        // Add main photo for blog
        $this->_processUploadForm($aVals, $aInsert);

        if (!$this->_processUploadForm($aVals, $aInsert)) {
            Phpfox_Error::set(_p('size_image_exceed_max_size'));
            return false;
        }

        $iId = $this->database()->insert(Phpfox::getT('ynblog_blogs'), $aInsert);

        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_add_end')) ? eval($sPlugin) : false);

        // Add category data for blog
        if (!empty($aVals['category'])) {
            $this->addCategoryForBlog($iId, array_filter($aVals['category']), ($aVals['post_status'] == 'public' ? true : false));
        }

        if (Phpfox::isModule('tag')) {
            Phpfox::getService('tag.process')->add('ynblog', $iId, Phpfox::getUserId(), $aVals['text'], true);

            if (isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
                Phpfox::getService('tag.process')->add('ynblog', $iId, Phpfox::getUserId(), $aVals['tag_list']);
            }
            $this->cache()->remove('ynblog_hot_tags');
        }

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        if ($bIsSpam === true) {
            $this->cache()->remove('ynblog_my_total_' . Phpfox::getUserId());
            return $iId;
        }

        if ($aVals['post_status'] == 'public') {
            if (isset($aVals['module_id']) && ($aVals['module_id'] != 'ynblog') && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'], 'getFeedDetails')) {
                (Phpfox::isModule('feed') && Phpfox::getParam('ynblog.allow_create_feed_adv_when_add_new_item', 1) ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aVals['module_id'] . '.getFeedDetails', $aVals['item_id']))->add('ynblog', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), $aVals['item_id']) : null);
            } else {
                (Phpfox::isModule('feed') && Phpfox::getParam('ynblog.allow_create_feed_adv_when_add_new_item', 1) ? Phpfox::getService('feed.process')->add('ynblog', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0)) : null);
            }

            //support add notification for parent module
            if (Phpfox::isModule('notification') && isset($aVals['module_id']) && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'], 'addItemNotification')) {
                Phpfox::callback($aVals['module_id'] . '.addItemNotification', ['page_id' => $aVals['item_id'], 'item_perm' => 'yn_advblog_view', 'item_type' => 'ynblog', 'item_id' => $iId, 'onwer_id' => Phpfox::getUserId()]);
            }

            //support send notification and mail to follower
            $this->sendMailandNotification($iId);

            //TODO Update user activity
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'ynblog', '+');
        }

        if ($aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('ynblog', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }


        //clear cache
        $this->cache()->remove('ynblog', 'substr');
        $this->cache()->remove('ynblog_my_total_' . Phpfox::getUserId());

        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process__end')) ? eval($sPlugin) : false);
        return $iId;
    }

    /**
     * Update a blog
     * @param $iId
     * @param $iUserId
     * @param $aVals - new params
     * @param null $aRow - old params
     * @return mixed
     */
    public function update($iId, $iUserId, $aVals, &$aRow = null)
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_update__start')) ? eval($sPlugin) : false);

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $oFilter = Phpfox::getLib('parse.input');

        Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['text']);

        $aOldBlogData = Phpfox::getService('ynblog.blog')->getBlogForEdit($iId);

        $sPostStatus = (isset($aVals['post_status']) ? $aVals['post_status'] : '');
        //Publish a draft blog, but this user group's blog have to approve first.
        if ($aOldBlogData['post_status'] == 'draft' && $sPostStatus == 'public' && user('yn_advblog_automatically_approve')) {
            $this->cache()->remove('ynblog_pending_total', 'substr');
            $aOldBlogData['is_approved'] = 0;
        }

        $sTitle = $oFilter->clean($aVals['title'], 255);
        $bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('attachment.can_attach_on_blog'));

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        $aUpdate = array(
            'title' => $sTitle,
            'time_update' => PHPFOX_TIME,
            'text' => (user('yn_advblog_embed_to_blog') ? $aVals['text'] : preg_replace("/<iframe[^>]+\>|<img[^>]+\>/i", "(media) ", $aVals['text'])),
            'is_approved' => $aOldBlogData['is_approved'],
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'is_hidden' => (isset($aVals['is_hidden']) ? $aVals['is_hidden'] : '0'),
        );

        if ($sPostStatus) {
            $aUpdate['post_status'] = $sPostStatus;
        }

        // Add main photo for blog
        $this->_processUploadForm($aVals, $aUpdate);

        if ($aRow !== null && isset($aVals['post_status']) && $aRow['post_status'] == 'draft' && $aVals['post_status'] == 'public') {
            $aUpdate['time_stamp'] = PHPFOX_TIME;
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_update')) ? eval($sPlugin) : false);

        $this->database()->update(Phpfox::getT('ynblog_blogs'), $aUpdate, 'blog_id = ' . (int)$iId);

        $this->updateCategoryForBlog($iId, array_filter($aVals['category']));

        if (Phpfox::isModule('tag')) {
            Phpfox::getService('tag.process')->update('ynblog', $iId, Phpfox::getUserId(), $aVals['text'], true);
            Phpfox::getService('tag.process')->update('ynblog', $iId, $iUserId, (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
            $this->cache()->remove('ynblog_hot_tags');
        }

        if ($aRow !== null && $aRow['is_approved'] == 1 && $aRow['post_status'] == 'draft' && $aVals['post_status'] == 'public') {
            if (isset($aRow['module_id']) && ($aRow['module_id'] != 'ynblog') && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'], 'getFeedDetails')) {
                (Phpfox::isModule('feed') && Phpfox::getParam('ynblog.allow_create_feed_adv_when_add_new_item', 1) ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aRow['module_id'] . '.getFeedDetails', $aRow['item_id']))->add('ynblog', $iId, $aVals['privacy'], $aVals['privacy_comment'], $aRow['item_id'], $iUserId) : null);
            } else {
                (Phpfox::isModule('feed') && Phpfox::getParam('ynblog.allow_create_feed_adv_when_add_new_item', 1) ? Phpfox::getService('feed.process')->add('ynblog', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $iUserId) : null);
            }

            //support add notification for parent module
            if (Phpfox::isModule('notification') && isset($aRow['module_id']) && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'], 'addItemNotification')) {
                Phpfox::callback($aRow['module_id'] . '.addItemNotification', ['page_id' => $aRow['item_id'], 'item_perm' => 'yn_advblog_view', 'item_type' => 'ynblog', 'item_id' => $iId, 'owner_id' => $iUserId]);
            }

            //support send notification and mail to follower
            $this->sendMailandNotification($iId);

            //TODO Update user activity
            Phpfox::getService('user.activity')->update($iUserId, 'ynblog');
        } else {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('ynblog', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $iUserId) : null);
        }

        if (Phpfox::isModule('privacy')) {
            if ($aVals['privacy'] == '4') {
                Phpfox::getService('privacy.process')->update('ynblog', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : []));
            } else {
                Phpfox::getService('privacy.process')->delete('ynblog', $iId);
            }
        }

        //clear cache
        $this->cache()->remove('ynblog', 'substr');

        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_update__end')) ? eval($sPlugin) : false);
        return $iId;
    }

    /**
     * Add category data to a blog
     * @param $iBlogId
     * @param array $aCategories
     * @param bool $bUpdateUsageCount
     */
    public function addCategoryForBlog($iBlogId, $aCategories = array())
    {
        $iTotalCat = count($aCategories);
        if ($iTotalCat) {
            $iKey = 0;
            foreach ($aCategories as $iId) {
                if (!is_numeric($iId))
                    continue;
                $iKey++;
                $this->database()->insert(Phpfox::getT('ynblog_category_data'), array('blog_id' => $iBlogId, 'category_id' => $iId, 'is_main' => ($iKey == $iTotalCat ? 1 : 0)));

                $this->database()->updateCount('ynblog_category_data', 'category_id = ' . (int)$iId, 'used', 'ynblog_category', 'category_id = ' . (int)$iId);
            }
        }
    }

    /**
     * Update category data for a blog
     * @param $iBlogId
     * @param $aCategories
     * @param $bUpdateUsageCount
     * @param bool $bDecreaseUsageCount
     */
    public function updateCategoryForBlog($iBlogId, $aCategories)
    {
        $aRows = $this->database()->select('category_id')
            ->from(Phpfox::getT('ynblog_category_data'))
            ->where('blog_id = ' . (int)$iBlogId)
            ->execute('getSlaveRows');

        if (count($aRows)) {
            foreach ($aRows as $aRow) {
                $this->database()->delete(Phpfox::getT('ynblog_category_data'), "blog_id = " . (int)$iBlogId . " AND category_id = " . (int)$aRow["category_id"]);
                $this->database()->updateCount('ynblog_category_data', 'category_id = ' . (int)$aRow['category_id'], 'used', 'ynblog_category', 'category_id = ' . (int)$aRow['category_id']);
            }
        }

        $this->addCategoryForBlog($iBlogId, $aCategories);
    }

    /**
     * Update view when a user click to blog detail. If the user read this blog twice, we just consider only one time.
     * @param $iBlogId
     * @return bool
     */
    public function updateView($iBlogId)
    {
        $this->database()->updateCounter('ynblog_blogs', 'total_view', 'blog_id', $iBlogId);

        $this->cache()->remove('ynblog', 'substr');
        return true;
    }

    /**
     * Add a blog to favorite list
     * @param $iUserId
     * @param $iBlogId
     * @return bool
     */
    public function addFavorite($iUserId, $iBlogId)
    {
        $id = Phpfox::getService('ynblog.blog')->findFavoriteId($iUserId, $iBlogId);

        if (!$id) {
            $this->database()->insert(Phpfox::getT('ynblog_favorite'), [
                'user_id' => intval($iUserId),
                'blog_id' => intval($iBlogId),
                'time_stamp' => time(),
            ]);

            $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit(intval($iBlogId));

            Phpfox::getService("notification.process")->add("ynblog_favoriteblog", $iBlogId, $aBlog['user_id'], Phpfox::getUserId());
            $this->database()->updateCounter('ynblog_blogs', 'total_favorite', 'blog_id', $iBlogId);

            $this->cache()->remove('ynblog', 'substr');
            $this->cache()->remove('ynblog_favorite_total_' . Phpfox::getUserId());
        }

        return true;
    }

    /**
     * Remove a blog from favorite list
     * @param $iUserId
     * @param $iBlogId
     * @return bool
     */
    public function deleteFavorite($iUserId, $iBlogId)
    {
        $id = Phpfox::getService('ynblog.blog')->findFavoriteId($iUserId, $iBlogId);

        if ($id) {
            $this->database()->delete(Phpfox::getT('ynblog_favorite'), 'favorite_id=' . intval($id));
            $this->database()->updateCounter('ynblog_blogs', 'total_favorite', 'blog_id', $iBlogId, true);
            $this->cache()->remove('ynblog', 'substr');
            $this->cache()->remove('ynblog_favorite_total_' . Phpfox::getUserId());
        }

        return true;
    }

    /**
     * Add follow a blogger. All bloggers will be displayed in My Following Bloggers
     * @param $iFollowerId
     * @param $iBloggerId
     * @return bool
     */
    public function addFollow($iFollowerId, $iBloggerId)
    {
        $id = Phpfox::getService('ynblog.blog')->findFavoriteId($iFollowerId, $iBloggerId);

        if (!$id) {
            $this->database()->insert(Phpfox::getT('ynblog_following'), [
                'blogger_id' => intval($iBloggerId),
                'follower_id' => intval($iFollowerId),
                'time_stamp' => time(),
            ]);

            $this->cache()->remove('ynblog', 'substr');
            $this->cache()->remove('ynblog_following_total_' . Phpfox::getUserId());
        }

        return true;
    }

    /**
     * Un-follow a blogger
     * @param $iFollowerId
     * @param $iBloggerId
     * @return bool
     */
    public function deleteFollow($iFollowerId, $iBloggerId)
    {
        $id = Phpfox::getService('ynblog.blog')->findFollowId($iFollowerId, $iBloggerId);

        if ($id) {
            $this->database()->delete(Phpfox::getT('ynblog_following'), 'follow_id=' . intval($id));
            $this->cache()->remove('ynblog', 'substr');
            $this->cache()->remove('ynblog_following_total_' . Phpfox::getUserId());
        }

        return true;
    }

    /**
     * Add a blog to saved for reading later
     * @param $iUserId
     * @param $iBlogId
     * @return bool
     */
    public function addSavedBlog($iUserId, $iBlogId, $callFromSavedApp = false)
    {
        $id = Phpfox::getService('ynblog.blog')->findSavedBlogId($iUserId, $iBlogId);

        if (!$id) {
            $this->database()->insert(Phpfox::getT('ynblog_saved'), [
                'user_id' => intval($iUserId),
                'blog_id' => intval($iBlogId),
                'time_stamp' => PHPFOX_TIME,
            ]);

            if(!$callFromSavedApp && Phpfox::isAppActive('P_SavedItems') && Phpfox::hasCallback('saveditems', 'saveItem')) {
                $title = db()->select('title')
                            ->from(Phpfox::getT('ynblog_blogs'))
                            ->where('blog_id = ' . (int)$iBlogId)
                            ->execute('getSlaveField');
                Phpfox::callback('saveditems.saveItem', [
                    'type_id' => 'ynblog',
                    'item_id' => $iBlogId,
                    'link' => !empty($title) ? Phpfox::permalink('ynblog', $iBlogId, $title) : '',
                    'is_save' => 1,
                    'user_id' => (int)$iUserId
                ]);
            }

            $this->cache()->remove('ynblog_saved_total', 'substr');
            $this->cache()->remove('ynblog_saved_total_' . Phpfox::getUserId());
        }

        return true;
    }

    /**
     * Remove saved blog from My Saved Blogs
     * @param $iUserId
     * @param $iBlogId
     * @return bool
     */
    public function deleteSavedBlog($iUserId, $iBlogId, $callFromSavedApp = false)
    {
        $id = Phpfox::getService('ynblog.blog')->findSavedBlogId($iUserId, $iBlogId);

        if ($id) {
            $this->database()->delete(Phpfox::getT('ynblog_saved'), 'saved_blog_id=' . intval($id));

            if(!$callFromSavedApp && Phpfox::isAppActive('P_SavedItems') && Phpfox::hasCallback('saveditems', 'saveItem')) {
                Phpfox::callback('saveditems.saveItem', [
                    'type_id' => 'ynblog',
                    'item_id' => $iBlogId,
                    'is_save' => 0,
                    'user_id' => (int)$iUserId
                ]);
            }

            $this->cache()->remove('ynblog_saved_total', 'substr');
            $this->cache()->remove('ynblog_saved_total_' . Phpfox::getUserId());
        }

        return true;
    }

    /**
     * Feature or Un-feature a blog
     * @param $iBlogId
     * @param $iIsFeatured 1 is set feature and 0 is unset feature to this blog
     * @return bool
     */
    public function featureBlog($iBlogId, $iIsFeatured)
    {
        if (!user('yn_advblog_feature') && !Phpfox::isAdmin()) {
            return false;
        }

        $oBlog = Phpfox::getService('ynblog.blog');
        $bUpdate = $this->database()->update(Phpfox::getT('ynblog_blogs'), array('is_featured' => $iIsFeatured), "blog_id = {$iBlogId} AND is_approved = 1 AND post_status = 'public'");

        // Cannot feature
        if (!$bUpdate) {
            return false;
        }
        if ($iIsFeatured) {
            $iOwnerId = $oBlog->getBlogOwnerId($iBlogId);
            if ($iOwnerId) {
                // Add notification
                $iSenderUserId = $iOwnerId;
                if ((int)Phpfox::getUserId() > 0) {
                    $iSenderUserId = Phpfox::getUserId();
                }
                Phpfox::getService("notification.process")->add("ynblog_blogfeature", $iBlogId, $iOwnerId, $iSenderUserId);
            }
        }

        $this->cache()->remove('ynblog', 'substr');
        return true;
    }

    /**
     * Delete Blog process
     * @param $iBlogId
     * @return bool
     */
    public function deleteBlog($iBlogId)
    {
        $aBlog = $this->database()->select('*')
            ->from(Phpfox::getT('ynblog_blogs'))
            ->where("blog_id =" . (int)$iBlogId)
            ->execute('getRow');

        if (!isset($aBlog['blog_id']) || !Phpfox::getService('ynblog.permission')->canDeleteBlog($aBlog['blog_id'])) {
            return false;
        }

        if (!empty($aBlog['image_path'])) {
            $sImagePath = $aBlog['image_path'];
            $aImages = array(
                Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_240'),
                Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_500'),
                Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_1024'),
            );
            foreach ($aImages as $sImage) {
                if (file_exists($sImage)) {
                    @unlink($sImage);
                }
            }
        }
        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_delete__start')) ? eval($sPlugin) : false);

        $this->database()->delete(Phpfox::getT('ynblog_blogs'), "blog_id = " . (int)$iBlogId);
        $this->database()->delete(Phpfox::getT('track'), 'item_id = ' . (int)$iBlogId . ' AND type_id="ynblog"');

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('ynblog', (int)$iBlogId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_ynblog', $iBlogId) : null);

        (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete('ynblog', (int)$iBlogId, 0, true) : null);
        (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem(['ynblog_like', 'comment_ynblog', 'ynblog_blogfeature', 'ynblog_approved'], (int)$iBlogId) : null);

        //TODO Update user activity
        Phpfox::getService('user.activity')->update($aBlog['user_id'], 'ynblog', '-');

        $aRows = $this->database()->select('blog_id, category_id')
            ->from(Phpfox::getT('ynblog_category_data'))
            ->where('blog_id = ' . (int)$iBlogId)
            ->execute('getSlaveRows');

        if (count($aRows)) {
            foreach ($aRows as $aRow) {
                $this->database()->delete(Phpfox::getT('ynblog_category_data'), "blog_id = " . (int)$aRow['blog_id'] . " AND category_id = " . (int)$aRow['category_id']);
                if ($aBlog['post_status'] == 'public') {
                    $this->database()->updateCount('ynblog_category_data', 'category_id = ' . (int)$aRow['category_id'], 'used', 'ynblog_category', 'category_id = ' . (int)$aRow['category_id']);
                }
            }
        }


        $this->database()->delete(Phpfox::getT('ynblog_saved'), "blog_id = {$aBlog['blog_id']}");
        $this->database()->delete(Phpfox::getT('ynblog_favorite'), "blog_id = {$aBlog['blog_id']}");
        $this->database()->delete(Phpfox::getT('ynblog_imported_blogs'), "advblog_id = {$aBlog['blog_id']}");


        if (Phpfox::isModule('tag')) {
            $this->database()->delete(Phpfox::getT('tag'), 'item_id = ' . $aBlog['blog_id'] . ' AND category_id = "ynblog"');
            $this->cache()->remove('tag', 'substr');
            $this->cache()->remove('ynblog_hot_tags');
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_delete')) ? eval($sPlugin) : false);

        if ($aBlog['module_id'] == 'pages') {
            $sType = 'ynblog_blog';

            $aFeeds = $this->database()->select('feed_id, user_id')
                ->from(Phpfox::getT($aBlog['module_id'] . '_feed'))
                ->where('type_id = \'' . $sType . '\' AND item_id = ' . (int)$iBlogId)
                ->execute('getRows');
            if (count($aFeeds)) {
                foreach ($aFeeds as $aFeed) {
                    $this->database()->delete(Phpfox::getT($aBlog['module_id'] . '_feed'), 'feed_id = ' . $aFeed['feed_id']);
                }
            }
        }

        $this->cache()->remove('ynblog', 'substr');

        if ((int)$aBlog['user_id'] == Phpfox::getUserId()) {
            $this->cache()->remove('ynblog_my_total_' . Phpfox::getUserId());
        } else {
            $this->cache()->remove('ynblog_favorite_total_' . Phpfox::getUserId());
            $this->cache()->remove('ynblog_following_total_' . Phpfox::getUserId());
            $this->cache()->remove('ynblog_saved_total_' . Phpfox::getUserId());
        }


        if ((int)$aBlog['is_approved'] == 0 && $aBlog['post_status'] == 'public') {
            $this->cache()->remove('ynblog_pending_total');
        }

        return true;
    }

    /**
     * Approve Blog process
     * @param $iBlogId
     * @param $bIsApprove
     * @return bool
     */
    public function approveBlog($iBlogId, $bIsApprove)
    {
        Phpfox::getUserParam('yn_advblog_approve', true);

        $aBlog = $this->database()->select('b.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('ynblog_blogs'), 'b')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->where('b.blog_id = ' . (int)$iBlogId)
            ->execute('getSlaveRow');

        if (!isset($aBlog['blog_id'])) {
            return Phpfox_Error::set(_p('the_blog_you_are_trying_to_approve_is_not_valid'));
        }

        if ($aBlog['is_approved'] == '1') {
            return false;
        }

        if ($bIsApprove) {
            $this->database()->update(Phpfox::getT('ynblog_blogs'), array('is_approved' => '1', 'post_status' => 'public', 'time_stamp' => PHPFOX_TIME), 'blog_id = ' . $aBlog['blog_id']);

            if (isset($aBlog['module_id']) && ($aBlog['module_id'] != 'ynblog') && Phpfox::isModule($aBlog['module_id']) && Phpfox::hasCallback($aBlog['module_id'], 'getFeedDetails')) {
                (Phpfox::isModule('feed') && Phpfox::getParam('ynblog.allow_create_feed_adv_when_add_new_item', 1) ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aBlog['module_id'] . '.getFeedDetails', $aBlog['item_id']))->add('ynblog', $iBlogId, $aBlog['privacy'], $aBlog['privacy_comment'], $aBlog['item_id'], $aBlog['user_id']) : null);
            } else {
                (Phpfox::isModule('feed') && Phpfox::getParam('ynblog.allow_create_feed_adv_when_add_new_item', 1) ? Phpfox::getService('feed.process')->add('ynblog', $iBlogId, $aBlog['privacy'], $aBlog['privacy_comment'], 0, $aBlog['user_id']) : null);
            }

            //support add notification for parent module
            if (Phpfox::isModule('notification') && isset($aBlog['module_id']) && Phpfox::isModule($aBlog['module_id']) && Phpfox::hasCallback($aBlog['module_id'], 'addItemNotification')) {
                Phpfox::callback($aBlog['module_id'] . '.addItemNotification', ['page_id' => $aBlog['item_id'], 'item_perm' => 'yn_advblog_view', 'item_type' => 'ynblog', 'item_id' => $iBlogId, 'owner_id' => $aBlog['user_id']]);
            }

            //support send notification and mail to follower
            $this->sendMailandNotification($iBlogId);

            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add('ynblog_approved', $aBlog['blog_id'], $aBlog['user_id']);
            }

            if ($aBlog['is_approved'] == '9') {
                $this->database()->updateCounter('user', 'total_spam', 'user_id', $aBlog['user_id'], true);
            }

            Phpfox::getService('user.activity')->update($aBlog['user_id'], 'ynblog', '+');

            (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_approve__1')) ? eval($sPlugin) : false);

            // Send the user an email
            $sLink = Phpfox::getLib('url')->permalink('ynblog', $aBlog['blog_id'], $aBlog['title']);
            Phpfox::getLib('mail')->to($aBlog['user_id'])
                ->subject(array('blog.your_blog_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
                ->message(array('blog.your_blog_has_been_approved_on_site_title_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
                ->notification('blog.blog_is_approved')
                ->send();
        } else {
            $this->database()->update(Phpfox::getT('ynblog_blogs'), array('post_status' => 'denied', 'time_stamp' => PHPFOX_TIME), 'blog_id = ' . $aBlog['blog_id']);
            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add('ynblog_denied', $aBlog['blog_id'], $aBlog['user_id']);
            }
        }

        //clear cache
        $this->cache()->remove('ynblog');
        $this->cache()->remove('ynblog_pending_total');

        return true;
    }

    /**
     * Migrate comments of blog item from phpFox Blog to YouNet Advanced Blogs
     * We will also migrate comments of comments
     * @param $aCoreBlog
     * @param $iNewBlogId
     */
    private function _migrateComments($aCoreBlog, $iNewBlogId)
    {
        $aComments = [];
        $sTable = 'comment';
        if ($aCoreBlog['module_id'] != 'blog' && !empty($aCoreBlog['item_id'])) {
            $aCallBack = Phpfox::callback($aCoreBlog['module_id'] . '.getFeedDetails', array('item_id' => $iNewBlogId));

            if (!empty($aCallBack['table_prefix'])
                && ($sTable = $aCallBack['table_prefix'] . 'feed_comment')
                && $this->database()->tableExists($sTable)
            ) {
                $aComments = $this->database()->select('*')->from(Phpfox::getT($sTable))->where('parent_user_id = ' . $aCoreBlog['blog_id'])->execute('getSlaveRows');
            }
        } else {
            $aComments = Phpfox::getService('comment')->get('cmt.*, cmt.update_time as cmt_update_time, cmt.time_stamp as cmt_time_stamp , comment_text.text as cmt_text, comment_text.text_parsed as cmt_text_parsed', 'cmt.item_id = ' . $aCoreBlog['blog_id'] . ' AND cmt.type_id = \'blog\'');
            $aComments = (!empty($aComments[0]) ? $aComments[1] : []);
        }

        if (count($aComments)) {
            foreach ($aComments as &$aComment) {
                $iLastInsert = $this->_insertComment($aComment, $sTable, $iNewBlogId);

                $aFeedMini = $this->database()
                    ->select('*')
                    ->from(Phpfox::getT('like'))
                    ->where('type_id = \'feed_mini\' AND item_id = ' . $aComment['comment_id'])
                    ->execute('getSlaveRows');

                if (count($aFeedMini)) {
                    foreach ($aFeedMini as $aMini) {
                        $this->_insertLike($aMini, $iLastInsert);
                    }
                }
            }
        }
    }

    /**
     * Migrate likes of blog item from phpFox Blog to YouNet Advanced Blogs.
     * @param $aCoreBlog
     * @param $iNewBlogId
     */
    private function _migrateLikes($aCoreBlog, $iNewBlogId)
    {
        $aLikes = $this->database()
            ->select('*')
            ->from(Phpfox::getT('like'))
            ->where('type_id = \'blog\' AND item_id = ' . $aCoreBlog['blog_id'])
            ->execute('getSlaveRows');

        if (count($aLikes)) {
            foreach ($aLikes as $aLike) {
                $aLike['type_id'] = 'ynblog';
                $this->_insertLike($aLike, $iNewBlogId);
            }
        }
    }

    /**
     * Insert a record comment of blog item from phpFox Blog to YouNet Advanced Blogs
     * @param $aComment
     * @param $sTable
     * @param $iNewBlogId
     * @return int
     */
    private function _insertComment($aComment, $sTable, $iNewBlogId)
    {
        $aInsert = array();
        $aInsert['parent_id'] = $aComment['parent_id'];
        $aInsert['type_id'] = 'ynblog';
        $aInsert['item_id'] = $iNewBlogId;
        $aInsert['user_id'] = $aComment['user_id'];
        $aInsert['owner_user_id'] = $aComment['owner_user_id'];
        $aInsert['time_stamp'] = $aComment['cmt_time_stamp'];
        $aInsert['update_time'] = $aComment['cmt_update_time'];
        $aInsert['update_user'] = $aComment['update_user'];
        $aInsert['rating'] = $aComment['rating'];
        $aInsert['ip_address'] = $aComment['ip_address'];
        $aInsert['author'] = $aComment['author'];
        $aInsert['author_email'] = $aComment['author_email'];
        $aInsert['author_url'] = $aComment['author_url'];
        $aInsert['view_id'] = $aComment['view_id'];
        $aInsert['child_total'] = $aComment['child_total'];
        $aInsert['total_like'] = $aComment['total_like'];
        $aInsert['total_dislike'] = $aComment['total_dislike'];
        $aInsert['feed_table'] = $aComment['feed_table'];
        $iLastInsert = $this->database()->insert(Phpfox::getT($sTable), $aInsert);

        //inset text and text parse.
        $aInsertText['text'] = $aComment['cmt_text'];
        $aInsertText['comment_id'] = $iLastInsert;
        $aInsertText['text_parsed'] = $aComment['cmt_text_parsed'];
        $this->database()->insert(phpFox::getT('comment_text'), $aInsertText);

        return $iLastInsert;
    }

    /**
     * Insert a record like of blog item from phpFox Blog to YouNet Advanced Blogs
     * @param $aLike
     * @param $iNewBlogId
     * @return bool
     */
    private function _insertLike($aLike, $iNewBlogId)
    {
        if (!empty($aLike)) {
            unset($aLike['like_id']);
            $aLike['item_id'] = $iNewBlogId;
            $this->database()->insert(Phpfox::getT('like'), $aLike);

            //inset like cache.
            $insert_item_like_cache['type_id'] = $aLike['type_id'];
            $insert_item_like_cache['item_id'] = $iNewBlogId;
            $insert_item_like_cache['user_id'] = $aLike['user_id'];
            $this->database()->insert(phpFox::getT('like_cache'), $insert_item_like_cache);
        } else {
            return false;
        }
    }

    /**
     * Import Blog from Blog of phpFox. Support choose category
     * @param $iBlogId - maybe a list of blog id
     * @param $iCategory - chosen category all imported blogs will be belong to
     * @return int
     */
    public function importBlog($iBlogId, $iCategory)
    {
        Phpfox::isAdmin(true);
        $aCoreBlog = db()->select("blog.*, blog_text.text AS text, u.user_name")
            ->from(Phpfox::getT('blog'), 'blog')
            ->join(Phpfox::getT('blog_text'), 'blog_text', 'blog_text.blog_id = blog.blog_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = blog.user_id')
            ->where('blog.blog_id = ' . (int)$iBlogId)
            ->execute('getSlaveRow');

        if (!isset($aCoreBlog['blog_id'])) {
            return Phpfox_Error::set(_p('the_blog_you_are_trying_to_approve_is_not_valid'));
        }

        $aInsert = $aCoreBlog;
        unset($aInsert['total_dislike']);
        unset($aInsert['blog_id']);
        unset($aInsert['user_name']);
        unset($aInsert['total_attachment']);
        if (isset($aInsert['is_sponsor'])) unset($aInsert['is_sponsor']);

        $aInsert['total_favorite'] = 0;
        $aInsert['module_id'] = ($aCoreBlog['module_id'] != 'blog') ? $aCoreBlog['module_id'] : 'ynblog';
        $aInsert['total_share'] = 0;
        $aInsert['latest_comment'] = 0;
        $aInsert['is_featured'] = 0;
        $aInsert['is_old_suffix'] = 0;
        $aInsert['post_status'] = ($aCoreBlog['post_status'] == 1 ? 'public' : 'draft');
        $aInsert['text'] = html_entity_decode($aCoreBlog['text']);

        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_importblog_start')) ? eval($sPlugin) : false);

        $iId = $this->database()->insert(Phpfox::getT('ynblog_blogs'), $aInsert);
        (($sPlugin = Phpfox_Plugin::get('ynblog.service_process_importblog_end')) ? eval($sPlugin) : false);

        $this->addCategoryForBlog($iId, array($iCategory));

        //Clone comment and like
        $this->_migrateLikes($aCoreBlog, $iId);
        $this->_migrateComments($aCoreBlog, $iId);
        $this->processImagesImportBlog($iId, $aCoreBlog['image_path'], $aCoreBlog['server_id']);

        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('blog', $aCoreBlog['blog_id']);
            if (isset($aTags[$aCoreBlog['blog_id']])) {
                $aTagLists = $aTags[$aCoreBlog['blog_id']];
            } else {
                $aTagLists = array();
            }

            $aInsert['tag_list'] = '';

            foreach ($aTagLists as $aTagList) {
                $aInsert['tag_list'] .= ($aTagList['tag_text'] . ',');
            }

            if (Phpfox::isModule('tag')) {
                Phpfox::getService('tag.process')->add('ynblog', $iId, Phpfox::getUserId(), $aInsert['text'], true);

                if (isset($aInsert['tag_list']) && ((is_array($aInsert['tag_list']) && count($aInsert['tag_list'])) || (!empty($aInsert['tag_list'])))) {
                    Phpfox::getService('tag.process')->add('ynblog', $iId, Phpfox::getUserId(), $aInsert['tag_list']);
                }
            }
        }

        $this->database()->insert(Phpfox::getT('ynblog_imported_blogs'), array('advblog_id' => $iId, 'blog_id' => $aCoreBlog['blog_id']));
        if ($aCoreBlog['is_approved'] == 1 && $aCoreBlog['post_status'] == 1) {
            Phpfox::getService('user.activity')->update($aCoreBlog['user_id'], 'ynblog', '+');
        }

        $this->cache()->remove('ynblog_total_imported', 'substr');
        Phpfox::getService('ynblog.cache_remove')->my();
        return $iId;
    }

    /**
     * Update total share of blog. Hook share will call this function
     * @param $iBlogId
     * @return bool
     */
    public function updateShare($iBlogId)
    {
        $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);

        if (empty($aBlog['blog_id'])) {
            return false;
        }
        $this->database()->updateCounter('ynblog_blogs', 'total_share', 'blog_id', $iBlogId);
    }

    /**
     * Import blogs from Wordpress / Bloggers / Tumblr
     * @param $aPosts
     * @param $aCategories
     *
     * @return int
     */
    public function importBlogs($aPosts, $aCategories)
    {
        $bSuccess = 0;
        $aDefaultFields = [
            'server_id' => 0,
            'user_id' => Phpfox::getUserId(),
            'time_update' => PHPFOX_TIME,
            'latest_comment' => 0,
            'is_approved' => 0,
            'is_featured' => 0,
            'privacy' => 0,
            'privacy_comment' => 0,
            'post_status' => 'public',
            'total_comment' => 0,
            'total_view' => 0,
            'total_like' => 0,
            'total_share' => 0,
            'total_favorite' => 0,
            'module_id' => 'ynblog',
            'item_id' => 0,
        ];

        foreach ($aPosts as $aPost) {
            /**
             * Break this process if user do not have permission (maybe exceed maximum blog can create)
             */
            if (!Phpfox::getService('ynblog.permission')->canCreateBlog()) {
                break;
            }
            if (empty($aPost['title']))
                continue;

            if (!empty($aPost['list_tags'])) {
                $aTagList = $aPost['list_tags'];
                unset($aPost['list_tags']);
            }

            $aInsert = array_merge($aPost, $aDefaultFields);
            $iId = $this->database()->insert(Phpfox::getT('ynblog_blogs'), $aInsert);

            if (Phpfox::isModule('tag')) {
                Phpfox::getService('tag.process')->add('ynblog', $iId, Phpfox::getUserId(), $aInsert['text'], true);

                if (isset($aTagList) && ((is_array($aTagList) && count($aTagList)) || (!empty($aTagList)))) {
                    Phpfox::getService('tag.process')->add('ynblog', $iId, Phpfox::getUserId(), $aTagList);
                }
            }

            $this->addCategoryForBlog($iId, $aCategories, true);

            $this->cache()->remove('ynblog_my_total', 'substr');
            $bSuccess++;
        }

        $this->cache()->remove('ynblog', 'substr');

        return $bSuccess;
    }

    /**
     * Send mail and notification for all followers
     * @param $iBlogId
     */
    public function sendMailandNotification($iBlogId)
    {
        $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);
        $aFollowers = Phpfox::getService('ynblog.blog')->getAllFollowingByBloggerId($aBlog['user_id']);

        if ($aFollowers) {
            foreach ($aFollowers as $aFollower) {
                $sLink = Phpfox::getLib('url')->permalink('ynblog', $iBlogId, $aBlog['title']);
                $aUser = [];
                Phpfox::getService('user')->getUserFields(true, $aUser, null, $aFollower['follower_id']);
                if (!empty($aUser)) {
                    Phpfox::getLib('mail')->to($aFollower['follower_id'])
                        ->subject(array('full_name_has_just_written_a_new_blog', array('user_name' => Phpfox::getUserBy('user_name'))))
                        ->message(array('blogger_user_name_has_just_written_a_new_blog_blog_name', array('user_name' => $aUser['full_name'], 'link' => $sLink, 'title' => $aBlog['title'])))
                        ->send();

                    Phpfox::getService("notification.process")->add("ynblog_newblog", $iBlogId, $aFollower['follower_id'], $aBlog['user_id']);
                }
            }
        }
    }

    /**
     * @param int $iId
     * @param bool $bMinus
     */
    public function updateCounter($iId, $bMinus = false)
    {
        $this->database()->update(Phpfox::getT('ynblog_blogs'), ['total_comment' => 'total_comment ' . ($bMinus ? "-" : "+") . ' 1'], ['blog_id' => (int)$iId], false);
    }

    public function processImagesImportBlog($iItemId, $sImportImagePath, $iImportImageServerId)
    {
        $aSizes = Phpfox::getParam('ynblog.thumbnail_sizes');

        $oImage = Phpfox::getLib('image');
        $sPicStorage = Phpfox::getParam('core.dir_pic') . 'blog/';
        $sDesPicStorage = Phpfox::getParam('core.dir_pic') . 'ynadvancedblog/';
        $iServerId = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');

        if ($iImportImageServerId) {
            // import image from cdn
            // get image url
            $sSrcImageUrl = Phpfox::getLib('image.helper')->display([
                'server_id' => $iImportImageServerId,
                'path' => 'core.url_pic',
                'file' => 'blog/' . $sImportImagePath,
                'suffix' => '',
                'return_url' => true
            ]);

            // check temporary directory
            if (!is_dir(Phpfox::getParam('core.dir_file_temp'))) {
                @mkdir(Phpfox::getParam('core.dir_file_temp'), 0777, true);
            }
            // create temporary file
            $sSrcImagePath = Phpfox::getParam('core.dir_file_temp') . uniqid();
            @file_put_contents($sSrcImagePath, @fox_get_contents($sSrcImageUrl));

            if (empty($sSrcImagePath)) {
                return false;
            }

            // remove temp file
            register_shutdown_function(function () use ($sSrcImagePath) {
                @unlink($sSrcImagePath);
            });
        } else {
            // import image from local server
            $sSrcImagePath = $sPicStorage . sprintf($sImportImagePath, '');
        }

        if (!is_dir($sDesPicStorage)) {
            @mkdir($sDesPicStorage, 0777, 1);
            @chmod($sDesPicStorage, 0777);
        }

        foreach ($aSizes as $aSize) {
            $sNewFilePath = $sDesPicStorage . sprintf($sImportImagePath, '_' . $aSize);
            $oImage->createThumbnail($sSrcImagePath, $sNewFilePath, $aSize, $aSize, true);
        }

        $this->database()->update(Phpfox::getT('ynblog_blogs'), array(
            'blog_id' => $iItemId,
            'image_path' => $sImportImagePath,
            'server_id' => $iServerId,
        ), 'blog_id = ' . $iItemId);

        return true;
    }

    private function _processUploadForm($aVals, &$aInsert)
    {
        if (!empty($aVals['remove_logo'])) {
            $aInsert['image_path'] = null;
            $aInsert['server_id'] = 0;
        } else if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aInsert['image_path'] = $aFile['path'];
                $aInsert['server_id'] = $aFile['server_id'];
                $aInsert['is_old_suffix'] = 0;
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }

        return true;
    }

}
