<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/12/16
 * Time: 10:34 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Search extends Phpfox_Component
{
    public function process()
    {

        $bIsAdvSearch = $this->request()->get('flag_advancedsearch');
        $aSearch = $this->getParam('aSearch');

        $sSearchAction = str_replace('?view=','',$this->search()->getFormUrl());
        $sFormUrl = $sSearchAction.'/view_'.$this->request()->get('view').'/bIsAdvSearch_true';

        $this->template()->assign(array(
            'bIsAdvSearch' => $bIsAdvSearch,
            'sCorePath' => Phpfox::getParam('core.path'),
            'sFormUrl' => $sFormUrl,
            'DMU' => Phpfox::getParam('ynsocialstore.default_distance_measurement_unit','mi'),
            'aSearch' => $aSearch,
        ));
    }
}