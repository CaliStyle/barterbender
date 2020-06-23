<?php
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Feed extends Phpfox_Component {

    public function process() {
        if ($this_feed_id = $this->getParam('this_feed_id')) {
            $custom = $this->getParam('custom_param_' . $this_feed_id);
            $this->template()->assign([
                'aVideos' => $custom
            ]);
            $this->clearParam('this_feed_id');
        }
    }
}