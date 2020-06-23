<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class FeedBack_Component_Controller_Index extends Phpfox_Component {

    public function process() {
        Phpfox::getUserParam('feedback.can_view_feedback', true);
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }

        $bIsYounetTheme = false;
        $sThemeName = phpfox::getLib('database')->select('name')
                ->from(phpfox::getT('theme_style'))
                ->where('is_default = 1')
                ->execute('getSlaveField');
        if (isset($sThemeName) && $sThemeName == 'YouLite') {
            $bIsYounetTheme = true;
        }

        $sView = $this->request()->get('view');
        $sStatus = $this->request()->get('status');
        $core_url = phpfox::getParam('core.path');
        $aFeedbackStatus = Phpfox::getService('feedback')->getStatusIndex();

        $aStatusParam = array();
        $aStatusParam[] = array('link' => '', 'phrase' => _p('feedback.all'));
        foreach ($aFeedbackStatus as $aValue => $iTems) {
            $aStatusParam[] = array('link' => $iTems['status_id'], 'phrase' => $iTems['name']);
        }
        $link = Phpfox::getLib('url')->makeURL('feedback');
        $this->template()->setPhrase(array('are_you_sure_you_want_to_delete_these_feedbacks', 'no_feedback_selected_to_delete', 'are_you_sure','description_of_feedback_cannot_be_empty','title_cannot_be_empty'));
        $this->template()->setBreadcrumb(_p('feedback.feedbacks'), $link);
        $this->search()->set(array(
            'type' => 'feedback',

            'field' => 'fb.feedback_id',
            'search_tool' => array(
                'table_alias' => 'fb',
                'search' => array(
                    'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('feedback', 'view' => $this->request()->get('feedback'))) : $this->url()->makeUrl('feedback', array('view' => $this->request()->get('view')))),
                    'default_value' => _p('feedback.search_feedback_dot'),
                    'name' => 'search',
                    'field' => 'fb.title',
                ),
                'sort' => array(
                    'latest' => array('fb.time_stamp', _p('feedback.latest')),
                    'most-viewed' => array('fb.total_view', _p('feedback.most_viewed')),
                    'most-liked' => array('fb.total_like', _p('feedback.most_like')),
                    'most-talked' => array('fb.total_comment', _p('feedback.most_discussed'))
                ),
                'show' => array(5, 10, 15),
                'custom_filters' => array(
                    'Status' => array(
                        'default_phrase' => 'All',
                        'param' => 'status',
                        'data' => $aStatusParam
                    )
                )
            )
                )
        );

        $aParentModule = $this->getParam('aParentModule');
        $aBrowseParams = array(
            'module_id' => 'feedback',
            'alias' => 'fb',
            'field' => 'feedback_id',
            'table' => Phpfox::getT('feedback'),
            'hide_view' => array('pending', 'my')
        );
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
                $aFilterMenu[_p('feedback.friends_feedbacks')] = 'friend';
            }

            if (Phpfox::getUserParam('feedback.can_approve_feedbacks')) {
                $iPendingTotal = Phpfox::getService('feedback')->getPendingTotal();
                if ($iPendingTotal) {
                    $aFilterMenu[_p('feedback.pending_feedbacks') . (Phpfox::getUserParam('feedback.can_approve_feedbacks') ? '<span class="pending count-item"> ' . $iPendingTotal . '</span>' : 0)] = 'pending';
                }
            }
        }
        $this->template()->buildSectionMenu('feedback', $aFilterMenu);
        if (isset($sStatus) && $sStatus != '') {
            $this->search()->setCondition(' AND fb.feedback_status_id = ' . $sStatus);
        }
        switch ($sView) {
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition(' AND fb.user_id = ' . Phpfox::getUserId());
                break;
            case 'pending':
                Phpfox::isUser(true);
                if (Phpfox::getUserParam('feedback.can_approve_feedbacks')) {
                    $this->search()->setCondition('AND fb.is_approved = 0');
                } else {
                    $this->url()->send('subscribe');
                }
                break;
            default:
                if ($bIsProfile === true) {
                    $this->search()->setCondition("AND fb.user_id = " . $aUser['user_id'] . " AND fb.is_approved IN(" . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '1') . ")");                    
                } else {
                     $this->search()->setCondition(" AND fb.is_approved = 1 AND fb.privacy = 1 ");
                }
                break;
        }

        if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category') {
            if ($aFeedBackCategory = Phpfox::getService('feedback')->getFeedBackCatForEdit($this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')))) {
                $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aFeedBackCategory['name']) ? _p($aFeedBackCategory['name']) : $aFeedBackCategory['name']), null,false);
                $this->search()->setCondition('AND fb.feedback_category_id = ' . $this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')));
                $this->template()->setTitle(Phpfox::getLib('locale')->convert($aFeedBackCategory['name']));
                $this->search()->setFormUrl($this->url()->permalink(array('feedback.category', 'view' => $this->request()->get('view')), $aFeedBackCategory['category_id'], $aFeedBackCategory['name']));
            }
        }

        //support tag
        $bIsTagSearch = false;
        if ($this->request()->get('req2') == 'tag' && $this->request()->get('req3')) {
            $bIsTagSearch = true;
        }
        if($bIsTagSearch){
            $tag_url = Phpfox_Database::instance()->escape($this->request()->get('req3'));
            $replaces = array('+' => ' ');
            $tag_url = strtr($tag_url, $replaces);
            $this->search()->setCondition(" AND tag.tag_url = '" . $tag_url . "'");
        }

        $aFeedBacks = Phpfox::getService('feedback')->execute($aBrowseParams);

        foreach ($aFeedBacks as $iKey => $aItem) {
            $aFeedBacks[$iKey] = Phpfox::getService('feedback')->getFeedBackDetailById($aFeedBacks[$iKey]['feedback_id']);
            $aFeedBacks[$iKey]['total_view'] = Phpfox::getService('feedback.process')->numberAbbreviation($aItem['total_view']);
            $aFeedBacks[$iKey]['total_comment'] = Phpfox::getService('feedback.process')->numberAbbreviation($aItem['total_comment']);
            $aFeedBacks[$iKey]['total_like'] = Phpfox::getService('feedback.process')->numberAbbreviation($aItem['total_like']);
            $aFeedBacks[$iKey]['total_vote'] = Phpfox::getService('feedback.process')->numberAbbreviation($aItem['total_vote']);
            Phpfox::getService('feedback')->getFeedbackPermissions($aFeedBacks[$iKey]);
        }

        $bWidth = false;
        if ($sView != '') {
            $bWidth = true;
        }
        $iPage = $this->search()->getPage();
        Phpfox::getService('feedback')->getExtra($aFeedBacks);
        
        $this->template()
                ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'pager.css' => 'style_css',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'feedback.js' => 'module_feedback',
                    'feed.js' => 'module_feed',
                    'country.js' => 'module_core',
                ))
                ->setTitle(_p('feedback.feedback'))
                ->assign(array(
                    'iCnt' => $this->search()->browse()->getCount(),
                    'core_url' => $core_url,
                    'core_path' => $core_url,
                    'aFeedBacks' => $aFeedBacks,
                    'sView' => $sView,
                    'bWidth' => $bWidth,
                    'bFeedbackView' => false,
                    'bIsYounetTheme' => $bIsYounetTheme,
                    'iPage' => $iPage,
                ));
        if ($sView == 'pending') {
            $this->setParam('global_moderation', array(
                'name' => 'feedback',
                'ajax' => 'feedback.moderation',
                'menu' => array(
                    array(
                        'phrase' => _p('feedback.delete'),
                        'action' => 'delete'
                    ),
                    array(
                        'phrase' => _p('feedback.approve'),
                        'action' => 'approve'
                    )
                )
                    )
            );
        } else {
            $this->setParam('global_moderation', array(
                'name' => 'feedback',
                'ajax' => 'feedback.moderation',
                'menu' => array(
                    array(
                        'phrase' => _p('feedback.delete'),
                        'action' => 'delete'
                    ),
                )
                    )
            );
        }
        if (Phpfox::getUserId()) {
            $this->template()->setEditor(array(
                'load' => 'simple'
            ));
        }
        //set paging mode
        $this->search()->browse()
            ->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('feedback.feedback_paging_mode', 'loadmore'))
            ->execute();
        if ($sView == 'pending') {
            $aPager = array(
                'page' => $this->search()->getPage(),
                'size' => $this->search()->getDisplay(),
                'count' => Phpfox::getService('feedback')->getCount(),
                'paging_mode' => $this->search()->browse()->getPagingMode()
            );
        }
        else {
            $aPager = array(
                'page' => $this->search()->getPage(),
                'size' => $this->search()->getDisplay(),
                'count' => $this->search()->browse()->getCount(),
                'paging_mode' => $this->search()->browse()->getPagingMode()
            );
        }
        \Phpfox_Pager::instance()->set($aPager);
    }

}

?>
