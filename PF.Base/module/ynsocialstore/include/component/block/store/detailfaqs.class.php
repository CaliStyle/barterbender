<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/14/16
 * Time: 9:19 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_DetailFAQs extends Phpfox_Component
{
    public function process()
    {
        $aStore = $this->getParam('aStore');

        if (empty($aStore)) {
            return false;
        }

        $this->template()->assign(array(
            'aFAQs' => $aStore['FAQs'],
        ));

    }
}