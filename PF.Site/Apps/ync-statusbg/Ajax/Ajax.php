<?php

namespace Apps\YNC_StatusBg\Ajax;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Plugin;
use Phpfox_Url;

class Ajax extends Phpfox_Ajax
{
    public function getTotalActiveCollection()
    {
        $iId = $this->get('id', 0);
        $iTotal = Phpfox::getService('yncstatusbg')->countTotalActiveCollection($iId);
        echo json_encode([
            'total_active' => $iTotal,
        ]);
        exit;
    }

    public function refreshBackgrounds()
    {
        $iId = $this->get('id');
        if (!$iId) {
            return false;
        }
        $aBackgrounds = Phpfox::getService('yncstatusbg')->getImagesByCollection($iId);
        $this->template()->assign([
            'aBackgrounds' => $aBackgrounds,
        ])->getTemplate('yncstatusbg.block.admin.list-backgrounds');
        $this->call('$(\'#js_list_backgrounds\').html(\'' . $this->getContent() . '\');');
        $this->call('$Core.loadInit();');
        return true;
    }

    public function toggleActiveCollection()
    {
        $iId = $this->get('id');
        $iActive = $this->get('active');
        $bResult = Phpfox::getService('yncstatusbg.process')->toggleActiveCollection($iId, $iActive);
        if (!$bResult) {
            $this->call('setTimeout(function(){window.location.reload();},2000);');
        }
    }

    public function updateImagesOrdering()
    {
        $aVals = $this->get('val');
        $iSetId = $this->get('collection_id');
        Phpfox::getService('yncstatusbg.process')->updateImagesOrdering(array('values' => $aVals['ordering']), $iSetId);
    }

    public function deleteBackground()
    {
        $iId = $this->get('id');
        if (!$iId) {
            return false;
        }
        if (Phpfox::getService('yncstatusbg.process')->deleteBackground($iId)) {
            Phpfox::addMessage(_p('image_deleted_successfully'));
            $this->call('window.location.reload();');
        }
    }

    public function loadCollectionsList()
    {
        Phpfox::getBlock('yncstatusbg.collections-list');
        $this->call('yncstatusbg.appendCollectionList(\'' . $this->getContent() . '\')');
    }

    public function editStatusBackground()
    {
        $iFeedId = $this->get('feed_id');
        $iDisabled = (int)$this->get('is_disabled');
        $aCallback = [];
        if ($sModule = $this->get('module')) {
            $aCallback = [
                'module' => $sModule,
                'table_prefix' => $sModule . '_',
                'item_id' => $this->get('item_id'),
            ];
        }
        if ($this->get('url_ajax') == 'feed.updatePost') {
            $aFeed = db()->select('type_id, user_id, item_id')->from(':feed')->where(['feed_id' => $iFeedId])->executeRow();
        } else {
            if (Phpfox::isModule('ynfeed') && !$this->get('item_id') && !$this->get('module')) {
                $aFeed = Phpfox::getService('ynfeed')->get(null, $iFeedId);
                if (count($aFeed)) {
                    $aFeed = $aFeed[0];
                }
            } else {
                $aFeed = Phpfox::getService('feed')->getUserStatusFeed($aCallback, $iFeedId, false);
            }
        }
        if ($aFeed) {
            Phpfox::getService('yncstatusbg.process')->editUserStatusCheck($aFeed['item_id'], $aFeed['type_id'],
                $aFeed['user_id'], !$iDisabled);
        }
    }

    public function updateStatus()
    {
        Phpfox::isUser(true);
        $aVals = (array)$this->get('val');
        if (isset($aVals['user_status']) && ($iId = Phpfox::getService('yncstatusbg.process')->updateStatus($aVals))) {
            if (isset($aVals['feed_id'])) {
                //Mean edit already status
                Phpfox::getService('feed')->processUpdateAjax($aVals['feed_id']);
            } else {
                //Mean add new status
                (($sPlugin = Phpfox_Plugin::get('user.component_ajax_updatestatus')) ? eval($sPlugin) : false);
                Phpfox::getService('feed')->processAjax($iId);
            }
        } else {
            $this->call('$Core.activityFeedProcess(false);');
        }
    }

