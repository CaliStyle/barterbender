<?php

namespace Apps\YNC_Blogs\Block\Admin;

use Phpfox_Component;
use Phpfox;

class ImportChooseCategory extends Phpfox_Component
{
    public function process()
    {
        $iBlogId = $this->getParam('iBlogId');
        $aBlogId = $this->getParam('aBlogId');

        $this->template()->assign(array(
                'iBlogId' => $iBlogId,
                'sBlogId' => !empty($aBlogId) ? implode(',', $aBlogId) : null,
                'aCategories' => Phpfox::getService('ynblog.category')->getForAdmin(),
            )
        );
        return 'block';
    }
}
