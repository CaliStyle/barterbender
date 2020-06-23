<?php

defined('PHPFOX') or exit('NO DICE!');

class Fevent_Component_Block_Subscribe_Event extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {

        $aCategories = Phpfox::getService('fevent.category')->getForBrowse();
        //die(d($aCategories));
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        $this->template()->assign(array(
                'aEmail' => isset($aUser['email']) ? $aUser['email'] : '',
                'aCategories' => $aCategories,
                'sHeader' => _p('subscribe_event'),
                'sImageCheckin' => Phpfox::getParam('core.path') . '/module/fevent/static/image/checkin.png',
                'sCustomClassName' => 'ync-block'
            )
        );
        return 'block';
    }

}

?>