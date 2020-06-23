<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/14/16
 * Time: 19:25
 */
class Ynsocialstore_Component_Controller_Profile extends Phpfox_Component
{
    public function process()
    {
        $this->setParam('bIsProfile', true);

        Phpfox::getComponent('ynsocialstore.store.index', ['bNoTemplate' => true], 'controller');

    }
}