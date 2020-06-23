<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class AdvancedmarketplaceController extends Phpfox_Component
{
    public function process()
    {
        $iPage = $this->request()->getInt('page');
        $aPages = array(5, 10, 15, 20);
        $aDisplays = array();
        $aCategories = Phpfox::getService('advancedmarketplace.category')->getForBrowse();
        $aCats = array();
        $bIsSearch = false;
        foreach ($aCategories as $aCategory) {
            $aCats[$aCategory['category_id']] = $aCategory['name'];
        }

        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
        }


        $sCategories = Phpfox::getService('advancedmarketplace.category')->display('option')->get();

        $aFilters = array(
            'status' => array(
                'type' => 'select',
                'options' => array(
                    '1' => _p('advancedmarketplace.opened'),
                    '2' => _p('advancedmarketplace.closed'),
                    '3' => _p('advancedmarketplace.pending'),
                ),
                'add_any' => true
            ),
            'listing' => array(
                'type' => 'input:text',
            ),
            'owner' => array(
                'type' => 'input:text',
            ),
            'sponsored' => array(
                'type' => 'select',
                'options' => array(
                    '2' => _p('advancedmarketplace.yes'),
                    '1' => _p('advancedmarketplace.no')
                ),
                'add_any' => true
            ),
            'feature' => array(
                'type' => 'select',
                'options' => array(
                    '2' => _p('advancedmarketplace.yes'),
                    '1' => _p('advancedmarketplace.no')
                ),
                'add_any' => true
            ),
            'draft' => array(
                'type' => 'select',
                'options' => array(
                    '2' => _p('advancedmarketplace.yes'),
                    '1' => _p('advancedmarketplace.no')
                ),
                'add_any' => true
            ),
            'categories' => array(
                'type' => 'select',
                'options' => $aCats,
                'add_any' => true
            ),

        );

        $oSearch = Phpfox::getLib('search')->set(array(
                'type' => 'listings',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );
        $sStatus = $oSearch->get('status');
        $sListingName = $oSearch->get('listing');
        $sOwner = $oSearch->get('owner');
        $sFeature = $oSearch->get('feature');
        $sSponsore = $oSearch->get('sponsored');
        $sApprove = $oSearch->get('draft');
        $sCats = $oSearch->get('category');

        if (!empty($sListingName)) {
            $oSearch->setCondition('l.title LIKE "%' . $sListingName . '%"');
        }
        if (!empty($sOwner)) {
            $oSearch->setCondition('u.full_name LIKE "%' . $sOwner . '%"');
        }
        if (!empty($sApprove)) {
            switch ($sApprove) {
                case '1':
                    $oSearch->setCondition(' l.post_status = 1');
                    break;
                case '2':
                    $oSearch->setCondition(' l.post_status = 2 ');
                    break;
            }
        }
        if (!empty($sSponsore)) {
            switch ($sSponsore) {
                case '1':
                    $oSearch->setCondition('l.is_sponsor = 0');
                    break;
                case '2':
                    $oSearch->setCondition('l.is_sponsor = 1');
                    break;
            }
        }
        if (!empty($sFeature)) {
            switch ($sFeature) {
                case '1':
                    $oSearch->setCondition('l.is_featured = 0');
                    break;
                case '2':
                    $oSearch->setCondition('l.is_featured = 1');
                    break;
            }
        }

        if (!empty($sStatus)) {
            switch ($sStatus) {
                case '1':
                    $oSearch->setCondition('l.view_id = 0');
                    break;
                case '2':
                    $oSearch->setCondition('l.view_id = 2');
                    break;
                case '3':
                    $oSearch->setCondition('l.view_id = 1');
                    break;
            }
        }
        unset($_POST['category_id']);
        if (!empty($sCats)) {
            $oSearch->setCondition('cd.category_id = ' . $sCats);
            $_POST['category_id'] = $sCats;
        }
        //$aCategory = $this->request()->get('val');

        /*if(!empty($aCategory))
        {
            $iMaxCat = $aCategory[0];
            foreach($aCategory as $iCat)
            {
                if($iMaxCat < $iCat)
                {
                    $iMaxCat = $iCat;
                }
            }
            if($iMaxCat)
            {
                $oSearch->setCondition('cd.category_id = '.$iMaxCat);
            }
    
        }*/

        $arr_select = $this->request()->getArray('val');
        if ($arr_select['arr_selected']) {

            $arr = substr($arr_select['arr_selected'], 1);

            $aItems = phpfox::getLib('database')->select('adv.listing_id')
                ->from(phpfox::getT('advancedmarketplace'), 'adv')
                ->where('adv.listing_id IN (' . $arr . ')')
                ->execute('getRows');

            foreach ($aItems as $iKey => $aValue) {
                phpfox::getService('advancedmarketplace.process')->delete($aValue['listing_id']);
            }
        }


        if ($this->request()->get('search-id')) {
            $bIsSearch = true;
        }
        $iDelete = $this->request()->getInt('delete');
        if (isset($iDelete)) {
            $aItem = phpfox::getLib('database')->select('adv.listing_id')
                ->from(phpfox::getT('advancedmarketplace'), 'adv')
                ->where('adv.listing_id = ' . $iDelete)
                ->execute('getRow');
            if (empty($aItem)) {

            } else {
                phpfox::getService('advancedmarketplace.process')->delete($aItem['listing_id']);
            }

        }
        $iLimit = 10;
        $core_path = phpfox::getParam('core.path');
        list($iCnt, $aListings) = Phpfox::getService('advancedmarketplace')->getListings($oSearch->getConditions(), '',
            $oSearch->getPage(), $iLimit);
        foreach ($aListings as $iKey => $aListing) {
            $aListings[$iKey]['num_cate'] = count($aListing['categories']);
            $aListings[$iKey]['categories_last'] = [$aListing['categories'][$aListings[$iKey]['num_cate'] - 1]];
        }
        Phpfox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $oSearch->getSearchTotal($iCnt)
        ));

        $this->template()->assign(array(
            'aListings' => $aListings,
            'core_path' => $core_path,
            'sCategories' => $sCategories,
            'iCategoryId' => isset($_POST['category_id']) ? $_POST['category_id'] : 0,
            'bIsSearch' => $bIsSearch
        ))
            ->setHeader(array(
                'jscript/admin.js' => 'app_p-advmarketplace',
                'jscript/add.js' => 'app_p-advmarketplace'
            ))
            ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('advancedmarketplace'),
                $this->url()->makeUrl('admincp.app', ['id' => '__module_advancedmarketplace']))
            ->setBreadcrumb(_p('advancedmarketplace.manage_listings'),
                $this->url()->makeUrl('admincp.advancedmarketplace.advancedmarketplace'));
    }
}