    public function addComment()
    {
        Phpfox::isUser(true);

        $aVals = (array) $this->get('val');
        if (isset($aVals['status_background_id'])) {
            $iBackgroundId = $aVals['status_background_id'];
        } else {
            $iBackgroundId = 0;
        }

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status']))
        {
            $this->alert(_p('add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return false;
        }

        if (isset($aVals['parent_user_id']) && $aVals['parent_user_id'] > 0 && !($aVals['parent_user_id'] == Phpfox::getUserId() || (Phpfox::getUserParam('profile.can_post_comment_on_profile') && Phpfox::getService('user.privacy')->hasAccess('' . $aVals['parent_user_id'] . '', 'feed.share_on_wall')))) {
            $this->alert(_p('You don\'t have permission to post comment on this profile.'));
            $this->call('$Core.activityFeedProcess(false);');
            return false;
        }

        /* Check if user chose an egift */
        if (Phpfox::isModule('egift') && isset($aVals['egift_id']) && !empty($aVals['egift_id']))
        {
            /* is this gift a free one? */
            $aGift = Phpfox::getService('egift')->getEgift($aVals['egift_id']);
            if (!empty($aGift))
            {
                $bIsFree = true;
                foreach ($aGift['price'] as $sCurrency => $fVal)
                {
                    if ($fVal > 0)
                    {
                        $bIsFree = false;
                    }
                }
                /* This is an important change, in v2 birthday_id was the mail_id, in v3
                 * birthday_id is the feed_id
                */
                $aVals['feed_type'] = 'feed_egift';
                $iId = Phpfox::getService('feed.process')->addComment($aVals);
                if ($iId && $iBackgroundId) {
                    $iStatusId = db()->select('item_id')->from(':feed')->where('feed_id = ' . (int)$iId)->execute('getField');
                    Phpfox::getService('yncstatusbg.process')->addBackgroundForStatus('feed_comment',
                        $iStatusId, $iBackgroundId, Phpfox::getUserId(), 'feed');
                }
                // Always make an invoice, so the feed can check on the state
                $aGift['message'] = Phpfox::getLib('parse.input')->prepare($aVals['user_status']);
                $iInvoice = Phpfox::getService('egift.process')->addInvoice($iId, $aVals['parent_user_id'], $aGift);

                if (!$bIsFree)
                {
                    Phpfox::getBlock('api.gateway.form', [
                        'gateway_data' => [
                            'item_number'                => 'egift|' . $iInvoice,
                            'currency_code'              => Phpfox::getService('user')->getCurrency(),
                            'amount'                     => $aGift['price'][Phpfox::getService('user')->getCurrency()],
                            'item_name'                  => _p('egift_card_with_message') . ': ' . $aVals['user_status'] . '',
                            'return'                     => Phpfox_Url::instance()->makeUrl('friend.invoice'),
                            'recurring'                  => 0,
                            'recurring_cost'             => '',
                            'alternative_cost'           => 0,
                            'alternative_recurring_cost' => 0
                        ]
                    ]);
                    $this->call('$("#js_activity_feed_form").hide().after("' . $this->getContent(true) . '");');
                }
                else
                {
                    //send notification
                    $aInvoice = Phpfox::getService('egift')->getEgiftInvoice((int)$iInvoice);
                    Phpfox::getService('egift.process')->sendNotification($aInvoice);

                    // egift is free
                    Phpfox::getService('feed')->processAjax($iId);

                }
            }

        }
        else
        {
            if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->addComment($aVals)))
            {
                if ($iBackgroundId) {
                    $iStatusId = db()->select('item_id')->from(':feed')->where('feed_id = ' . (int)$iId)->execute('getField');
                    Phpfox::getService('yncstatusbg.process')->addBackgroundForStatus('feed_comment',
                        $iStatusId, $iBackgroundId, Phpfox::getUserId(), 'feed');
                }
                if (isset($aVals['feed_id'])) {
                    $this->call("$('#js_item_feed_$aVals[feed_id]').find('div.activity_feed_content_status').text('$aVals[user_status]');");
                    $this->call('tb_remove();');
                    $this->call('setTimeout(function(){$Core.resetActivityFeedForm();$Core.loadInit();}, 500);');
                } else {
                    Phpfox::getService('feed')->processAjax($iId);
                }
            }
            else
            {
                $this->call('$Core.activityFeedProcess(false);');
            }
        }

    }

