<?php

namespace Apps\YNC_Member\Block;

use \Phpfox;

class RecommendedFriends extends \Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::isModule('suggestion'))
            return false;
    }
}

