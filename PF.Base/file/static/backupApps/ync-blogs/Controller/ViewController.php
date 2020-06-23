<?php

namespace Apps\YNC_Blogs\Controller;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox_Component;

class ViewController extends Phpfox_Component
{
    public function process()
    {
        if ($this->request()->getInt('id')) {
            return Phpfox::getLib('module')->setController('error.404');
        }

        if (Phpfox::isUser() && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->delete('comment_ynblog', $this->request()->getInt('req2'), Phpfox::getUserId());
            Phpfox::getService('notification.process')->delete('ynblog_like', $this->request()->getInt('req2'), Phpfox::getUserId());
        }

        user('yn_advblog_view', null, null, true);

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_view_process_start')) ? eval($sPlugin) : false);

        $bIsProfile = $this->getParam('bIsProfile');
        if ($bIsProfile === true) {
            $this->setParam(array(
                    'bViewProfileBlog' => true,
                    'sTagType' => 'ynblog'
                )
            );
        }

        $aItem = Phpfox::getService('ynblog.blog')->getBlog($this->request()->getInt('req2'));

        if (empty($aItem['blog_id'])) {
            return Phpfox_Error::display(_p('blog_not_found'));
        }

        if (isset($aItem['module_id']) && !empty($aItem['item_id']) && !Phpfox::isModule($aItem['module_id'])) {
            return Phpfox_Error::display(_p('cannot_find_the_parent_item'));
        }

        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aItem['user_id'])) {
            return Phpfox::getLib('module')->setController('error.invalid');
        }

        if (Phpfox::getUserId() == $aItem['user_id'] && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->delete('ynblog_approved', $this->request()->getInt('req2'), Phpfox::getUserId());
        }

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('ynblog', $aItem['blog_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend']);
        }

        if (isset($aItem['module_id']) && Phpfox::isModule($aItem['module_id']) && Phpfox::hasCallback($aItem['module_id'], 'checkPermission')) {
            if (!Phpfox::callback($aItem['module_id'] . '.checkPermission', $aItem['item_id'], 'ynblog.view_browse_ynblogs')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }

        if (!Phpfox::getUserParam('yn_advblog_approve')) {
            if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId()) {
                return Phpfox_Error::display(_p('blog_not_found'), 404);
            }
        }

        if (($aItem['post_status'] != 'public' || $aItem['is_approved'] == 0) && Phpfox::getUserId() != $aItem['user_id'] && !Phpfox::getUserParam('yn_advblog_edit_other')) {
            return Phpfox_Error::display(_p('blog_not_found'));
        }

        // Increment the view counter
        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aItem['is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('ynblog', $aItem['blog_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('ynblog', $aItem['blog_id']);
                } else {
                    Phpfox::getService('track.process')->update('ynblog', $aItem['blog_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }
        if ($bUpdateCounter) {
            Phpfox::getService('ynblog.process')->updateView($aItem['blog_id']);
            $aItem['total_view'] += 1;
        }

        // Define params for "review views" block
        $this->setParam(array(
                'sTrackType' => 'ynblog',
                'iTrackId' => $aItem['blog_id'],
                'iTrackUserId' => $aItem['user_id']
            )
        );

        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('ynblog', $aItem['blog_id']);
            if (isset($aTags[$aItem['blog_id']])) {
                $aItem['tag_list'] = $aTags[$aItem['blog_id']];
            }
        }

        $aItem['bookmark_url'] = Phpfox::permalink('ynblog', $aItem['blog_id'], $aItem['title']);

        Phpfox::getService('ynblog.blog')->retrievePermissionForBlog($aItem);

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_view_process_middle')) ? eval($sPlugin) : false);

        // Add tags to meta keywords
        if (!empty($aItem['tag_list']) && $aItem['tag_list'] && Phpfox::isModule('tag')) {
            $this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aItem['tag_list']));
        }

        if (isset($aItem['module_id']) && Phpfox::hasCallback($aItem['module_id'], 'getBlogDetails')) {
            if ($aCallback = Phpfox::callback($aItem['module_id'] . '.getBlogDetails', $aItem)) {
                $this->template()->setBreadCrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadCrumb($aCallback['title'], $aCallback['url_home']);
            }
        }
        $this->setParam('aFeed', array(
                'comment_type_id' => 'ynblog',
                'privacy' => $aItem['privacy'],
                'comment_privacy' => $aItem['privacy_comment'],
                'like_type_id' => 'ynblog',
                'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
                'feed_is_friend' => $aItem['is_friend'],
                'item_id' => $aItem['blog_id'],
                'user_id' => $aItem['user_id'],
                'total_comment' => $aItem['total_comment'],
                'total_like' => $aItem['total_like'],
                'feed_link' => $aItem['bookmark_url'],
                'feed_title' => $aItem['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aItem['total_like'],
                'report_module' => 'ynblog',
                'report_phrase' => _p('report_this_blog'),
                'time_stamp' => $aItem['time_stamp']
            )
        );

        // Set param for block in detail
        $this->setParam(array(
            'aBlog' => $aItem,
            'blog_id' => $aItem['blog_id'],
        ));

        $aItem['bCanFavorite'] = (Phpfox::getUserId() && Phpfox::getUserId() != $aItem['user_id'] && $aItem['is_approved'] == 1 && $aItem['post_status'] == 'public');

        $sBreadcrumb = $this->url()->makeUrl('ynblog');
        if (isset($aCallback) && isset($aCallback['item_id'])) {
            $sBreadcrumb = $aCallback['url_home'] . 'advanced-blog';
        }

        if (isset($aCallback) && isset($aCallback['module_id']) && $aCallback['module_id'] == 'pages') {
            $this->setParam('sTagListParentModule', $aItem['module_id']);
            $this->setParam('iTagListParentId', (int)$aItem['item_id']);
        }

        // Set og tag
        if ($aItem['image_path']) {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aItem['server_id'],
                    'path' => 'core.url_pic',
                    'file' => 'ynadvancedblog/' . $aItem['image_path'],
                    'suffix' => '_big',
                    'return_url' => true
                )
            );
            $size_img = @getimagesize($sImage);
            if (!empty($size_img)) {
                $og_image_width = $size_img[0];
                $og_image_height = $size_img[1];
            } else {
                $og_image_width = 640;
                $og_image_height = 442;
            }
            $this->template()
                ->setMeta('og:image', $sImage)
                ->setMeta('og:image:width', $og_image_width)
                ->setMeta('og:image:height', $og_image_height);
        }

        $aTitleLabel = [
            'type_id' => 'ync-blog'
        ];
        if ($aItem['is_featured']) {
            Phpfox::getLib('module')->appendPageClass('item-featured');
            $aTitleLabel['label']['featured'] =[
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class'  => 'diamond'
            ];
        }

        $this->template()->setTitle($aItem['title'])
            ->setBreadCrumb(_p('Blogs'), $sBreadcrumb)
            ->setBreadCrumb($aItem['title'], '', true)
            ->setMeta('description', $aItem['title'] . '.')
            ->setMeta('description', $aItem['text'] . '.')
            ->setMeta('keywords', $this->template()->getKeywords($aItem['title']))
            ->assign(array(
                    'aItem' => $aItem,
                    'bBlogView' => true,
                    'bIsProfile' => $bIsProfile,
                    'sTagType' => ($bIsProfile === true ? 'ynblog_profile' : 'ynblog'),
                    'sMicroPropType' => 'BlogPosting',
                    'sUrl' => Phpfox::permalink('ynblog.embed', $aItem['blog_id'], $aItem['title']),
                    'appPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs',
                    'aTitleLabel' => $aTitleLabel
                )
            )->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                    'jscript/clipboard.min.js' => 'app_ync-blogs'
                )
            );

        $aCategories = Phpfox::getService('ynblog.category')->getCategoryByBlogId($aItem['blog_id']);

        foreach ($aCategories as $aCategory) {
            $this->template()->setBreadCrumb(_p($aCategory['name']), $this->url()->permalink(array('ynblog.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], _p($aCategory['name'])));
        }

        if ($this->request()->get('req4') == 'comment') {
            $this->template()->setHeader('<script type="text/javascript">var $bScrollToBlogComment = false; $Behavior.scrollToBlogComment = function () { if ($bScrollToBlogComment) { return; } $bScrollToBlogComment = true; if ($(\'#js_feed_comment_pager_' . $aItem['blog_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_pager_' . $aItem['blog_id'] . '\', 800); } }</script>');
        }

        if ($this->request()->get('req4') == 'add-comment') {
            $this->template()->setHeader('<script type="text/javascript">var $bScrollToBlogComment = false; $Behavior.scrollToBlogComment = function () { if ($bScrollToBlogComment) { return; } $bScrollToBlogComment = true; if ($(\'#js_feed_comment_form_' . $aItem['blog_id'] . '\').length > 0) { $.scrollTo(\'#js_feed_comment_form_' . $aItem['blog_id'] . '\', 800); $Core.commentFeedTextareaClick($(\'.js_comment_feed_textarea\')); } }</script>');
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
    }
}