    public function addCommentWithAdvFeed()
    {
        Phpfox::isUser(true);

        $aVals = (array)$this->get('val');
        if (isset($aVals['status_background_id'])) {
            $iBackgroundId = $aVals['status_background_id'];
        } else {
            $iBackgroundId = 0;
        }

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            $this->alert(_p('add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return false;
        }

        if (isset($aVals['parent_user_id']) && $aVals['parent_user_id'] > 0 && !($aVals['parent_user_id'] == Phpfox::getUserId() || (Phpfox::getUserParam('profile.can_post_comment_on_profile') && Phpfox::getService('user.privacy')->hasAccess('' . $aVals['parent_user_id'] . '',
                        'feed.share_on_wall')))
        ) {
            $this->alert(_p('You don\'t have permission to post comment on this profile.'));
            $this->call('$Core.activityFeedProcess(false);');
            return false;
        }

        /* Check if user chose an egift */
        if (Phpfox::isModule('egift') && isset($aVals['egift_id']) && !empty($aVals['egift_id'])) {
            /* is this gift a free one? */
            $aGift = Phpfox::getService('egift')->getEgift($aVals['egift_id']);
            if (!empty($aGift)) {
                $bIsFree = true;
                foreach ($aGift['price'] as $sCurrency => $fVal) {
                    if ($fVal > 0) {
                        $bIsFree = false;
                    }
                }
                /* This is an important change, in v2 birthday_id was the mail_id, in v3
                 * birthday_id is the feed_id
                */
                $aVals['feed_type'] = 'feed_egift';
                $iId = Phpfox::getService('ynfeed.process')->addComment($aVals);
                if ($iId && $iBackgroundId) {
                    $iStatusId = db()->select('item_id')->from(':feed')->where('feed_id = ' . (int)$iId)->execute('getField');
                    Phpfox::getService('yncstatusbg.process')->addBackgroundForStatus('feed_comment',
                        $iStatusId, $iBackgroundId, Phpfox::getUserId(), 'feed');
                }
                $aVals['type_id'] = 'feed_comment';
                $aFeed = Phpfox::getService('ynfeed')->getFeed($iId);
                Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, $aFeed));
                /* Notify tagged users */

                // Always make an invoice, so the feed can check on the state
                $iInvoice = Phpfox::getService('egift.process')->addInvoice($iId, $aVals['parent_user_id'], $aGift);

                if (!$bIsFree) {
                    Phpfox::getBlock('api.gateway.form', [
                        'gateway_data' => [
                            'item_number' => 'egift|' . $iInvoice,
                            'currency_code' => Phpfox::getService('user')->getCurrency(),
                            'amount' => $aGift['price'][Phpfox::getService('user')->getCurrency()],
                            'item_name' => _p('egift_card_with_message') . ': ' . $aVals['user_status'] . '',
                            'return' => Phpfox_Url::instance()->makeUrl('friend.invoice'),
                            'recurring' => 0,
                            'recurring_cost' => '',
                            'alternative_cost' => 0,
                            'alternative_recurring_cost' => 0
                        ]
                    ]);
                    $this->call('$("#js_activity_feed_form").hide().after("' . $this->getContent(true) . '");');
                } else {
                    // egift is free
                    Phpfox::getService('ynfeed')->processAjax($iId);
                }
            }

        } else {
            if (isset($aVals['user_status']) && ($iId = Phpfox::getService('ynfeed.process')->addComment($aVals))) {
                if ($iBackgroundId) {
                    $iStatusId = db()->select('item_id')->from(':feed')->where('feed_id = ' . (int)$iId)->execute('getField');
                    Phpfox::getService('yncstatusbg.process')->addBackgroundForStatus('feed_comment',
                        $iStatusId, $iBackgroundId, Phpfox::getUserId(), 'feed');
                }
                $aVals['type_id'] = 'feed_comment';
                Phpfox::getService('ynfeed')->processAjax($iId);
            } else {
                $this->call('$Core.activityFeedProcess(false);');
            }
        }
    }
}