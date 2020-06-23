<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Transferowner extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $this->template()->assign(array(
                'iBusinessId'        => $this->getParam('iBusinessId'),
                'frontend'        => $this->getParam('frontend'),
                'sCorePath'        => Phpfox::getParam('core.path'),
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
    }

}

?>