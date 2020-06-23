<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class FindEventBlock extends Phpfox_Component
{
    static $number = 0;

    public function process()
    {
        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('fevent.helper')->isSideLocation($sBlockLocation);

        if ($bIsSearch && !$bIsSideLocation) {
            return false;
        }

        $this->template()->assign([
            'sHeader' => _p('find_events'),
            'sCustomClassName' => 'p-block',
            'currentTime' => PHPFOX_TIME + (self::$number++)
        ]);

        return 'block';
    }
}