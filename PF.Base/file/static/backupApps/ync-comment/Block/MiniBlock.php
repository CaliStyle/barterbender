<?php

namespace Apps\YNC_Comment\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox_Component;

class MiniBlock extends Phpfox_Component
{
    public function process()
    {
        if (($aChildComments = $this->getParam('comment_custom'))) {
            $this->template()->assign(array(
                    'aComment' => $aChildComments,
                    'bNotMoreNestedComments' => false
                )
            );
        }
        return 'block';
    }
}