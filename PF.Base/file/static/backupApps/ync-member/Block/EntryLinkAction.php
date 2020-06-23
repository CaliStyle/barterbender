<?php

namespace Apps\YNC_Member\Block;

use \Phpfox;

class EntryLinkAction extends \Phpfox_Component
{
    public function process()
    {
        $aUser = $this->getParam('aUser');

        $this->template()->assign([
            'aUser' => $aUser,
        ]);

        return 'block';
    }
}