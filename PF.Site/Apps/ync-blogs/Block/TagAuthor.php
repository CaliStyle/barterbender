<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox;
use Phpfox_Component;

class TagAuthor extends Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::isModule('tag'))
            return false;

        $aTagAuthor = Phpfox::getService('ynblog.blog')->getTagBelongToAuthor(Phpfox::getUserId());

        if (empty($aTagAuthor) || $this->request()->get('view') != 'my') {
            return false;
        }

        $this->template()
            ->assign(array(
                'sHeader' => _p('Tags'),
                'aTagAuthor' => $aTagAuthor,
            ));

        return 'block';
    }
}
