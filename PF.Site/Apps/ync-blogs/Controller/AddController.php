<?php

namespace Apps\YNC_Blogs\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Error;

class AddController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $bIsEdit = false;
        $bCanEditPersonalData = true;
        $iMaxFileSize = setting('yn_advblog_max_file_size') * 1024;

        $sModule = $this->request()->get('module');
        $iItemId = $this->request()->getInt('item');
        if (($aVals = $this->request()->getArray('val')) && !empty($aVals['module_id']) && !empty($aVals['item_id'])) {
            $sModule = $aVals['module_id'];
            $iItemId = $aVals['item_id'];
        }
        if (!empty($sModule) && !empty($iItemId)) {
            $this->template()->assign([
                'sModule' => $sModule,
                'iItem' => $iItemId
            ]);
        }

        // Check if do not have any categories
        $sCategoriesGet = Phpfox::getService('ynblog.category')->get();

        if (!$sCategoriesGet) {
            Phpfox_Error::display(_p('there_are_no_categories'));
        }

        if (($iEditId = $this->request()->getInt('id'))) {
            $oBlog = Phpfox::getService('ynblog.blog');

            $aRow = $oBlog->getBlogForEdit($iEditId);

            if (empty($aRow) || empty($aRow['blog_id'])) {
                return Phpfox_Error::display(_p('blog_not_found'));
            }

            if ($aRow['is_approved'] != '1' &&
                ($aRow['user_id'] != Phpfox::getUserId() && !Phpfox::getUserParam('yn_advblog_edit_other'))
            ) {
                return Phpfox_Error::display(_p('unable_to_edit_this_blog'));
            }

            if (Phpfox::isModule('tag')) {
                $aTags = Phpfox::getService('tag')->getTagsById('ynblog', $aRow['blog_id']);
                if (isset($aTags[$aRow['blog_id']])) {
                    $aRow['tag_list'] = '';
                    foreach ($aTags[$aRow['blog_id']] as $aTag) {
                        $aRow['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $aRow['tag_list'] = trim(trim($aRow['tag_list'], ','));
                }
            }

            (Phpfox::getUserId() == $aRow['user_id'] ? Phpfox::getUserParam('yn_advblog_edit', true) : Phpfox::getUserParam('yn_advblog_edit_other', true));
            if (Phpfox::getUserParam('yn_advblog_edit_other') && Phpfox::getUserId() != $aRow['user_id']) {
                $bCanEditPersonalData = false;
            }

            $sCategories = Phpfox::getService('ynblog.category')->getStringCategoryByBlogId($aRow['blog_id']);

            $this->setParam('iIdCategoryEdit', (int)$sCategories);

            $bIsEdit = true;

            $this->template()->assign(array(
                    'aForms' => $aRow
                )
            );

            $this->template()->buildPageMenu('js_ynblogs_block', [], [
                'link' => Phpfox::permalink('ynblog', $iEditId, $aRow['title']),
                'phrase' => _p('view_blog')
            ]);

            if (!empty($aRow['module_id'])) {
                $sModule = $aRow['module_id'];
                $iItemId = $aRow['item_id'];
            }

            (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_add_process_edit')) ? eval($sPlugin) : false);
        } else {
            user('yn_advblog_add_blog',null,null,true);

            if (user('yn_advblog_max_blogs') && user('yn_advblog_max_blogs') <= Phpfox::getService('ynblog.blog')->getMyTotal()) {
                Phpfox_Error::display(_p('ynblog_you_have_exceeded_limited_writable_blogs'));
            }
        }

        $aValidation = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('fill_title_for_blog')
            ),
            'text' => array(
                'def' => 'required',
                'title' => _p('add_content_to_blog')
            )
        );

        if (Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_blog_add')) {
            $aValidation['image_verification'] = _p('complete_captcha_challenge');
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_add_process_validation')) ? eval($sPlugin) : false);

        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynblog_js_blog_form',
                'aParams' => $aValidation
            )
        );

        $aCallback = null;

        if (!empty($sModule) && Phpfox::hasCallback($sModule, 'getItem')) {
            $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
            if ($aCallback === false) {
                return Phpfox_Error::display(_p('cannot_find_the_parent_item'));
            }
            $bCheckParentPrivacy = true;
            if (!$bIsEdit && Phpfox::hasCallback($sModule, 'checkPermission')) {
                $bCheckParentPrivacy = Phpfox::callback($sModule . '.checkPermission', $iItemId, 'ynblog.share_blogs');
            }

            if (!$bCheckParentPrivacy) {
                return Phpfox_Error::display(_p('unable_to_add_this_item_due_to_privacy_settings'));
            }

            if ($bIsEdit) {
                $sUrl = $this->url()->makeUrl('ynblog', array('add', 'id' => $iEditId));
                $sCrumb = _p('editing_blog') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getService('core')->getEditTitleSize(), '...');
                $this->template()->buildPageMenu('js_ynblogs_block', [], [
                    'link' => Phpfox::permalink('ynblog', $iEditId, $aRow['title']),
                    'phrase' => _p('view_blog')
                ]);
            } else {
                $sUrl = $this->url()->makeUrl('ynblog', array('add', 'module' => $aCallback['module'], 'item' => $iItemId));
                $sCrumb = _p('write_blog_entry');
            }

            $this->template()
                ->setBreadCrumb(isset($aCallback['module_title']) ? $aCallback['module_title'] : _p($sModule), $this->url()->makeUrl($sModule))
                ->setBreadCrumb($aCallback['title'], Phpfox::permalink($sModule, $iItemId))
                ->setBreadCrumb(_p('Blog'), $this->url()->makeUrl($sModule, array($iItemId, 'blog')))
                ->setBreadCrumb($sCrumb, $sUrl);
        } else {
            if (!empty($sModule) && !empty($iItemId) && $sModule != 'blog' && $aCallback === null) {
                return Phpfox_Error::display(_p('cannot_find_the_parent_item'));
            }

            $this->template()
                ->setBreadCrumb(_p('Blog'), $this->url()->makeUrl('ynblog'))
                ->setBreadCrumb((!empty($iEditId) ? _p('editing_blog') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('Write Blog Entry')), ($iEditId > 0 ? $this->url()->makeUrl('ynblog', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('ynblog', array('add'))), true);

        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($oValid->isValid($aVals)) {
                //Check empty category
                if (empty(array_filter($aVals['category']))) {
                    Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
                }

                // Add the new blog
                if (isset($aVals['publish']) || isset($aVals['draft'])) {

                    if (isset($aVals['draft'])) {
                        $aVals['post_status'] = 'draft';
                        $sMessage = _p('blog_successfully_saved');
                    } else {
                        $sMessage = _p('your_blog_has_been_added');
                    }

                    if (($iFlood = Phpfox::getUserParam('flood_control_blog')) !== 0) {
                        $aFlood = array(
                            'action' => 'last_post', // The SPAM action
                            'params' => array(
                                'field' => 'time_stamp', // The time stamp field
                                'table' => Phpfox::getT('ynblog_blogs'), // Database table we plan to check
                                'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                                'time_stamp' => $iFlood * 60 // Seconds);
                            )
                        );

                        // actually check if flooding
                        if (Phpfox::getLib('spam')->check($aFlood)) {
                            Phpfox_Error::set(_p('your_are_posting_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                        }
                    }

                    if (Phpfox_Error::isPassed()) {
                        $iId = Phpfox::getService('ynblog.process')->add($aVals);
                    }
                }

                // Update a blog
                if ((isset($aVals['update']) || isset($aVals['draft_update']) || isset($aVals['draft_publish'])) && isset($aRow['blog_id']) && $bIsEdit) {
                    if (isset($aVals['draft_publish'])) {
                        $aVals['post_status'] = 'public';
                    }

                    // Update the blog
                    if (Phpfox_Error::isPassed()) {
                        $iId = Phpfox::getService('ynblog.process')->update($aRow['blog_id'], $aRow['user_id'], $aVals, $aRow);
                        $sMessage = _p('blog_publish_successfully');
                    }
                }

                if (isset($iId) && $iId) {
                    Phpfox::permalink('ynblog', $iId, $aVals['title'], true, $sMessage);
                }
            }
        }
        $this->template()->setPhrase(['add_content_to_blog', 'provide_a_category_this_item_will_belong_to'])
            ->assign(array(
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'bIsEdit' => $bIsEdit,
                    'bCanEditPersonalData' => $bCanEditPersonalData,
                    'sCategories' => $sCategoriesGet,
                    'iMaxFileSize' => $iMaxFileSize,
                    'bCanCustomPrivacy' => (empty($sModule) ? true : !Phpfox::hasCallback($sModule, 'inheritPrivacy'))
                )
            )
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                )
            );

        if (Phpfox::isModule('attachment')) {
            $this->setParam('attachment_share', array(
                    'type' => 'ynblog',
                    'id' => 'advancedblog_js_blog_form',
                    'edit_id' => ($bIsEdit ? $iEditId : 0)
                )
            );
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_add_process')) ? eval($sPlugin) : false);
    }
}
