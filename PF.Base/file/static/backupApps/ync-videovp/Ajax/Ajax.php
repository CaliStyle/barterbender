<?php
/**
 * User: YouNetCo
 * Date: 5/10/18
 * Time: 11:17 AM
 */

namespace Apps\YNC_VideoViewPop\Ajax;

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Plugin;

class Ajax extends Phpfox_Ajax
{
    public function view()
    {
        Phpfox::getComponent('yncvideovp.view', array(), 'controller');
    }

    public function approve()
    {
        $iVideoId = $this->get('video_id');
        $sVideoType = $this->get('video_type');

        if ($this->{'approve_' . $sVideoType}($iVideoId)) {
            $this->call('yncvideovp.refresh();');
            $this->alert(_p('video_has_been_approved'), _p('video_approved'), 300, 100, true);
        } else {
            $this->alert(_p('error'), _p('error'), 300, 100, true);
        }
    }

    public function approve_video($iVideoId)
    {
        return Phpfox::getService('v.process')->approve($iVideoId);
    }

    public function approve_ultimatevideo($iVideoId)
    {
        return Phpfox::getService('ultimatevideo.process')->approvedVideo($iVideoId, 1);
    }

    public function approve_videochannel($iVideoId)
    {
        return Phpfox::getService('videochannel.process')->approve($iVideoId);
    }

    public function feature()
    {
        $sVideoType = $this->get('video_type');
        $iVideoId = (int)$this->get('video_id');
        $type = $this->get('type');
        if (empty($sVideoType) || empty($iVideoId)) {
            return $this->alert(_p('error'), null, 300, 100, true);
        }

        $this->{'feature_' . $sVideoType}($iVideoId, $type);
    }

