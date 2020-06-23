<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/6/16
 * Time: 10:55 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_DetailAboutus extends Phpfox_Component
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

        $aStore['main_address'] = (count($aStore['address']) > 0) ? $aStore['address'][0] : null;

        $this->template()->assign(array(
                'aAboutUs'	=> $aStore,
                'apiKey' => Phpfox::getParam('core.google_api_key'),
                'sHeader' => '',
            )
        );
        return 'block';
    }
}