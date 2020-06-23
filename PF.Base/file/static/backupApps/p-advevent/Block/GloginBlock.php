<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class GloginBlock extends Phpfox_Component
{
    public function process()
    {
        $event_id = $this->request()->get('id');
        $sCorePath = Phpfox::getParam('core.path_actual') ;
        $sCorePath = str_replace("index.php".PHPFOX_DS,"",$sCorePath);
        $sCorePath .= 'PF.Site'.PHPFOX_DS;
        $this->template()->assign(array(
            'core_path' => $sCorePath,
            'event_id' => $event_id
        ));
    }
}