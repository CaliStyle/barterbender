<?php

namespace Apps\YNC_Member\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Pager;
use Core\Route\Controller;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;

class ReviewViewController extends Phpfox_Component
{
    public function process()
    {
        if ($this->request()->getInt('id'))
        {
            return Phpfox_Module::instance()->setController('error.404');
        }

        (($sPlugin = Phpfox_Plugin::get('ynmember.component_controller_view_review_process_start')) ? eval($sPlugin) : false);

        $aItem = Phpfox::getService('ynmember.review')->getReview($this->request()->getInt('req3'));

        if (empty($aItem['review_id']))
        {
            return Phpfox_Error::display(_p('Review Not Found'));
        }

        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aItem['user_id']))
        {
            return Phpfox_Module::instance()->setController('error.invalid');
        }

        if (Phpfox::isModule('privacy'))
        {
            Phpfox::getService('privacy')->check('ynmember_review', $aItem['review_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend']);
        }

        $aFilterMenu = [
            _p('All Members') => '',
            _p('My Friends') => 'my',
            _p('Featured Members') => 'featured',
            _p('Members Rating & Review') => 'ynmember.review',
            _p('Members Birthday') => 'ynmember.birthday',
        ];
        $this->template()->buildSectionMenu('ynmember', $aFilterMenu);

        $aUser = Phpfox::getService('user')->get($aItem['item_id']);
        $this->template()->setBreadCrumb(_p('Review For ') . $aUser['full_name'], $aUser['user_name']);

        // comment block
        $this->setParam('aFeed', array(
                'comment_type_id' => 'ynmember_review',
                'privacy' => $aItem['privacy'],
                'comment_privacy' => $aItem['privacy_comment'],
                'like_type_id' => 'ynmember_review',
                'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
                'feed_is_friend' => $aItem['is_friend'],
                'item_id' => $aItem['review_id'],
                'user_id' => $aItem['user_id'],
                'total_comment' => $aItem['total_comment'],
                'total_like' => $aItem['total_like'],
                'feed_link' => $aItem['bookmark_url'],
                'feed_title' => $aItem['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aItem['total_like'],
                'report_module' => 'ynmember_review',
                'report_phrase' => _p('Report this review'),
                'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aItem['time_stamp'])
            )
        );

        $this->template()
            ->assign(array(
                    'aItem' => $aItem,
                )
            );

    }

    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('ynmember.component_controller_review_clean')) ? eval($sPlugin) : false);
    }
}
