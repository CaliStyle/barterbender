<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/11/16
 * Time: 5:10 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_CoverUpload extends Phpfox_Component
{
    public function process()
    {
        if (($iStoreId = $this->request()->get('store_id')))
        {
            $this->template()->assign(array(
                'iStoreId' => $iStoreId
            ));
        }
    }
}