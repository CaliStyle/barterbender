<?php

namespace Apps\YNC_Blogs\Block;

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
        $selected = $this->getParam('iIdCategoryEdit');

        $aItems = Phpfox::getService('ynblog.category')->getForUsers(0,1,1);

        $this->template()->assign(array(
            'aItems' => $aItems,
            'iIdItemEdit' => $selected
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
