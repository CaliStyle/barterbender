<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/18/16
 * Time: 5:53 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Featured extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $hideBlock = $this->getParam('hideBlock', false);
        if($hideBlock) {
            return false;
        }

        $iLimit = $this->getParam('limit', 5);
        $aStores = Phpfox::getService('ynsocialstore')->getFeaturedStore($iLimit);
        if(!count($aStores))
            return false;

        $this->template()->assign(array(
            'aItems' => $aStores,
            'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
        ));

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('ynsocialstore_featured_stores_limit'),
                'description' => _p('ynsocialstore_featured_stores_limit_description'),
                'value' => 5,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('ynsocialstore_featured_stores_limit_validation')
            ]
        ];
    }
}