<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/6/16
 * Time: 4:39 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_DetailPolicy extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aStore = $this->getParam('aStore');

        if (empty($aStore)) {
            return false;
        }

        $this->template()->assign(array(
                'sPolicy'	=> $aStore['return_policy'],
            )
        );
    }
}