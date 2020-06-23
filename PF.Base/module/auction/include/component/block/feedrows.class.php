<?php

defined('PHPFOX') or exit('NO DICE!');


class Auction_Component_Block_feedrows extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($this_feed_id = $this->getParam('this_feed_id')) {
            $custom = $this->getParam('custom_param_' . $this_feed_id);

            $this->template()->assign([
                'aAuction' => $custom,
            ]);
        }
    }

}