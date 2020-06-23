<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/6/16
 * Time: 11:00 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Store_Detail extends Phpfox_Component
{
    public function process()
    {
        $sError = '';
        if (!$this->request()->getInt('req3'))
        {
            return Phpfox_Module::instance()->setController('error.404');
        }

        $this->template()
        ->setHeader('cache', array(
           'owl.carousel.min.js' => 'module_ynsocialstore',
           'owl.carousel.css' => 'module_ynsocialstore',
               )
        );

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();

        $iStoreId = $this->request()->getInt('req3');

        // Subpage of store detail

        $aPage = array('aboutus', 'policy', 'buyerprotection', 'shipandpayment', 'photos', 'faqs', 'activities','reviews', 'products');

        if (null != ($req5 = $this->request()->get('req5')) && isset($req5) && in_array($req5, $aPage)) {
            $firstpage = $req5;
        } else {
            $firstpage = '';
        }

        $this->getParam('hideBlock', $firstpage != '');

        $this->template()->clearBreadCrumb();

        $aStore = Phpfox::getService('ynsocialstore')->getStoreForDetailById($iStoreId);
        if(!$aStore || (isset($aStore['status']) && $aStore['status'] == 'deleted'))
        {
            $sError = _p('unable_to_find_the_store_you_are_looking_for');
            $this->template()->setTitle($aStore['name'])
                ->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore.store'));
            $this->template()->assign(array(
                                          'firstpage' => $firstpage,
                                          'sError' => $sError,
                                      ));
        }
        else {
            if(!defined('PHPFOX_CHECK_FOR_UPDATE_FEED')) {
                define('PHPFOX_CHECK_FOR_UPDATE_FEED', true);
            }

            if (Phpfox::isModule('privacy'))
            {
                Phpfox::getService('privacy')->check('ynsocialstore_store', $aStore['store_id'], $aStore['user_id'], $aStore['privacy'], $aStore['is_friend']);
            }

            if(!Phpfox::getUserParam('ynsocialstore.can_view_store') || (!Phpfox::isAdmin() && Phpfox::getUserId() != $aStore['user_id'] && in_array($aStore['status'], array('expired', 'pending', 'denied', 'draft'))))
            {
                return Phpfox_Error::display(_p('You don\'t have permission to view this store.'));
            }

            if ($aStore['user_id'] != Phpfox::getUserId())
            {
                Phpfox::getService('ynsocialstore.process')->updateTotalView($aStore['store_id']);
            }
            if(!$aStore['total_products'])
            {
                $this->url()->permalink('ynsocialstore.store',$aStore['store_id']);
            }
            $this->setParam(array(
                'aStore' => $aStore,
                'iStoreId' => $aStore['store_id'],
            ));

            $this->setParam('aFeedCallback', array(
                    'module' => 'ynsocialstore',
                    'table_prefix' => 'ynstore_',
                    'ajax_request' => 'ynsocialstore.addFeedComment',
                    'item_id' => $aStore['store_id'],
                    'disable_share' => false,
                    'feed_comment' => 'ynsocialstore_comment'
                )
            );
            if (!empty($aStore['module_id']) && $aStore['module_id'] != 'ynsocialstore') {
                if (Phpfox::hasCallback($aStore['module_id'], 'getStoreDetails')) {
                    $aCallback = Phpfox::callback($aStore['module_id'] . '.getStoreDetails', $aStore);
                } else {
                    if (Phpfox::isModule('pages')) {
                        $aCallback = $this->getStoreDetails($aStore);
                    } else {
                        $aCallback = Phpfox::callback($aStore['module_id'] . '.getStoreDetails', $aStore);
                    }
                }
                if (Phpfox::isModule('pages')) {
                    $this->template()
                        ->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
                        ->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
                }
                $this->template()
                    ->setBreadCrumb(_p('social_store'), $aCallback['url_home'] . 'social-store/');
                if ((Phpfox::isModule('pages') && $aStore['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aCallback['item_id'],
                        ''))) {
                    return Phpfox_Error::display(_p('Unable to view this item due to privacy settings'));
                }
            } else {
                $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'));
                $this->template()->setBreadcrumb(_p('all_stores'), $this->url()->makeUrl('ynsocialstore.store'));
            }

            Phpfox::getService('ynsocialstore.helper')->buildMenu();

            $this->template()->setBreadCrumb($aStore['name'], $this->url()->permalink('ynsocialstore.store', $iStoreId, $aStore['name']), true);
            $this->template()->setTitle($aStore['name']);
            $this->template()->assign(array(
                  'aStore' => $aStore,
                  'firstpage' => $firstpage,
                  'sError' => $sError,
                  'iPage' => $this->search()->getPage(),
                  'bIsDetail' => true,
              ));
        }

        if(!defined('PHPFOX_IS_PAGES_VIEW') && !defined('PHPFOX_PAGES_ITEM_TYPE') && !defined('PHPFOX_IS_USER_PROFILE')){
            $canCreateProduct = Phpfox::getService('ynsocialstore')->checkUserStores();
            if($canCreateProduct) {
                sectionMenu(_p('ynsocialstore_sell_new_product'), 'social-store/add');
            }
        }

        return true;
    }

    public function getStoreDetails($aItem)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages.pages'),
            'breadcrumb_home' => \Phpfox_Url::instance()->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'ynsocialstore/',
            'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_store_detail_clean')) ? eval($sPlugin) : false);
    }
}