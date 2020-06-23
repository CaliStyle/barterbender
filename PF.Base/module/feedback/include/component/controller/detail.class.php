<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          3.01p1
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class FeedBack_Component_Controller_Detail extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getUserParam('feedback.can_view_feedback', true);
        if (isset($_SESSION['data_search'])) {
            unset($_SESSION['data_search']);
        }

        // Phpfox::isUser(true);
        $fb_id = $this->request()->get('feedback');
        $sTitle = $this->request()->get('req3');
        $core_url = Phpfox::getParam('core.path');

        if (!empty($fb_id)) {
            $feedback = Phpfox::getService('feedback')->getFeedBackDetailById($fb_id);
        } else {

            $feedback = Phpfox::getService('feedback')->getFeedBackDetailByAlias($sTitle);
        }

        if (empty($feedback)) {
            return Phpfox_Error::display(_p('feedback.feedback_not_found'));
        }
        $feedback['total_view'] = Phpfox::getService('feedback.process')->numberAbbreviation($feedback['total_view']);
        $feedback['total_comment'] = Phpfox::getService('feedback.process')->numberAbbreviation($feedback['total_comment']);
        $feedback['total_like'] = Phpfox::getService('feedback.process')->numberAbbreviation($feedback['total_like']);
        $feedback['total_vote'] = Phpfox::getService('feedback.process')->numberAbbreviation($feedback['total_vote']);

        if ($feedback['is_approved'] == 0) {
            if (((Phpfox::getUserId() == $feedback['user_id']) && Phpfox::getUserId()) || Phpfox::getUserParam('feedback.can_approve_feedbacks')) {
            } else {
                $this->url()->send('subscribe');
            }
        }

        $iPrivacy = in_array($feedback['privacy'], [2, 3]) ? 3 : 0;

        Phpfox::getService('privacy')->check('feedback', $feedback['feedback_id'], $feedback['user_id'], $iPrivacy, $feedback['is_friend']);

        if (Phpfox::isModule('track') && phpfox::isUser() && count($feedback) > 0 && !$feedback['is_viewed']) {
            Phpfox::getService('track.process')->add('feedback', $feedback['feedback_id']);
            Phpfox::getService('feedback.process')->updateView($feedback['feedback_id']);
        }
        if (!empty($feedback['category_name'])) {
            $sCategory = ' <a href="' . Phpfox::getLib('url')->makeUrl('feedback', array('category', $feedback['feedback_category_id'], $feedback['category_name'])) . '">' . $feedback['category_name'] . '</a>';
            $feedback['category_url'] = $sCategory;
        }

        $link = phpfox::getLib('url')->makeURL($feedback['user_name']);
        $time_stamp = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $feedback['time_stamp']);
        if (empty($feedback['full_name'])) {
            $feedback['info'] = _p('feedback.visitor_posted_feedback', array('time_stamp' => $time_stamp, 'visitor' => $feedback['visitor']));
        } else {

            $feedback['info'] = _p('feedback.posted_time_link_by_user', array('time_stamp' => $time_stamp, 'link' => $link, 'full_name' => $feedback['full_name']));
        }
        $aFilterMenu = array();
        if (Phpfox::getUserId()) {
            $iMyFeedbackTotal = Phpfox::getService('feedback')->getMyFeedbacksTotal();
        }
        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            if (Phpfox::getUserId() && $iMyFeedbackTotal > 0) {
                $aFilterMenu = array(
                    _p('feedback.all_feedbacks') => '',
                    _p('feedback.my_feedbacks').'<span class="my count-item">' . ($iMyFeedbackTotal > 99 ? '99+' : $iMyFeedbackTotal) . '</span>' => 'my'
                );
            }
            else {
                $aFilterMenu = array(
                    _p('feedback.all_feedbacks') => '',
                    _p('feedback.my_feedbacks').'<span class="my"></span>' => 'my'
                );
            }

            if (Phpfox::isModule('friend') && !Phpfox::getParam('core.friends_only_community')) {
                $aFilterMenu[_p('feedback.friend_feedbacks')] = 'friend';
            }

            if (Phpfox::getUserParam('feedback.can_approve_feedbacks')) {
                $iPendingTotal = Phpfox::getService('feedback')->getPendingTotal();
                if ($iPendingTotal) {
                    $aFilterMenu[_p('feedback.pending_feedbacks') . (Phpfox::getUserParam('feedback.can_approve_feedbacks') ? '<span class="pending count-item"> ' . $iPendingTotal . '</span>' : 0)] = 'pending';
                }
            }
        }
        $this->template()->buildSectionMenu('feedback', $aFilterMenu);
        $isVoted = Phpfox::getLib('database')->select('fv.*')
            ->from(Phpfox::getT('feedback_vote'), 'fv')
            ->where('fv.user_id = ' . Phpfox::getUserId() . ' and fv.feedback_id = ' . $feedback['feedback_id'])
            ->execute('getRow');
        if (empty($isVoted)) {
            $feedback['isVoted'] = false;
        } else {
            $feedback['isVoted'] = true;
        }
        $aSorts = array(
            'fb.date_modify' => _p('feedback.most_recent'),
            'fb.total_vote' => _p('feedback.most_voted'),
            'fb.total_view' => _p('feedback.most_viewed'),
            'fb.total_comment' => _p('feedback.most_comment'),
            'fb.is_featured' => _p('feedback.featured')
        );
        $sCats = phpfox::getService('feedback')->getFeedBackCat();
        $aTypes = $sCats;
        //	$aTypes['All'] = 'All';
        $aStatus = Phpfox::getService('feedback')->getFeedBackStatus();

        $aFilters = array(
            'type_cats' => array(
                'type' => 'select',
                'options' => $aTypes,
                'add_any' => true,
                'search' => "fb.feedback_category_id = [VALUE]"
            ),
            'type_status' => array(
                'type' => 'select',
                'options' => $aStatus,
                'add_any' => true,
                'search' => "fb.feedback_status_id = [VALUE]"
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'fb.time_stamp'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('core.descending'),
                    'ASC' => _p('core.ascending')
                ),
                'default' => 'DESC'
            ),
            'keyword' => array(
                'type' => 'input:text',
            )

        );

        Phpfox::getLib('search')->set(array(
                'type' => 'feedback',
                'filters' => $aFilters,
                'cache' => true,
                'field' => 'fb.feedback_id',
                'search' => 'keyword'
            )
        );
        //use to show social share
        $public_id_addthis = Phpfox::getParam('core.addthis_pub_id');
        $public_id_addthis = empty($public_id_addthis) ? 'younet' : $public_id_addthis;
        $sFormUrl = $this->url()->makeUrl('feedback');
        $bookmark_url = Phpfox::getLib('url')->makeUrl('feedback', array('detail', $feedback['title_url']));
        $login = phpfox::getLib('url')->makeURL('login');

        //support tag 
        if (Phpfox::isModule('tag')) {
            $aTags = Phpfox::getService('tag')->getTagsById('feedback',
                $feedback['feedback_id']);
            if (isset($aTags[$feedback['feedback_id']])) {
                $feedback['tag_list'] = $aTags[$feedback['feedback_id']];
            }
        }

        // Add tags to meta keywords
        if (!empty($feedback['tag_list']) && $feedback['tag_list'] && Phpfox::isModule('tag')) {
            $this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($feedback['tag_list']));
        }

        Phpfox::getService('feedback')->getFeedbackPermissions($feedback);

        $this->template()->setTitle($feedback['title'])
            ->setBreadCrumb(_p('feedback.feedbacks'), $this->url()->makeUrl('feedback'), false)
            ->setHeader('cache', array(
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                'jquery/plugin/imgnotes/jquery.tag.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'feed.js' => 'module_feed',
                'pager.css' => 'style_css',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'feedback.js' => 'module_feedback',
            ))
            ->setPhrase(array('are_you_sure_you_want_to_delete_this_photo', 'feedback_photo','description_of_feedback_cannot_be_empty','title_cannot_be_empty'))
            ->assign(array(
                'aFeedBack' => $feedback,
                'core_url' => $core_url,
                'link' => $link,
                'login' => $login,
                'full_name' => isset($feedback['full_name']) ? $feedback['full_name'] : $feedback['visitor'],
                'info' => $feedback['info'],
                'feedback_link' => phpfox::getLib('url')->makeUrl('feedback.detail', $feedback['title_url']),
                'core_path' => phpfox::getParam('core.path'),
                'sFormUrl' => $sFormUrl,
                'bFeedbackView' => true,
                'public_id_addthis' => $public_id_addthis,
                'sShareDescription' => str_replace(array("\n","\r","\r\n"),'', $feedback['feedback_description']),
                'sBookmarkUrl' => $bookmark_url
            ));

        // in fact that feed back doesn't have comment privacy , so we use user setting here for privacy comment
        $this->setParam('aFeed', array(
                'comment_type_id' => 'feedback',
                'privacy' => $iPrivacy,
                'comment_privacy' => (Phpfox::getUserParam('feedback.can_you_post_on_feedback') ? 0 : 3),
                'like_type_id' => 'feedback',
                'feed_is_liked' => $feedback['is_liked'],
                'feed_is_friend' => (isset($feedback['is_friend']) ? $feedback['is_friend'] : '0'),
                'item_id' => $feedback['feedback_id'],
                'user_id' => $feedback['user_id'],
                'total_comment' => $feedback['total_comment'],
                'total_like' => $feedback['total_like'],
                'feed_link' => Phpfox::permalink('feedback.detail', $feedback['title_url']),
                'feed_title' => $feedback['title'],
                'feed_display' => 'view',
                'feed_total_like' => $feedback['total_like'],
                'report_module' => 'feedback',
                'report_phrase' => _p('feedback.report_this_feedback')
            )
        );
        if (Phpfox::getUserId()) {
            $this->template()->setEditor(array(
                    'load' => 'simple',
                    'wysiwyg' => ((Phpfox::isModule('comment')))
                )
            );
        }
        if (Phpfox::getUserParam('feedback.can_approve_feedbacks'))
        {
            if ($feedback['is_approved'] != 1) {
                $aTitleLabel['label']['pending'] = [
                    'title' => '',
                    'title_class' => 'flag-style-arrow',
                    'icon_class' => 'clock-o'

                ];
                $aPendingItem = [
                    'message' => _p('feedback_is_pending_approval'),
                    'actions' => []
                ];
                if (Phpfox::getUserParam('feedback.can_approve_feedbacks')) {
                    $aPendingItem['actions']['approve'] = [
                        'is_ajax' => true,
                        'label' => _p('approve'),
                        'action' => '$.ajaxCall(\'feedback.approve\', \'inline=true&amp;id='.$feedback['feedback_id'].'\', \'POST\')'
                    ];
                }
                if ($feedback['user_id'] == Phpfox::getUserId()) {
                    $aPendingItem['actions']['edit'] = [
                        'is_ajax' => true,
                        'label' => _p('edit'),
                        'action' => '$Core.box(\'feedback.editFeedBack\', 500, \'feedback_id='.$feedback['feedback_id'].'\')',
                    ];
                }
                if ($feedback['user_id'] == Phpfox::getUserId()) {
                    $aPendingItem['actions']['delete'] = [
                        'is_confirm' => true,
                        'confirm_message' => _p('are_you_sure_you_want_to_delete_this_feedback_permanently'),
                        'label' => _p('delete'),
                        'action' => $this->url()->makeUrl('feedback/delete',['id' => $feedback['feedback_id']]),
                    ];
                }

                $this->template()->assign([
                    'aPendingItem' => $aPendingItem
                ]);
            }
        }

        $this->template()->assign([
            'bCanApprove' => Phpfox::getUserParam('feedback.can_approve_feedbacks')
        ]);

    }

}

?>
