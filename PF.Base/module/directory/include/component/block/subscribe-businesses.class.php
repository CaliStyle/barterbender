<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Subscribe_Businesses extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {

        $aCategories = Phpfox::getService('directory.category')->getForBrowse();

        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(),true);

        $this->template()->assign(array(
                'aEmail'    => isset($aUser['email'])?$aUser['email']:'',
                'aCategories' => $aCategories,
                'sHeader' => _p('directory.subscribe_businesses'),
                'sCustomClassName' => 'ync-block'
            )
        );
        return 'block';
    }

}

?>