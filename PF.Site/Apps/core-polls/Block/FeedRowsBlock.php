<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Polls\Block;

use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class FeedRowsBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($this_feed_id = $this->getParam('this_feed_id')) {
            $custom = $this->getParam('custom_param_' . $this_feed_id);
            $iSponsorFeedId = $this->getParam('sponsor_feed_id');
            $iVotedByUser = 0;
            foreach($custom['answer'] as $aAnswer)
            {
                if($aAnswer['voted'])
                {
                    $iVotedByUser++;
                }
            }
            $this->template()->assign([
                'aPoll' => $custom,
                'bIsSponsorFeed' => (int)$iSponsorFeedId === (int)$this_feed_id,
                'iSponsorId' => \Phpfox::isAppActive('Core_BetterAds') && ((int)$iSponsorFeedId === (int)$this_feed_id) ? \Phpfox::getService('ad.get')->getFeedSponsors($this_feed_id) : 0,
                'iVotedByUser' => $iVotedByUser
            ]);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_feed_rows')) ? eval($sPlugin) : false);
    }
}