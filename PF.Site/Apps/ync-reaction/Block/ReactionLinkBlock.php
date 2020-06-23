<?php

namespace Apps\YNC_Reaction\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;

class ReactionLinkBlock extends Phpfox_Component
{
    public function process()
    {
        $sModule = $sItemTypeId = Phpfox_Module::instance()->getModuleName();

        if ($sModule == 'apps' && Phpfox::isModule('pages')) {
            $sModule = 'pages';
        }
        if ($sModule == 'core') {
            $sModule = $this->getParam('like_type_id');
            $sModule = explode('_', $sModule);
            $sModule = $sModule[0];
        } else {
            if ($sModule == 'profile') {
                $sModule = $sItemTypeId = $this->getParam('like_type_id');
                $sModule = explode('_', $sModule);
                $sModule = $sModule[0];
            } else {
                if ($sModule == 'profile' && ($this->getParam('like_type_id') == 'feed_comment' || $this->getParam('like_type_id') == 'feed_mini')) {
                    $sModule = 'feed';
                }
            }
        }

        if (!$this->getParam('aFeed') && ($aVals = $this->request()->getArray('val')) && isset($aVals['is_via_feed'])) {
            $this->template()->assign(array('aFeed' => array('feed_id' => $aVals['is_via_feed'])));
        }

        if ($iOwnerId = $this->getParam('like_owner_id', null)) {
            if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $iOwnerId)) {
                return false;
            }
        }
        $sType = $this->getParam('like_type_id');
        $iItemId = $this->getParam('like_item_id');
        $bIsLike = $this->getParam('like_is_liked');
        //Get reaction detail
        $aReacted = [];
        if ($bIsLike) {
            $aReacted = Phpfox::getService('yncreaction')->getReactedDetail($iItemId, $sType, Phpfox::getUserId());
        }
        $this->template()->assign(array(
                'sParentModuleName' => $sModule,
                'aLike' => array(
                    'like_type_id' => $sType,
                    'like_item_id' => $iItemId,
                    'like_is_liked' => $bIsLike,
                    'like_is_custom' => $this->getParam('like_is_custom')
                ),
                'aUserReacted' => $aReacted
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncreaction.component_block_reaction_link_clean')) ? eval($sPlugin) : false);

        $this->template()->clean('aLike');
    }
}