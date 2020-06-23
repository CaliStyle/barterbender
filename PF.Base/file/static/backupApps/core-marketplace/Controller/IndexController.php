<?php

namespace Apps\Core_Marketplace\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Database;
use Phpfox_Module;
use Phpfox_Pager;
use Phpfox_Plugin;


defined('PHPFOX') or exit('NO DICE!');

/**
 * Class IndexController
 * @package Apps\Core_Marketplace\Controller
 */
class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('marketplace.can_access_marketplace', true);

        if ($this->request()->getInt('req2') > 0) {
            return Phpfox_Module::instance()->setController('marketplace.view');
        }


        if (($iDeleteId = $this->request()->getInt('delete'))) {
            if (Phpfox::getService('marketplace.process')->delete($iDeleteId)) {
                $this->url()->send('marketplace', null, _p('listing_successfully_deleted'));
            }
        }

        if (($iRedirectId = $this->request()->getInt('redirect')) && ($aListing = Phpfox::getService('marketplace')->getListing($iRedirectId))
        ) {
            $this->url()->send('marketplace.view', [$aListing['title_url']]);
        }

        $aUser = [];
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

        $oServiceMarketplaceBrowse = Phpfox::getService('marketplace.browse');
        $sCategoryUrl = null;
        $sView = $this->request()->get('view');

        $bIsUserProfile = false;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsUserProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsUserProfile = true;
            $aUser = $this->getParam('aUser');
        }

        $aCountriesValue = [];
        $aCountries = Phpfox::getService('core.country')->get();
        foreach ($aCountries as $sKey => $sValue) {
            $aCountriesValue[] = [
                'link' => $sKey,
                'phrase' => $sValue
            ];
        }

        $aSearchFields = [
            'type' => 'marketplace',
            'field' => 'l.listing_id',
            'ignore_blocked' => true,
            'search_tool' => [
                'table_alias' => 'l',
                'search' => [
                    'action' => ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], [
                        'marketplace',
                        'view' => $this->request()->get('view')
                    ]) : $this->url()->makeUrl('marketplace', ['view' => $this->request()->get('view')])),
                    'default_value' => _p('search_listings'),
                    'name' => 'search',
                    'field' => ['l.title', 'mt.description_parsed']
                ],
                'sort' => [
                    'latest' => ['l.time_stamp', _p('latest')],
                    'most-liked' => ['l.is_sponsor DESC, l.total_like', _p('most_liked')],
                    'most-talked' => ['l.is_sponsor DESC, l.total_comment', _p('most_discussed')]
                ],
                'show' => [12, 15, 18, 21]
            ]
        ];

        if (!$bIsUserProfile) {
            $aSearchFields['search_tool']['custom_filters'] = [
                _p('location') => [
                    'param' => 'location',
                    'default_phrase' => _p('anywhere'),
                    'data' => $aCountriesValue,
                    'height' => '300px',
                    'width' => '150px'
                ]
            ];
        }

        $this->search()->set($aSearchFields);

        $aBrowseParams = [
            'module_id' => 'marketplace',
            'alias' => 'l',
            'field' => 'listing_id',
            'table' => Phpfox::getT('marketplace'),
            'hide_view' => ['pending', 'my']
        ];

        switch ($sView) {
            case 'sold':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND l.user_id = ' . Phpfox::getUserId());
                $this->search()->setCondition('AND (l.is_sell = 1 OR l.allow_point_payment = 1)');

                break;
            case 'featured':
                $this->search()->setCondition('AND l.is_featured = 1');
                break;
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND l.user_id = ' . Phpfox::getUserId());
                break;
            case 'pending':
                if (Phpfox::getUserParam('marketplace.can_approve_listings')) {
                    $this->search()->setCondition('AND l.view_id = 1');
                    $this->template()->assign('bIsInPendingMode', true);
                } else {
                    if ($bIsProfile === true) {
                        $this->search()->setCondition("AND l.view_id IN(" . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ") AND l.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ") AND l.user_id = " . $aUser['user_id'] . "");
                    } else {
                        $this->search()->setCondition('AND l.view_id = 0 AND l.privacy IN(%PRIVACY%)');
                    }
                }
                break;
            case 'expired':
                if (Phpfox::getParam('marketplace.days_to_expire_listing') > 0 && Phpfox::getUserParam('marketplace.can_view_expired')) {
                    $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
                    $this->search()->setCondition('AND l.time_stamp < ' . $iExpireTime);
                    break;
                } else {
                    $this->search()->setCondition('AND l.time_stamp < 0');
                }
                break;
            case 'invoice':
                $this->url()->send('marketplace.invoice');
                break;
            default:
                if ($bIsProfile === true) {
                    $this->search()->setCondition("AND l.view_id = 0 AND l.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ") AND l.user_id = " . $aUser['user_id'] . "");
                } else {
                    switch ($sView) {
                        case 'invites':
                            Phpfox::isUser(true);
                            $oServiceMarketplaceBrowse->seen();
                            break;
                    }
                    $this->search()->setCondition('AND l.view_id = 0 AND l.privacy IN(%PRIVACY%)');
                }
                break;
        }

        if (($sLocation = $this->request()->get('location'))) {
            $this->search()->setCondition('AND l.country_iso = \'' . Phpfox_Database::instance()->escape($sLocation) . '\'');
        }

        if ($this->request()->get('req2') == 'category') {
            $sCategoryUrl = $this->request()->getInt('req3');
            $iCategory = (int)$sCategoryUrl;
            $aListingCategory = Phpfox::getService('marketplace.category')->getCategory($iCategory);
            if ($aListingCategory) {
                $this->search()->setFormUrl($this->url()->permalink([
                    'marketplace.category',
                    'view' => $sView
                ], $iCategory, _p($aListingCategory['name'])));
            }
            $this->search()->setCondition('AND mcd.category_id = ' . $iCategory);
        }

        $oServiceMarketplaceBrowse->category($sCategoryUrl);

        if ($this->search()->isSearch()) {
            $oServiceMarketplaceBrowse->search();
        }

        if (Phpfox::getParam('marketplace.days_to_expire_listing') > 0 && $sView != 'my' && $sView != 'expired' && $sView != 'invites') {
            $iExpireTime = (PHPFOX_TIME - (Phpfox::getParam('marketplace.days_to_expire_listing') * 86400));
            $this->search()->setCondition(' AND l.time_stamp >=' . $iExpireTime);
        }

        $this->search()->setContinueSearch(true);
        $this->search()->browse()
            ->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('marketplace.marketplace_paging_mode', 'loadmore'))
            ->execute();

        // if its a user trying to buy sponsor space he should get only his own listings
        if ($this->request()->get('sponsor') == 'help') {
            $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId() . ' AND is_sponsor != 1');
        }

        (($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_index_process_filter')) ? eval($sPlugin) : false);


        $this->template()->setTitle(($bIsProfile ? _p('full_name_s_listings',
            ['full_name' => $aUser['full_name']]) : _p('marketplace')))
            ->setBreadCrumb(_p('marketplace'), ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                'marketplace') : $this->url()->makeUrl('marketplace')))
            ->setHeader('cache', [
                    'country.js' => 'module_core',
                ]
            )
            ->setMeta('description', Phpfox::getParam('marketplace.marketplace_meta_description'))
            ->setMeta('keywords', Phpfox::getParam('marketplace.marketplace_meta_keywords'))
            ->assign([
                    'aListings' => $this->search()->browse()->getRows(),
                    'sCategoryUrl' => $sCategoryUrl,
                    'sListingView' => $sView
                ]
            );

        (($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_process_end')) ? eval($sPlugin) : false);

        Phpfox::getService('marketplace')->buildSectionMenu();

        if ($sCategoryUrl !== null) {
            $aCategories = Phpfox::getService('marketplace.category')->getParentBreadcrumb($sCategoryUrl);
            $this->setParam('sCurrentCategory', $sCategoryUrl);
            $this->setParam('iParentCategoryId', Phpfox::getService('marketplace.category')->getParentCategoryId($sCategoryUrl));
            $iCnt = 0;
            foreach ($aCategories as $aCategory) {
                $iCnt++;

                $this->template()->setTitle($aCategory[0]);

                if ($bIsUserProfile) {
                    $aCategory[1] = str_replace('/marketplace/', '/' . $aUser['user_name'] . '/marketplace/',
                        $aCategory[1]);
                }

                $this->template()->setBreadCrumb($aCategory[0], $aCategory[1],
                    ($iCnt === count($aCategories) ? true : false));
            }
        }

        // section menu
        if (Phpfox::getUserParam('marketplace.can_create_listing') && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            sectionMenu(_p('menu_add_new_listing'), 'marketplace.add');
        }
        $aModerationMenu = [];
        $bShowModerator = false;
        if ($sView == 'pending') {
            if (Phpfox::getUserParam('marketplace.can_approve_listings')) {
                $aModerationMenu[] = [
                    'phrase' => _p('approve'),
                    'action' => 'approve'
                ];
            }
        } elseif (Phpfox::getUserParam('marketplace.can_feature_listings')) {
            $aModerationMenu[] = [
                'phrase' => _p('feature'),
                'action' => 'feature'
            ];
            $aModerationMenu[] = [
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            ];
        }
        if (Phpfox::getUserParam('marketplace.can_delete_other_listings')) {
            $aModerationMenu[] = [
                'phrase' => _p('delete'),
                'action' => 'delete'
            ];
        }
        if (count($aModerationMenu)) {
            $this->setParam('global_moderation', [
                    'name' => 'marketplace',
                    'ajax' => 'marketplace.moderation',
                    'menu' => $aModerationMenu
                ]
            );
            $bShowModerator = true;
        }
        $this->template()->assign(['bShowModerator' => $bShowModerator]);
        Phpfox_Pager::instance()->set([
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ]);

        $this->setParam('aGmapView', [
            'type' => 'marketplace_listing',
            'url' => $this->url()->makeUrl('marketplace.map', ['type' => 'marketplace_listing', 'view' => $sView])
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}