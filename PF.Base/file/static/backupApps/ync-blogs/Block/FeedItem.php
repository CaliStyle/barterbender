<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class FeedItem extends Phpfox_Component
{
    public function process()
    {
        if ($this_feed_id = $this->getParam('this_feed_id')) {
            $custom = $this->getParam('custom_param_' . $this_feed_id);
            $this->template()->assign([
                'aBlog' => $custom,
                'appPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs',
            ]);
        }
    }
}
