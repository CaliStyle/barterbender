<?php

namespace Apps\YNC_Blogs\Ajax;

use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{
    public function updateActivity()
    {
        Phpfox::getService('ynblog.process')->updateActivity($this->get('id'), $this->get('active'));
    }

    public function approveBlog()
    {
        $iBlogId = (int)$this->get('iBlogId');
        if ($iBlogId) {
            if (Phpfox::getService('ynblog.permission')->canApproveBlog($iBlogId) && Phpfox::getService('ynblog.process')->approveBlog($iBlogId, 1)) {
                $this->alert(_p('blog_successfully_approved'));
                $this->call('setTimeout(function(){ location.reload(); },3000);');
            } else {
                $this->alert(_p('you_do_not_have_permission_to_approve_this_blog'));
            }
        }
    }

    public function denyBlog()
    {
        $iBlogId = (int)$this->get('iBlogId');
        if ($iBlogId) {
            if (Phpfox::getService('ynblog.process')->approveBlog($iBlogId, 0)) {
                $this->alert(_p('blog_successfully_denied'));
                $this->call('setTimeout(function(){ location.reload(); },3000);');
            } else {
                $this->alert(_p('you_do_not_have_permission_to_deny_this_blog'));
            }
        }
    }

    public function publishBlog()
    {
        $iBlogId = (int)$this->get('iBlogId');
        if ($iBlogId) {
            $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iBlogId);
            $aVals = $aBlog;

            if (Phpfox::getService('ynblog.permission')->canEditBlog($iBlogId) && $aBlog['post_status'] == 'draft') {
                $sCategories = Phpfox::getService('ynblog.category')->getStringCategoryByBlogId($aBlog['blog_id']);
                $aVals['draft_publish'] = true;
                $aVals['post_status'] = 'public';
                $aVals['category'] = explode(',', $sCategories);
                Phpfox::getService('ynblog.process')->update($iBlogId, $aBlog['user_id'], $aVals, $aBlog);
                (user('yn_advblog_automatically_approve')) ? $this->alert(_p('blog_successfully_public_please_waiting_approval_from_administrator')) : $this->alert(_p('blog_successfully_public'));
                $this->call('setTimeout(function(){ location.reload(); },3000);');
            } else {
                $this->alert(_p('you_do_not_have_permission_to_approve_this_blog'));
            }
        }
    }

    public function updateFavorite()
    {
        $iBlogId = $this->get('iBlogId');
        $bFavorite = $this->get('bFavorite');
        $iFavoriteTotal = Phpfox::getService('ynblog.blog')->getFavoriteTotal();

        if ($bFavorite) {
            Phpfox::getService('ynblog.process')->addFavorite(Phpfox::getUserId(), $iBlogId);
            $sHtml = '<a class="btn btn-primary btn-sm" onclick="$Core.ajaxMessage();ynadvancedblog.updateFavorite(' . $iBlogId . ',0);return false;"><i class="fa fa-star-o" aria-hidden="true"></i>&nbsp;' . _p('Favorited') . '</a>';
            $iFavoriteTotal++;
            $iFavoriteTotal = ($iFavoriteTotal >= 100) ? '99+' : $iFavoriteTotal;
            $this->call('$("#total_favorite_blog").text(' . $iFavoriteTotal . '); $("#ynadvblog-detail-favorite-blog-' . $iBlogId . '").html(\'' . $sHtml . '\');' . ($iFavoriteTotal ? '' : 'window.location.reload();'));
        } else {
            $this->call("$('#js_blog_id_" . $iBlogId . "').remove();");
            Phpfox::getService('ynblog.process')->deleteFavorite(Phpfox::getUserId(), $iBlogId);
            $sHtml = '<a class="btn btn-default btn-sm" onclick="$Core.ajaxMessage();ynadvancedblog.updateFavorite(' . $iBlogId . ',1);return false;"><i class="fa fa-star" aria-hidden="true"></i>&nbsp;' . _p('Favorite') . '</a>';
            $iFavoriteTotal--;
            $iFavoriteTotal = ($iFavoriteTotal >= 100) ? '99+' : $iFavoriteTotal;
            $this->call('$("#total_favorite_blog").text(' . $iFavoriteTotal . '); $("#ynadvblog-detail-favorite-blog-' . $iBlogId . '").html(\'' . $sHtml . '\');' . ($iFavoriteTotal ? '' : 'window.location.reload();'));
        }
    }

    public function updateFollow()
    {
        $iBloggerId = $this->get('iUserId');
        $bFollow = $this->get('bFollow');
        $aNewFollowTotal = Phpfox::getService('ynblog.blog')->getAllFollowingByBloggerId($iBloggerId);
        $iNewFollowTotal = count($aNewFollowTotal);

        if ($bFollow) {
            Phpfox::getService('ynblog.process')->addFollow(Phpfox::getUserId(), $iBloggerId);
            $sHtml = '<button class="btn btn-sm btn-primary" onclick="ynadvancedblog.updateFollow(' . $iBloggerId . ',0);return false;"><i class="fa fa-minus" aria-hidden="true"></i>&nbsp;' . _p('Un-Follow') . '</button>';
            $iNewFollowTotal++;
            $sNewFollowTotal = $iNewFollowTotal . '&nbsp;' . ($iNewFollowTotal == 1 ? _p('follower') : _p('followers'));

            // Update total my following blogger
            $iFollowingTotal = Phpfox::getService('ynblog.blog')->getFollowingTotal();
            $iFollowingTotal = ($iFollowingTotal >= 100) ? '99+' : $iFollowingTotal;
            $this->call('$("#total_follow_blogger").text(' . $iFollowingTotal . '); $("#js_ynblog_total_update_follow_' . $iBloggerId . '").html(\'' . $sNewFollowTotal . '\');$("#js_ynblog_update_follow_' . $iBloggerId . '").html(\'' . $sHtml . '\');');
        } else {
            Phpfox::getService('ynblog.process')->deleteFollow(Phpfox::getUserId(), $iBloggerId);
            $sHtml = '<button class="btn btn-sm btn-primary" onclick="ynadvancedblog.updateFollow(' . $iBloggerId . ',1);return false;"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;' . _p('Follow') . '</button>';
            $iNewFollowTotal--;
            $iNewFollowTotal = ($iNewFollowTotal < 0 ? 0 : $iNewFollowTotal);
            $sNewFollowTotal = $iNewFollowTotal . '&nbsp;' . ($iNewFollowTotal == 1 ? _p('follower') : _p('followers'));

            // Update total my following blogger
            $iFollowingTotal = Phpfox::getService('ynblog.blog')->getFollowingTotal();
            $iFollowingTotal = ($iFollowingTotal >= 100) ? '99+' : $iFollowingTotal;
            $this->call('$("#total_follow_blogger").text(' . $iFollowingTotal . '); $("#js_ynblog_my_following_blogger_item_' . $iBloggerId . '").remove(); $("#js_ynblog_total_update_follow_' . $iBloggerId . '").html(\'' . $sNewFollowTotal . '\'); $("#js_ynblog_update_follow_' . $iBloggerId . '").html(\'' . $sHtml . '\');');
        }
    }

    public function updateFollowLink()
    {
        $iBloggerId = $this->get('iUserId');
        $bFollow = $this->get('bFollow');
        $aNewFollowTotal = Phpfox::getService('ynblog.blog')->getAllFollowingByBloggerId($iBloggerId);
        $iNewFollowTotal = count($aNewFollowTotal);

        if ($bFollow) {
            Phpfox::getService('ynblog.process')->addFollow(Phpfox::getUserId(), $iBloggerId);
            $sHtml = '<a onclick="ynadvancedblog.updateFollowLink(' . $iBloggerId . ',0);return false;"><i class="fa fa-minus" aria-hidden="true"></i>&nbsp;' . _p('Un-Follow') . '</a>';
            $iNewFollowTotal++;
            $sNewFollowTotal = $iNewFollowTotal . '&nbsp;' . ($iNewFollowTotal == 1 ? _p('follower') : _p('followers'));

            // Update total my following blogger
            $iFollowingTotal = Phpfox::getService('ynblog.blog')->getFollowingTotal();
            $iFollowingTotal = ($iFollowingTotal >= 100) ? '99+' : $iFollowingTotal;
            $this->call('$("#total_follow_blogger").text(' . $iFollowingTotal . '); $("#js_ynblog_total_update_follow_' . $iBloggerId . '").html(\'' . $sNewFollowTotal . '\');$("#js_ynblog_update_follow_' . $iBloggerId . '").html(\'' . $sHtml . '\');');
        } else {
            Phpfox::getService('ynblog.process')->deleteFollow(Phpfox::getUserId(), $iBloggerId);
            $sHtml = '<a onclick="ynadvancedblog.updateFollowLink(' . $iBloggerId . ',1);return false;"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;' . _p('Follow') . '</a>';
            $iNewFollowTotal--;
            $iNewFollowTotal = ($iNewFollowTotal < 0 ? 0 : $iNewFollowTotal);
            $sNewFollowTotal = $iNewFollowTotal . '&nbsp;' . ($iNewFollowTotal == 1 ? _p('follower') : _p('followers'));

            // Update total my following blogger
            $iFollowingTotal = Phpfox::getService('ynblog.blog')->getFollowingTotal();
            $iFollowingTotal = ($iFollowingTotal >= 100) ? '99+' : $iFollowingTotal;
            $this->call('$("#total_follow_blogger").text(' . $iFollowingTotal . '); $("#js_ynblog_my_following_blogger_item_' . $iBloggerId . '").remove(); $("#js_ynblog_total_update_follow_' . $iBloggerId . '").html(\'' . $sNewFollowTotal . '\'); $("#js_ynblog_update_follow_' . $iBloggerId . '").html(\'' . $sHtml . '\');');
        }
    }

    public function updateSavedBlog()
    {
        $iBloggerId = $this->get('iUserId');
        $bSavedBlog = $this->get('bSavedBlog');
        $iTotalSaved = (int)Phpfox::getService('ynblog.blog')->getSavedTotal();

        if ($bSavedBlog) {
            Phpfox::getService('ynblog.process')->addSavedBlog(Phpfox::getUserId(), $iBloggerId);
            $sHtml = '<a title="' . _p('unsave_this_ynblog') . '" href="javascript:void(0)" onclick="ynadvancedblog.updateSavedBlog(' . $iBloggerId . ',0);return false;"><i class="fa fa-bookmark hover" aria-hidden="true"></i></a>';
            $iTotalSaved++;
            $this->call('$("#total_saved_blog").show(); $("#total_saved_blog").text(' . $iTotalSaved . '); $(".js_ynblog_saved_blog_' . $iBloggerId . '").addClass(\'active\');$(".js_ynblog_saved_blog_' . $iBloggerId . '").html(\'' . $sHtml . '\');' . ($iTotalSaved ? '' : 'window.location.reload();'));
        } else {
            Phpfox::getService('ynblog.process')->deleteSavedBlog(Phpfox::getUserId(), $iBloggerId);
            $sHtml = '<a title="' . _p('save_this_ynblog') . '" href="javascript:void(0)" onclick="ynadvancedblog.updateSavedBlog(' . $iBloggerId . ',1);return false;"><i class="fa fa-bookmark hover" aria-hidden="true"></i></a>';
            $iTotalSaved--;
            $this->call('$("#total_saved_blog").text(' . $iTotalSaved . '); $(".js_ynblog_saved_blog_' . $iBloggerId . '").removeClass(\'active\'); $(".js_ynblog_saved_blog_' . $iBloggerId . '").html(\'' . $sHtml . '\');' . ($iTotalSaved ? '' : 'window.location.reload();'));
        }
    }

    public function updateFeature()
    {
        $iBlogId = $this->get('iBlogId');
        $bFeature = $this->get('bFeature');

        if ($bFeature) {
            Phpfox::getService('ynblog.process')->featureBlog($iBlogId, $bFeature);
            $sHtml = '<a href="javascript:void(0)" onclick="ynadvancedblog.updateFeature(' . $iBlogId . ',0);return false;"><i class="fa fa-diamond" aria-hidden="true"></i>' . _p('un_feature') . '</a>';
            $this->call('$(".js_ynblog_featured_blog_btn_' . $iBlogId . '").html(\'' . $sHtml . '\');');
        } else {
            Phpfox::getService('ynblog.process')->featureBlog($iBlogId, $bFeature);
            $sHtml = '<a href="javascript:void(0)" onclick="ynadvancedblog.updateFeature(' . $iBlogId . ',1);return false;"><i class="fa fa-diamond" aria-hidden="true"></i>' . _p('feature') . '</a>';
            $this->call('$(".js_ynblog_featured_blog_btn_' . $iBlogId . '").html(\'' . $sHtml . '\');');
        }
    }

    public function changePageManageBlog()
    {
        $aPage = $this->get('page');
        $aSearch = array(
            'title' => $this->get('title'),
            'author' => $this->get('author'),
            'category' => $this->get('category'),
            'feature' => $this->get('feature'),
            'post_status' => $this->get('post_status')
        );
        Phpfox::getComponent('ynblog.admincp.manageblogs', array('page' => $aPage, 'search' => $aSearch), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function changePageImportCoreBlog()
    {
        $aPage = $this->get('page');
        $aSearch = array(
            'title' => $this->get('title'),
            'author' => $this->get('author'),
            'from_time' => $this->get('js_start_time__datepicker', $this->get('from_time')),
            'end_time' => $this->get('js_end_time__datepicker', $this->get('end_time'))
        );
        Phpfox::getComponent('ynblog.admincp.importcoreblogs', array('page' => $aPage, 'search' => $aSearch), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function updateFeaturedInAdmin()
    {
        // Get Params
        $iBlogId = (int)$this->get('iBlogId');
        $iIsFeatured = (int)$this->get('active');
        $sResult = false;

        $oProcess = Phpfox::getService('ynblog.process');
        if (!Phpfox::getService('ynblog.permission')->canFeatureBlog($iBlogId)) {
            $this->alert(_p('you_can_not_feature_a_pending_blog'));
            return false;
        }

        if ($iBlogId) {
            $sResult = $oProcess->featureBlog($iBlogId, $iIsFeatured);
        }
        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_blog'));
            $this->call('setTimeout(function(){window.location.reload();}, 1800);');
            return false;
        }

        return true;
    }

    public function actionMultiSelectBlog()
    {
        $aVals = $this->get('blog_row');
        $aType = $this->get('val');

        if (!count($aVals)) {
            $this->alert(_p('no_blogs_selected'));
            return false;
        }

        $oProcess = Phpfox::getService('ynblog.process');
        if ($aType['selected']) {
            switch ($aType['selected']) {
                case '1':
                    $success = false;
                    foreach ($aVals as $key => $blogID) {
                        $sResult = $oProcess->deleteBlog($blogID);
                        if (!$sResult) {
                            $success = false;
                            $this->alert(_p('you_do_not_have_permission_to_delete_blogs'));
                            continue;
                        } else {
                            $success = true;
                            $this->call('setTimeout(function(){ location.reload(); },1000);');
                        }
                    }
                    break;
                case '2':
                    foreach ($aVals as $key => $blogID) {
                        $sResult = $oProcess->approveBlog($blogID, 1);
                        if (!$sResult) {
                            $this->alert(_p('you_do_not_have_permission_to_approve_blogs'));
                            continue;
                        } else {
                            $success = true;
                        }

                        if ($success) {
                            $this->alert(_p('blogs_successfully_approved'));
                            $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.apps_menu ul li:eq(3) a\').trigger(\'click\')},2000);');
                        } else {
                            $this->alert(_p('approve_failed'));
                        }
                    }
                    break;
                case '3':
                    foreach ($aVals as $key => $blogID) {
                        $sResult = $oProcess->approveBlog($blogID, 0);
                        if (!$sResult) {
                            $this->alert(_p('you_do_not_have_permission_to_deny_blogs'));
                            continue;
                        } else {
                            $success = true;
                        }

                        if ($success) {
                            $this->alert(_p('blogs_successfully_denied'));
                            $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.apps_menu ul li:eq(3) a\').trigger(\'click\')},2000);');
                        } else {
                            $this->alert(_p('deny_failed'));
                        }
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
    }

    public function filterAdminFilterBlog()
    {
        $aSearch = $this->get('search');
        Phpfox::getComponent('ynblog.admincp.manageblogs', array('search' => $aSearch), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function filterAdminFilterImportBlog()
    {
        $aSearch = $this->get('search');
        $aSearch['from_time'] = $this->get('js_start_time__datepicker');
        $aSearch['end_time'] = $this->get('js_end_time__datepicker');
        Phpfox::getComponent('ynblog.admincp.importcoreblogs', array('search' => $aSearch), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function deleteBlogInAdmin()
    {
        $iBlogId = (int)$this->get('iBlogId');
        if ($iBlogId) {
            if (Phpfox::getService('ynblog.process')->deleteBlog($iBlogId)) {
                $this->alert(_p('blog_successfully_deleted'));
                $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.apps_menu ul li:eq(3) a\').trigger(\'click\')},2000);');
            } else {
                $this->alert(_p('you_do_not_have_permission_to_delete_this_blog'));
            }
        }
    }

    public function approveBlogInAdmin()
    {
        $iBlogId = (int)$this->get('iBlogId');
        if ($iBlogId) {
            if (Phpfox::getService('ynblog.process')->approveBlog($iBlogId, 1)) {
                $this->alert(_p('blog_successfully_approved'));
                $this->html('#ynab_blog_update_approve_' . $iBlogId, _p('Public'));
            } else {
                $this->alert(_p('you_do_not_have_permission_to_approve_this_blog'));
            }
        }
    }

    public function denyBlogInAdmin()
    {
        $iBlogId = (int)$this->get('iBlogId');
        if ($iBlogId) {
            if (Phpfox::getService('ynblog.process')->approveBlog($iBlogId, 0)) {
                $this->alert(_p('blog_successfully_denied'));
                $this->html('#ynab_blog_update_approve_' . $iBlogId, _p('Denied'));
            } else {
                $this->alert(_p('you_do_not_have_permission_to_deny_this_blog'));
            }
        }
    }

    public function importBlogInAdmin()
    {
        $iBlogId = (int)$this->get('iBlogId');
        $sBlogId = $this->get('sBlogId');
        $iCategory = (int)$this->get('category');
        if (!$iCategory) {
            return $this->alert(_p('please_select_a_category'));
        }

        if ($iBlogId) {
            if (Phpfox::getService('ynblog.process')->importBlog($iBlogId, $iCategory)) {
                $this->alert(_p('blog_s_successfully_imported'));
                $this->call('$("#js_row' . $iBlogId . '").slideUp();');
            } else {
                return $this->alert(_p('you_do_not_have_permission_to_import_this_blog'));
            }
        } else if ($sBlogId) {
            $aBlogId = explode(',', $sBlogId);

            if (count($aBlogId) > 0) {
                foreach ($aBlogId as $iBlogId) {
                    Phpfox::getService('ynblog.process')->importBlog($iBlogId, $iCategory);
                }

                $this->alert(_p('blog_s_successfully_imported'));
//                return $this->call('setTimeout(function() {js_box_remove($(\'.js_box_close\'));$(\'.apps_menu ul li:eq(4) a\').trigger(\'click\')},2000);');
                $this->call('setTimeout(function(){ location.reload(); },1000);');
            }
        }
    }

    public function chooseCategoryInAdmin()
    {
        $iBlogId = (int)$this->get('iBlogId');
        $aBlogId = $this->get('blog_row');
        Phpfox::getBlock('ynblog.admin.importchoosecategory', array('iBlogId' => $iBlogId, 'aBlogId' => $aBlogId));
        $this->setTitle(_p('select_category'));
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sAction = $this->get('action');
        $sMessage = '';
        $aIds = $this->get('item_moderate');

        switch ($sAction) {
            case 'approve':
                if (!user('yn_advblog_approve')) {
                    $sMessage = _p('you_do_not_have_permission_on_this_action');
                    break;
                }
                foreach ((array)$aIds as $iId) {
                    if (Phpfox::getService('ynblog.permission')->canApproveBlog($iId)) {
                        Phpfox::getService('ynblog.process')->approveBlog($iId, 1);
                    }
                }
                $sMessage = _p('Blog(s) successfully approved');
                break;
            case 'delete':
                if (!user('yn_advblog_delete_other')) {
                    $sMessage = _p('you_do_not_have_permission_on_this_action');
                    break;
                }
                foreach ((array)$aIds as $iId) {
                    Phpfox::getService('ynblog.process')->deleteBlog($iId);
                }
                $sMessage = _p('Blog(s) successfully deleted');
                break;
            case 'feature':
                if (!user('yn_advblog_feature')) {
                    $sMessage = _p('you_do_not_have_permission_on_this_action');
                    break;
                }
                foreach ((array)$aIds as $iId) {
                    Phpfox::getService('ynblog.process')->featureBlog($iId, 1);
                }
                $sMessage = _p('Blog(s) successfully featured');
                break;
            case 'unfeature':
                if (!user('yn_advblog_feature')) {
                    $sMessage = _p('you_do_not_have_permission_on_this_action');
                    break;
                }
                foreach ((array)$aIds as $iId) {
                    Phpfox::getService('ynblog.process')->featureBlog($iId, 0);
                }
                $sMessage = _p('Blog(s) successfully un-featured');
                break;
            case 'un_saved':
                foreach ((array)$aIds as $iId) {
                    Phpfox::getService('ynblog.process')->deleteSavedBlog(Phpfox::getUserId(), $iId);
                }
                $sMessage = _p('Blog(s) successfully un-saved');
                break;
            case 'deny':
                if (!user('yn_advblog_approve')) {
                    $sMessage = _p('you_do_not_have_permission_on_this_action');
                    break;
                }
                foreach ((array)$aIds as $iId) {
                    if (Phpfox::getService('ynblog.permission')->canDenyBlog($iId)) {
                        Phpfox::getService('ynblog.process')->approveBlog($iId, 0);
                    }
                }
                $sMessage = _p('Blog(s) successfully denied');
                break;
            case 'publish':
                foreach ((array)$aIds as $iId) {
                    $aBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iId);
                    $aVals = $aBlog;

                    if (Phpfox::getService('ynblog.permission')->canEditBlog($iId) && $aBlog['post_status'] == 'draft') {
                        $sCategories = Phpfox::getService('ynblog.category')->getStringCategoryByBlogId($aBlog['blog_id']);
                        $aVals['draft_publish'] = true;
                        $aVals['post_status'] = 'public';
                        $aVals['category'] = explode(',', $sCategories);
                        Phpfox::getService('ynblog.process')->update($iId, $aBlog['user_id'], $aVals, $aBlog);
                    }
                }
                $sMessage = _p('Blog(s) successfully public');
                break;
            case 'export_wordpress':
            case 'export_tumblr':
            case 'export_blogger':
                $this->hide('.moderation_process');
                $this->call('window.location = \'' . Phpfox::getLib('url')->makeUrl('ynblog.export', array('aIds' => $aIds, 'sType' => $sAction), true) . '\';');
                $bDontReload = true;
                break;
        }
        if (empty($bDontReload)) {
            $this->alert($sMessage, _p('Moderation'), 300, 150, true);

            $this->hide('.moderation_process');
            $this->call('setTimeout(function(){ location.reload(); },3000);');
        }

    }

    public function getsubcategory()
    {
        $iSub = $this->get('id');
        Phpfox::getComponent('ynblog.admincp.category', array('sub' => $iSub), 'controller');
        $this->html('#site_content', $this->getContent(false));
        $this->call('$(".link_menu").hide(); $Core.loadInit();');
    }

    public function AdminDeleteCategory()
    {
        if (($iDelete = (int)$this->get('delete'))) {
            if (Phpfox::getService('ynblog.category')->getAllItemBelongToCategory($iDelete) > 0) {
                $this->alert(_p('you_can_not_delete_this_category_because_there_are_many_items_related_to_it'));
            } else {
                if (Phpfox::getService('ynblog.process')->deleteCategory($iDelete)) {
                    $this->alert(_p('successfully_deleted_the_category'));
                    $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');$(\'.apps_menu ul li:eq(2) a\').trigger(\'click\'); js_box_remove($(\'.js_box_close\'));},2000);');
                    $this->call('setTimeout(function(){ location.reload(); },1000);');
                }
            }
        }
    }
}
