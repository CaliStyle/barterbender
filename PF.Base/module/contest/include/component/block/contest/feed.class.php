<?php
defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Feed extends Phpfox_Component
{

    public function process()
    {
        if ($this_feed_id = $this->getParam('this_feed_id')) {
            $aItem = $this->getParam('custom_param_' . $this_feed_id);
            $this->template()->assign([
                'aItem' => $aItem,
            ]);
        }
    }
}