<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class SubscribeEventBlock extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $bInHomepage = $this->getParam('bInHomepage', false);
        $blockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $isSideLocation = Phpfox::getService('fevent.helper')->isSideLocation($blockLocation);

        if (!($bInHomepage || $isSideLocation)) {
            return false;
        }

        $aCategories = Phpfox::getService('fevent.category')->getForBrowse();
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        $this->template()->assign(array(
                'aEmail' => isset($aUser['email']) ? $aUser['email'] : '',
                'aCategories' => $aCategories,
                'sHeader' => _p('subscribe_event'),
                'sImageCheckin' => Phpfox::getParam('core.path') . '/module/fevent/static/image/checkin.png',
                'sCustomClassName' => 'p-block'
            )
        );
        return 'block';
    }
}