<?php

namespace Apps\P_AdvMarketplace\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class SearchController extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::getUserParam('advancedmarketplace.can_access_advancedmarketplace', true);
        $this->setParam('bIsSearch', true);

        $isInPage = false;
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW) {
            $isInPage = true;
            $this->setParam('bIsInPage', $isInPage);
        }

        // certain conditions need to apply to sponsor a listing
        if ($this->request()->get('sponsor') == 'help') {
            // check if the user can sponsor items
            if (!Phpfox::getUserParam('advancedmarketplace.can_purchase_sponsor') &&
                !Phpfox::getUserParam('advancedmarketplace.can_sponsor_advancedmarketplace')) {
                $this->url()->forward($this->url()->makeUrl('advancedmarketplace.'),
                    _p('subscribe.the_feature_or_section_you_are_attempting_to_use_is_not_permitted_with_your_membership_level'));
            }
        }

        if (($iRedirectId = $this->request()->getInt('redirect')) && ($aListing = Phpfox::getService('advancedmarketplace')->getListing($iRedirectId,
                true))) {
            $this->url()->send('advancedmarketplace.view.all', array($aListing['title_url']));
        }

        $oServiceAdvancedMarketplaceBrowse = Phpfox::getService('advancedmarketplace.browse');
        $sCategoryUrl = null;
        $sView = $this->request()->get('view');
        $sSearch = $this->request()->get('search');
        $aForms = [];
        if (is_array($sSearch)) {
            $sSearch = $sSearch['search'];
        }

        $isCategoryPage = false;
        if ($this->request()->get('req3') == 'category') {
            $sCategoryUrl = $this->request()->getInt('req4');
            $isCategoryPage = true;
        }
        if (!$isCategoryPage && ($category = $this->request()->get('category'))) {
            $aForms['category'] = $category;
        }

        $categoryId = $isCategoryPage ? $sCategoryUrl : (!empty($category) ? $category : 0);
        if (!empty($categoryId)) {
            $strChildIds = rtrim($categoryId . ',' . Phpfox::getService('advancedmarketplace.category')->getChildIds($categoryId), ',');
            $this->search()->setCondition(' AND (mcd.category_id IN (' . $strChildIds . '))');
            $oServiceAdvancedMarketplaceBrowse->category($categoryId);
        }


        $bIsUserProfile = false;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsUserProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } elseif (defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsUserProfile = true;
            $aUser = $this->getParam('aUser');
        }

        $aCountriesValue = array();
        $aCountries = Phpfox::getService('core.country')->get();
        foreach ($aCountries as $sKey => $sValue) {
            $aCountriesValue[] = array(
                'link' => $sKey,
                'phrase' => $sValue
            );
        }
        $aParentModule = $this->getParam('aParentModule');
        $sUrl = $aParentModule['url'] . 'advancedmarketplace';

        $sort = [
            'latest' => ['l.time_stamp', _p('advancedmarketplace.latest')],
            'most-viewed' => ['l.total_view', _p('advancedmarketplace.most_viewed')],
            'most-liked' => ['l.total_like', _p('advancedmarketplace.most_liked')],
            'most-talked' => ['l.total_comment', _p('advancedmarketplace.most_discussed')],
            'most-reviewed' => ['l.total_rate desc, l.total_score', _p('advancedmarketplace.most_reviewed')],
            'low-high-price' => ['l.price', _p('low_high_price'), 'default_sort_order' => 'ASC'],
            'high-low-price' => ['l.price', _p('high_low_price')],
            'low-high-rating' => ['l.total_score', _p('low_high_rating'), 'default_sort_order' => 'ASC'],
            'high-low-rating' => ['l.total_score', _p('high_low_rating')],
        ];

        if (in_array($sView, ['all', 'my', 'invites', 'friend']) || $bIsUserProfile || $isInPage) {
            $sort = array_merge($sort, [
                'featured' => ['l.is_featured', _p('featured')],
                'sponsored' => ['l.is_sponsor', _p('sponsored')]
            ]);
        }

        if (!in_array($sView, ['my', 'my-wishlist']) && !$bIsUserProfile) {
            $sort['recent-viewed'] = [
                'review_time',
                _p('advancedmarketplace.recent_viewed')
            ];
        }

        $aSearchFields = array(
            'type' => 'advancedmarketplace.',
            'field' => 'l.listing_id',
            'search_tool' => array(
                'table_alias' => 'l',
                'search' => array(
                    'action' => $aParentModule != null ? $sUrl : ($bIsUserProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('advancedmarketplace', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('advancedmarketplace', array('view' => $this->request()->get('view')))),
                    'default_value' => _p('advancedmarketplace.search_listings'),
                    'name' => 'search',
                    'field' => array('l.title', 'mt.description_parsed')
                ),
                'sort' => $sort,
                'show' => array(10, 15, 18, 21)
            )
        );


        $sortQuery = $this->request()->get('sort');
        switch ($sortQuery) {
            case 'featured':
                {
                    $this->search()->setCondition(' AND l.is_featured = 1');
                    break;
                }
            case 'sponsored':
                {
                    $this->search()->setCondition(' AND l.is_sponsor = 1');
                    break;
                }
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

        switch ($sView) {
            case 'sold':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND l.user_id = ' . Phpfox::getUserId());
                $this->search()->setCondition('AND l.is_sell = 1');
                break;
            case 'featured':
                $this->search()->setCondition('AND l.is_featured = 1');
                break;
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND l.user_id = ' . Phpfox::getUserId());
                $this->setParam("bIsNoRecent", true);
                break;
            case 'pending':
                if (Phpfox::getUserParam('advancedmarketplace.can_approve_listings')) {
                    $this->search()->setCondition('AND l.view_id = 1 AND l.post_status != 2');
                    $this->template()->assign('bIsInPendingMode', true);

                }
                break;
            case 'expired':
                /*never expired,it will always show no result*/
                $this->search()->setCondition('AND l.has_expiry = 1 AND l.expiry_date < ' . PHPFOX_TIME);
                break;
            default:
                if ($bIsUserProfile === true) {
                    $this->setParam("bIsNoRecent", true);
                    $aUser = $this->getParam('aUser');
                    $this->search()->setCondition("AND l.post_status != 2 AND l.view_id = 0 AND l.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ") AND l.user_id = " . $aUser['user_id'] . "");
                    $this->search()->setCondition('AND l.user_id = ' . $aUser["user_id"]);
                    if (($sLocation = $this->request()->get('country'))) {
                        $this->search()->setCondition('AND l.country_iso = \'' . Phpfox::getLib('database')->escape($sLocation) . '\'');
                    }
                } else {
                    switch ($sView) {
                        case 'invites':
                            Phpfox::isUser(true);
                            $oServiceAdvancedMarketplaceBrowse->seen();
                            break;
                    }

                    if (($sLocation = $this->request()->get('country'))) {
                        $this->search()->setCondition('AND l.country_iso = \'' . Phpfox::getLib('database')->escape($sLocation) . '\'');
                    }

                    $this->search()->setCondition('AND l.view_id = 0 AND l.privacy IN(%PRIVACY%)');
                }
                break;
        }

        if (!empty($aParentModule)) {
            $this->search()->setCondition(' AND (l.module_id = "' . $aParentModule['module_id'] . '" AND l.item_id = ' . (int)$aParentModule['item_id'] . ')');
        }

        if (!($sView == "my" || $sView == "pending" || $bIsUserProfile === true)) {
            $this->search()->setCondition('AND l.post_status = 1');
        }

        $aLocation = $this->request()->get('val');

        $country_iso = $aLocation['country_iso'];
        if ($country_iso) {
            $this->template()->setHeader('cache', array(
                    '<script type="text/javascript">$Behavior.setCountry = function(){$(\'#js_country_iso_option_\' + "' . $country_iso . '" ).prop(\'selected\', true)}</script>'
                )
            );
        }

        if ($aLocation) {
            $bCheckChildCountry = [];
            if (!empty($country_iso)) {
                $bCheckChildCountry = Phpfox::getLib('database')
                    ->select('child_id')
                    ->from(':country_child')
                    ->where("country_iso='" . $aLocation['country_iso'] . "'")
                    ->execute('getRow');
            }


            if (!empty($country_iso)) {
                $this->setParam(array('country_child_value' => $country_iso));
                $this->search()->setCondition('AND l.country_iso = \'' . Phpfox::getLib('database')->escape($country_iso) . '\'');
            }
            if (!empty($aLocation['country_child_id']) && count($bCheckChildCountry) > 0) {
                $this->setParam(array('country_child_id' => $aLocation['country_child_id']));
                $this->search()->setCondition('AND l.country_child_id = \'' . Phpfox::getLib('database')->escape($aLocation['country_child_id']) . '\'');
            }
        }

        if ($sCity = $this->request()->get('city')) {
            $sCity = phpfox::getLib('parse.input')->prepare($sCity);
            $this->search()->setCondition(' AND l.city LIKE \'%' . Phpfox::getLib('database')->escape($sCity) . '%\'');
            $this->template()->assign('sCity', $sCity);
        }

        $sZipCode = $this->request()->get('zipcode');
        if (isset($sZipCode) && $sZipCode != '') {
            $this->search()->setCondition(' AND l.postal_code = \'' . $sZipCode . '\'');
            $this->template()->assign('sZipCode', $sZipCode);
        }

        if ($this->request()->get('seller-more')) {
            $iId = $this->request()->get('seller-more');
            $aItem = phpfox::getService('advancedmarketplace')->getListing($iId);
            $this->search()->setCondition('AND l.user_id = ' . $aItem['user_id']);
            $this->search()->setCondition('AND l.post_status = 1');
        }

        if ($this->request()->get('interesting')) {
            $iId = $this->request()->get('interesting');
            $aCategories = phpfox::getLib('database')->select('cd.category_id')
                ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
                ->where('cd.listing_id = ' . $iId)
                ->execute('getSlaveRows');
            $sCategories = '';
            foreach ($aCategories as $iKey => $aCategory) {
                $sCategories .= $aCategory['category_id'] . ',';
            }
            $iCatId = phpfox::getService('advancedmarketplace.category')->getChildIdsOfCats($aCategories);
            if ($iCatId['category_id'] == '') {
                $iCatId['category_id'] = 0;
            }
            $iCnt = phpfox::getLib('database')->select('count(cd.category_id)')
                ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
                ->where('cd.listing_id =' . $iId)
                ->execute('getSlaveField');
            $aListingIds = phpfox::getLib('database')->select('cd.listing_id')
                ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
                ->where('cd.category_id = ' . $iCatId['category_id'])
                ->execute('getRows');
            $sListingIds = '';
            foreach ($aListingIds as $iKey => $aId) {
                $sListingIds .= $aId['listing_id'] . ',';
            }
            $sListingIds = substr($sListingIds, 0, strlen($sListingIds) - 1);
            $aListings = phpfox::getLib('database')->select('cd.listing_id')
                ->from(phpfox::getT('advancedmarketplace_category_data'), 'cd')
                ->where('cd.listing_id in (' . $sListingIds . ')')
                ->group('cd.listing_id')
                ->having('count(cd.listing_id) = ' . $iCnt)
                ->execute('getRows');
            $sIds = '';
            foreach ($aListings as $iKey => $aId) {
                $sIds .= $aId['listing_id'] . ',';
            }
            $sIds = substr($sIds, 0, strlen($sIds) - 1);
            $this->search()->setCondition(' AND l.listing_id in (' . $sIds . ')');
            $this->search()->setCondition('AND l.post_status = 1');
        }

        list($isTagSearch, $tagSearchText) = Phpfox::getService('advancedmarketplace.helper')->_isTagSearching();

        if (!$isCategoryPage && $isTagSearch && !empty($tagSearchText)) {
            if (($aTag = Phpfox::getService('tag')->getTagInfo('advancedmarketplace', $tagSearchText))) {
                $this->template()->setBreadCrumb(_p('tag.topic') . ': ' . $aTag['tag_text'] . '',
                    $this->url()->makeUrl('current'), true);
                $this->search()->setCondition('AND (tag.tag_text = \'' . Phpfox::getLib('database')->escape($aTag['tag_text']) . '\' AND tag.tag_type = 0)');
                $this->search()->setCondition('AND l.post_status = 1');
            } else {
                $this->search()->setCondition('AND 0');
            }
        }

        $this->template()->assign('sSearch', $sSearch);
        $this->setParam('sCategory', $sCategoryUrl);

        if ($sView != 'my' && $sView != 'expired' && $sView != 'my-wishlist') {
            $this->search()->setCondition(' AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')');
        }

        // if its a user trying to buy sponsor space he should get only his own listings
        if ($this->request()->get('sponsor') == 'help') {
            $this->search()->setCondition(' AND l.user_id = ' . Phpfox::getUserId());
        }

        $pagingMode = Phpfox::getParam('advancedmarketplace.advancedmarketplace_paging_mode', 'next_prev');
        $this->search()->browse()->params($aBrowseParams)->setPagingMode($pagingMode)->execute();

        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_index_process_filter')) ? eval($sPlugin) : false);

        $aListings = (($this->search()->browse()->getRows()));

        foreach ($aListings as $iKey => $aListing) {
            list($aListings[$iKey]['average_score'], $aListings[$iKey]['rating_star']) = Phpfox::getService('advancedmarketplace.rate')->getAverageScoreAndRatingStar($aListing['total_score']);
            $aListings[$iKey]['aFeed']['feed_mini'] = true;
            if ($aListings[$iKey]['view_id'] == 1) {
                $aListings[$iKey]['aFeed']['no_share'] = false;
            }
            $aListings[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aListing['price'], $aListing['currency_id']);
        }

        $this->template()
            ->setTitle(($bIsUserProfile ? _p('advancedmarketplace.full_name_s_listings',
                array('full_name' => $aUser['full_name'])) : _p('advancedmarketplace.advanced_advancedmarketplace')))
            ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                    'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')))
            ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'feed.js' => 'module_feed',
                    'jscript/jquery.cycle.all.js' => 'app_p-advmarketplace',
                    'jscript/index.js' => 'app_p-advmarketplace',
                )
            )
            ->assign(array(
                    'aListings' => $aListings,
                    'corepath' => phpfox::getParam('core.path'),
                    'sCategoryUrl' => $sCategoryUrl,
                    'sListingView' => $sView,
                    'error_img_path' => Phpfox::getParam('core.path') . 'theme/frontend/default/style/default/image/noimage/item.png'
                )
            );

        switch ($sView) {
            case 'sold':
                $this->template()
                    ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                        ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                            'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')))
                    ->setBreadcrumb(_p('advancedmarketplace.sold'), null, true);
                break;
            case 'friend':
                $this->template()
                    ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                        ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                            'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')));
                break;
            case 'featured':
                $this->template()
                    ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                        ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                            'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')))
                    ->setBreadcrumb(_p('advancedmarketplace.featured_listings'), null, true);
                break;
            case 'my':
                $this->template()
                    ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                        ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                            'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')));
                break;
            case 'pending':
                $this->template()
                    ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                        ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                            'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')));
                break;
            default:
                if ($bIsUserProfile === true) {
                    $this->template()
                        ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                            ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                                'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')));
                    break;
                } else {
                    switch ($sView) {
                        case 'invites':
                            $this->template()
                                ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                                    ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'],
                                        'advancedmarketplace.') : $this->url()->makeUrl('advancedmarketplace.')));
                            break;
                    }
                }
                break;
        }

        $this->template()->setPhrase(array(
            'advancedmarketplace.view_this_listing',
            'advancedmarketplace.address',
            'advancedmarketplace.listing',
            'advancedmarketplace.location',

        ));
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_process_end')) ? eval($sPlugin) : false);

        $aFilterMenu = Phpfox::getService('advancedmarketplace')->getSectionMenu();
        $this->template()->buildSectionMenu('advancedmarketplace', $aFilterMenu);

        if (Phpfox::getUserParam('advancedmarketplace.can_create_listing') && ((!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id'])) && !defined('PHPFOX_IS_PAGES_VIEW'))) {
            sectionMenu(_p('create_new_listing'), 'advancedmarketplace.add');
        }

        if ($sCategoryUrl !== null) {
            List($allCates,) = Phpfox::getService('advancedmarketplace.category')->getCategorieStructure(true);
            $aCatesPathInvert = array();
            $iCurrentId = $sCategoryUrl;
            while ($iCurrentId != 0) {
                $aCatesPathInvert[] = $allCates[$iCurrentId];
                $iCurrentId = $allCates[$iCurrentId]["parent_id"];
            }
            $aCatesPath = array_reverse($aCatesPathInvert);

            $iCnt = 0;
            foreach ($aCatesPath as $aCategory) {
                $iCnt++;

                $this->template()->setTitle(Phpfox::getLib('locale')->convert($aCategory['name']));

                if ($bIsUserProfile) {
                    $aCategory["url"] = str_replace('/advancedmarketplace/',
                        '/' . $aUser['user_name'] . '/advancedmarketplace/', $aCategory["url"]);
                }
                $this->template()->setBreadcrumb(Phpfox::getLib('locale')->convert($aCategory['name']), $aCategory["url"],
                    ($iCnt === count($aCatesPath) ? true : false));
            }
        }
        $aModeration = [];
        if (Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')) {
            $aModeration[] = [
                'phrase' => _p('delete'),
                'action' => 'delete'
            ];
        }
        if (Phpfox::getUserParam('advancedmarketplace.can_feature_listings')) {
            $aModeration[] = [
                'phrase' => _p('feature'),
                'action' => 'feature'
            ];
            $aModeration[] = [
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            ];
        }
        if (isset($sView) && $sView == 'pending' && Phpfox::getUserParam('advancedmarketplace.can_approve_listings')) {
            array_unshift($aModeration, array('phrase' => _p('approve'), 'action' => 'approve'));
        }

        $this->setParam('global_moderation', array(
            'name' => 'advancedmarketplace.', //jh: recheck
            'ajax' => 'advancedmarketplace.moderation',
            'menu' => $aModeration
        ));

        $currentPage = $this->search()->getPage() ? $this->search()->getPage() : 1;
        $currentDisplay = $this->search()->getDisplay();
        $totalItem = $this->search()->browse()->getCount();

        $canContinuePaging = true;
        if(count($aListings) < $currentDisplay || ($currentPage * $currentDisplay) >= $totalItem) {
            $canContinuePaging = false;
        }

        if($canContinuePaging || $pagingMode != 'loadmore') {
            Phpfox::getLib('pager')->set(array(
                'page' => $currentPage,
                'size' => $currentDisplay,
                'count' => $totalItem,
                'paging_mode' => $this->search()->browse()->getPagingMode()
            ));
        }

        $this->template()->assign([
            'aForms' => $aForms,
            'parentCategories' => Phpfox::getService('advancedmarketplace.category')->getParentCategoriesList(),
            'isWishlistPage' => ($sView == 'my-wishlist'),
            'bShowModerator' => count($aModeration) ? 1 : 0,
            'showConfigBtn' => true,
            'canContinuePaging' => $canContinuePaging || $pagingMode != 'loadmore',
            'sView' => $sView
        ]);

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
