<?php

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class AddCategoryList extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $selected = $this->getParam('aSelectedCategories', array());

        $aItems = Phpfox::getService('ultimatevideo.category')->getForUsers(0, 1, 1, 0, $selected);

        $this->template()->assign(array(
            'aItems' => $aItems
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_block_add_category_list_clean')) ? eval($sPlugin) : false);
    }
}
