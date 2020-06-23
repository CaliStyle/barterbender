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
            if(!empty($custom)){
                $aItem = $custom[0];
                $aItem['image_url'] = Phpfox::getService('ynblog.helper')->getImagePath($aItem['image_path'], $aItem['server_id'], '_500', $aItem['is_old_suffix'], true);
                $this->template()->assign([
                    'aBlog' => $aItem,
                    'sCategory' => $custom['aCategory'][0],
                    'sLink' => $custom['sLink'],
                    'appPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs',
                ]);
            }

            $this->clearParam('custom_param_blog_' . $this_feed_id);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('ynblog.component_block_feed_clean')) ? eval($sPlugin) : false);
    }
}
