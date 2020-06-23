<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          AnNT
 * @package         YouNet Coupon
 * @version         3.02
 */

class Coupon_Component_Block_Search extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iCategory = $this->getParam('category');
        $sCategories = Phpfox::getService('coupon.category')->display('search')->get($iCategory);
        $sCountry = $this->getParam('country_iso');
        $sCountries = Phpfox::getService('coupon.helper')->getSelectCountriesForSearch($sCountry);
        

        $this->setParam('country_child_filter',true); 
        
        $this->template()->assign(array(
            'sHeader' => _p('search'),
            'sCategories' => $sCategories,
            'sCountries' => $sCountries,            
        ));

        (($sPlugin = Phpfox_Plugin::get('coupon.component_block_search_process')) ? eval($sPlugin) : false);

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('coupon.component_block_search_clean')) ? eval($sPlugin) : false);
    }
}