    public function feature_video($iVideoId, $type)
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('v.process')->feature($iVideoId, $type)) {
            $this->call('yncvideovp.refresh();');
            if ($type == '1') {
                $this->alert(_p('video_successfully_featured'), null, 300, 100, true);
            } else {
                $this->alert(_p('video_successfully_unfeatured'), null, 300, 100, true);
            }
        }
    }

    public function feature_ultimatevideo($iVideoId, $type)
    {
        $oProcess = Phpfox::getService('ultimatevideo.process');
        $sResult = null;

        if ($iVideoId) {
            $sResult = $oProcess->featureVideo($iVideoId, $type);
        }

        if (!$sResult) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_video'));
        } else {
            $this->call('yncvideovp.refresh();');
            if ($type == '1') {
                $this->alert(_p('video_successfully_featured'), null, 300, 100, true);
            } else {
                $this->alert(_p('video_successfully_unfeatured'), null, 300, 100, true);
            }
        }
    }

    public function feature_videochannel($iVideoId, $type)
    {
        if (Phpfox::getService('videochannel.process')->feature($iVideoId, $type)) {
            $this->call('yncvideovp.refresh();');
            if ($type == 1)
                $this->alert(_p('videochannel.featured_this_video_successfully'), 'Moderation', 300, 100, true);
            else
                $this->alert(_p('videochannel.un_featured_this_video_successfully'), 'Moderation', 300, 100, true);
        }
    }

    public function sponsor()
    {
        if (!Phpfox::isModule('ad')) {
            $this->alert('your_request_is_invalid');
        }
        if (Phpfox::getService('v.process')->sponsor($this->get('video_id'), $this->get('type'))) {
            $iVideoId = $this->get('video_id');
            $aVideo = Phpfox::getService('v.video')->getForEdit($iVideoId);
            if ($this->get('type') == '1') {
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'v',
                    'item_id' => $iVideoId,
                    'name' => _p('default_campaign_custom_name', ['module' => _p('video'), 'name' => $aVideo['title']])
                ));
                $this->call('yncvideovp.refresh();');
                $this->alert(_p('video_successfully_sponsored'), null, 300, 100, true);
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('v', $iVideoId);
                $this->call('yncvideovp.refresh();');
                $this->alert(_p('video_successfully_un_sponsored'), null, 300, 100, true);
            }
            Phpfox::getLib('cache')->removeGroup(['ad', 'betterads']);
        }
    }

    public function removeSponsor()
    {
        if (Phpfox::isModule('feed') && (Phpfox::getUserParam('feed.can_purchase_sponsor') || Phpfox::getUserParam('feed.can_sponsor_feed')) && ($iSponsorId = Phpfox::getService('feed')->canSponsoredInFeed($this->get('type_id'),
                $this->get('item_id')))) {
            if ($iSponsorId === true) {
                $this->alert(_p('Cannot find the feed!'), null, 300, 150, true);
                return;
            }
            if (Phpfox::getService('ad.process')->deleteSponsor($iSponsorId, true)) {
                $this->call('yncvideovp.refresh();');
                $this->alert(_p('This item in feed has been unsponsored successfully!'),
                    null, 300, 150, true);
            } else {
                $this->alert(_p('Cannot unsponsor this item in feed!'),
                    null, 300, 150, true);
                return;
            }

        } else {
            $this->alert(_p('Cannot unsponsor this item in feed!'));
            return;
        }
    }

    public function delete()
    {
        $iVideoId = $this->get('video_id');
        $sVideoType = $this->get('video_type');


        if ($this->{'delete_' . $sVideoType}($iVideoId)) {
            Phpfox::addMessage(_p('video_s_successfully_deleted'));
            $this->call('window.location.reload();');
        } else {
            $this->alert(_p('error'), _p('error'), 300, 100, true);
        }
    }

    public function delete_video($iVideoId)
    {
        if (!Phpfox::getService('v.video')->isAdminOfParentItem($iVideoId)) {
            if (!user('pf_video_delete_all_video', 0, null, false)) {
                return false;
            }
        }
        Phpfox::getService('v.process')->delete($iVideoId, '', 0, false);
        return true;
    }

    public function delete_ultimatevideo($iVideoId)
    {
        return Phpfox::getService('ultimatevideo.process')->deleteVideo($iVideoId);
    }

    public function delete_videochannel($iVideoId)
    {
        return Phpfox::getService('videochannel.process')->delete($iVideoId);
    }

    public function favorite_videochannel()
    {
        Phpfox::isUser(true);
        $iVideoId = $this->get('video_id');
        $iType = $this->get('type');
        $oService = Phpfox::getService('videochannel');
        $aCallback = null;

        $aVideo = $oService->getVideoSimple($iVideoId);
        if (!$aVideo) {
            $this->alert(_p('error'), _p('error'), 300, 100, true);
        }

        if ($iType) {
            if ($oService->addToFavorite('videochannel', $iVideoId)) {
                if (Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) {
                    Phpfox::getService('feed.process')->callback($aCallback)->add('videochannel_favourite', $iVideoId, $aVideo['privacy'], $aVideo['privacy_comment'], ($aCallback === null ? 0 : $aVideo['item_id']));
                }

                Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'videochannel');
                Phpfox::getService('notification.process')->add('videochannel_favourite', $aVideo['video_id'], $aVideo['user_id']);

                (($sPlugin = Phpfox_Plugin::get('videochannel.component_ajax_favorite_end')) ? eval($sPlugin) : false);

                $this->call('yncvideovp.updateFavoriteButton(1);');
                $this->alert(_p('videochannel.favourite_succeed'), null, 300, 100, true);
            }
        } else {
            if (Phpfox::getService('videochannel.process')->unfavouriteVideo($iVideoId)) {
                if (Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) {
                    Phpfox::getService('feed.process')->callback($aCallback)->add('videochannel_unfavourite', $iVideoId, $aVideo['privacy'], $aVideo['privacy_comment'], ($aCallback === null ? 0 : $aVideo['item_id']));
                }

                // Update user activity
                Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'videochannel');

                (($sPlugin = Phpfox_Plugin::get('videochannel.component_ajax_unfavorite_end')) ? eval($sPlugin) : false);

                $this->call('yncvideovp.updateFavoriteButton(0);');
                $this->alert(_p('videochannel.unfavourite_succeed'), null, 300, 100, true);
            }
        }
    }
}