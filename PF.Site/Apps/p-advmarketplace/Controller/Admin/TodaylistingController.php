<?php

namespace Apps\P_AdvMarketplace\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class TodaylistingController extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
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
        //var_dump($aCategories);
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
        }

        $sCategories = Phpfox::getService('advancedmarketplace.category')->display('option')->get();

        $aFilters = array(
            'status' => array(
                'type' => 'select',
                'options' => array(
                    '1' => 'Open',
                    '2' => 'Close',
                    '3' => 'Unapproved'
                ),
                'add_any' => true
            ),
            'listing' => array(
                'type' => 'input:text',
            ),
            'owner' => array(
                'type' => 'input:text',
            ),
            'feature' => array(
                'type' => 'select',
                'options' => array(
                    '2' => 'Yes',
                    '1' => 'No'
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

        $sListingName = $oSearch->get('listing');
        $sOwner = $oSearch->get('owner');
        $sCats = $oSearch->get('category');
        $sFromDate = $this->request()->get('js_from__datepicker');
        $sToDate = $this->request()->get('js_to__datepicker');

        $iLimit = 10;

        if (!empty($sListingName)) {
            $oSearch->setCondition('l.title LIKE "%' . $sListingName . '%"');
        }
        if (!empty($sOwner)) {
            $oSearch->setCondition('u.full_name LIKE "%' . $sOwner . '%"');
        }

        if ($sFromDate && $sToDate) {
            $oParseInput = Phpfox::getLib('parse.input');
            // $sParsedText = $oParseInput->clean($sText);
            // $oSearch->setCondition('(td.time_stamp >= '.phpfox::getLib('date')->convertToGmt($sFromDateTime).' and td.time_stamp <= '.phpfox::getLib('date')->convertToGmt($sToDateTime).')');
            $oSearch->setCondition(
                str_replace("##", "%",
                    sprintf("UNIX_TIMESTAMP(STR_TO_DATE('%s ','##m/##d/##Y')) <= (td.time_stamp) AND (td.time_stamp) < (UNIX_TIMESTAMP(STR_TO_DATE('%s ','##m/##d/##Y')) + 86400)",
                        $oParseInput->clean($sFromDate), $oParseInput->clean($sToDate)))
            );
        }

        unset($_POST['category_id']);
        if (!empty($sCats)) {
            $oSearch->setCondition('cd.category_id = ' . $sCats);
            $_POST['category_id'] = $sCats;
        }
        $iDelete = $this->request()->getInt('delete');
        if (isset($iDelete)) {
            $aItem = phpfox::getLib('database')->select('adv.listing_id')
                ->from(phpfox::getT('advancedmarketplace'), 'adv')
                ->where('adv.listing_id = ' . $iDelete)
                ->execute('getRow');
            if (empty($aItem)) {

            } else {
                phpfox::getService('advancedmarketplace.process')->deleteTodayListing($aItem['listing_id']);
            }

        }
        if ($this->request()->get('search-id')) {
            $bIsSearch = true;
        }
        list($iCnt, $aListings) = Phpfox::getService('advancedmarketplace')->getTodayListings($oSearch->getConditions(),
            '', $oSearch->getPage(), $iLimit);
        Phpfox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $oSearch->getSearchTotal($iCnt)
        ));
        $this->template()->setTitle(_p('advancedmarketplace.today_listing'))
            ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('advancedmarketplace'),
                $this->url()->makeUrl('admincp.app', ['id' => '__module_advancedmarketplace']))
            ->setBreadcrumb(_p('today_listings'))
            ->assign(array(
                'aListings' => $aListings,
                'sCategories' => $sCategories,
                'iCategoryId' => isset($_POST['category_id']) ? $_POST['category_id'] : 0,
                'sFromDate' => $sFromDate,
                'sToDate' => $sToDate,
                'bIsSearch' => $bIsSearch
            ))
            ->setHeader([
                'jscript/admin.js' => 'app_p-advmarketplace',
            ]);
    }

    private function _convertToTimeStamp($sDate, $bToDate = false)
    {
        if (!$bToDate) {
            $aDate = explode('/', $sDate);
            $sTimeStamp = phpfox::getLib('date')->mktime(0, 0, 0, $aDate[0], $aDate[1], $aDate[2]);
            if (isset($sTimeStamp) && $sTimeStamp != '') {
                return $sTimeStamp;
            }

            return '';
        }
        $aDate = explode('/', $sDate);
        $sTimeStamp = phpfox::getLib('date')->mktime(23, 59, 59, $aDate[0], $aDate[1], $aDate[2]);
        if (isset($sTimeStamp) && $sTimeStamp != '') {
            return $sTimeStamp;
        }

        return '';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }

}
