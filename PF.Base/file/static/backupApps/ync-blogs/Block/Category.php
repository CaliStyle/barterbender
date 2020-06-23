<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class Category extends Phpfox_Component
{
    public function process()
    {
        $aParentId = ($this->request()->get('req2') == 'category' && $this->request()->get('req3') > 0) ? $this->request()->getInt('req3') : 0;
        $aCategories = Phpfox::getService('ynblog.category')->getForUsers($aParentId, false, true);

        if (empty($aCategories) || $this->request()->get('view') == 'my') {
            return false;
        }

        $this->template()
            ->assign([
                'sHeader' => $aParentId ? _p('sub_categories') : _p('Categories'),
                'aCategories' => $aCategories,
            ]);

        return 'block';
    }
}
