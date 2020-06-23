<?php

namespace Apps\P_AdvMarketplace\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class IndexController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::getUserParam('advancedmarketplace.can_access_advancedmarketplace', true);

        if(($deleteId = $this->request()->get('delete')) && Phpfox::getService('advancedmarketplace.process')->delete($deleteId)) {
            $this->url()->send('advancedmarketplace', null, _p('advancedmarketplace.successfully_deleted_listing'));
        }

        $bIsInHomePage = $this->_checkIsInHomePage();
        if (!$bIsInHomePage) {
            return Phpfox::getLib('module')->setController('advancedmarketplace.search');
        }

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }

        $bIsUserProfile = false;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsUserProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        }

        $aSearchFields = array(
            'type' => 'advancedmarketplace.',
            'field' => 'l.listing_id',
            'search_tool' => array(
                'table_alias' => 'l',
                'search' => array(
                    'action' => $this->url()->makeUrl('advancedmarketplace.search'),
                    'default_value' => _p('advancedmarketplace.search_listings'),
                    'name' => 'search',
                    'field' => array('l.title', 'mt.description_parsed')
                ),
                'no_filters' => [_p('when')]
            )
        );

        if ($bIsInHomePage) {
            $aSearch['search_tool']['no_filters'] = [_p('sort'), _p('show'), _p('when')];
            unset($aSearchFields['search_tool']['custom_filters']);
        }

        $this->search()->set($aSearchFields);
        $aBrowseParams = array(
            'module_id' => 'advancedmarketplace',
            'alias' => 'l',
            'field' => 'listing_id',
            'table' => Phpfox::getT('advancedmarketplace'),
            'hide_view' => array('pending', 'my')
        );

        if (Phpfox::getParam('core.section_privacy_item_browsing')) {
            $aBrowseParams['join'] = array(
                'alias' => 'mt',
                'field' => 'listing_id',
                'table' => Phpfox::getT('advancedmarketplace_text')
            );
        }

        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_index_process_filter')) ? eval($sPlugin) : false);

        $arHeader = array(
            'country.js' => 'module_core',
            'feed.js' => 'module_feed',
            'jscript/jquery.cycle.all.js' => 'app_p-advmarketplace',
            'jscript/index.js' => 'app_p-advmarketplace',
            'jscript/jquery.easing.min.js' => 'app_p-advmarketplace',
            'jscript/ynmarketplace.js' => 'app_p-advmarketplace',
            'jscript/masterslider.css' => 'app_p-advmarketplace',
            'masterslider-style.css' => 'app_p-advmarketplace',
        );

        $this->search()->setContinueSearch(true);
        $this->search()->browse()
            ->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('advancedmarketplace.advancedmarketplace_paging_mode', 'next_prev'))
            ->execute();

        $this->setParam(array(
            'bIsInHomePage' => $bIsInHomePage,
        ));

        $this->_setMetaAndKeywordsOfPage();

        $this->template()->setTitle(($bIsProfile ? _p('advancedmarketplace.full_name_s_listings',
            array('full_name' => $aUser['full_name'])) : _p('advancedmarketplace.advanced_advancedmarketplace')))
            ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'), $this->url()->makeUrl('advancedmarketplace'))
            ->setHeader('cache', $arHeader)
            ->assign(array(
                "isIndex" => true,
                'error_img_path' => Phpfox::getParam('core.path') . 'theme/frontend/default/style/default/image/noimage/item.png',
                'googleApiKey' => Phpfox::getParam('core.google_api_key'),
                'bIsInHomePage' => $bIsInHomePage,
                'corepath' => phpfox::getParam('core.path'),
                'parentCategories' => Phpfox::getService('advancedmarketplace.category')->getParentCategoriesList()
            ));

        $this->template()->setPhrase(array(
            'advancedmarketplace.view_this_listing',
            'advancedmarketplace.address',
            'advancedmarketplace.listing',
            'advancedmarketplace.location',
        ));

        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_process_end')) ? eval($sPlugin) : false);

        $aFilterMenu = Phpfox::getService('advancedmarketplace')->getSectionMenu();

        $this->template()->buildSectionMenu('advancedmarketplace', $aFilterMenu);

        if (Phpfox::getUserParam('advancedmarketplace.can_create_listing') && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            sectionMenu(_p('create_new_listing'), 'advancedmarketplace.add');
        }
    }

    private function _setMetaAndKeywordsOfPage()
    {
        $this->template()->setMeta('keywords', Phpfox::getParam('advancedmarketplace.advmarketplace_meta_keywords'));
        $this->template()->setMeta('description',
            Phpfox::getParam('advancedmarketplace.advmarketplace_meta_description'));
    }

    private function _checkIsInHomePage()
    {
        $bIsInHomePage = false;
        $aParentModule = $this->getParam('aParentModule');
        $sTempSearch = $this->request()->get('s', 0);
        $sTempView = $this->request()->get('view', false);
        if ($sTempSearch == '' && $sTempView == '' && !isset($aParentModule['module_id']) && !$this->request()->get('search-id')
            && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('type')
            && !$this->request()->get('show')
            && !$this->request()->get('search')
            && $this->request()->get('req2') == '') {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                $bIsInHomePage = true;
            }
        }
        return $bIsInHomePage;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_index_clean')) ? eval($sPlugin) : false);
    }

}
