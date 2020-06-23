<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ListingList extends Phpfox_Component
{

    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $limit = $this->getParam('limit', 4);
        if (!$limit) {
            return false;
        }

        $bIsSearch = $this->getParam('bIsSearch');
        $blockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $isSideLocation = Phpfox::getService('advancedmarketplace.helper')->bIsSideLocation($blockLocation);
        if ($bIsSearch && !$isSideLocation) {
            return false;
        }

        $id = $this->getParam('id');
        $aListing = $this->getParam('aListing');
        $cacheTime = $this->getParam('cache_time', 5);
        $dataSource = $this->getParam('data_source', 'latest');
        $cache = Phpfox::getLib('cache');
        $exceptionBlocks = ['recent_viewed'];
        if(in_array($dataSource, $exceptionBlocks)) {
            $cacheId = $cache->set('advancedmarketplace_block_'. $dataSource . '_' . Phpfox::getUserId());
        }
        else {
            if (empty($aListing)) {
                $cacheId = $cache->set('advancedmarketplace_block_' . $dataSource . '_' . $id);
            } else {
                $cacheId = $cache->set('advancedmarketplace_block_' . $dataSource . '_' . $id . '_' . $aListing['listing_id']);
            }
        }

        $cache->group('advancedmarketplace_block', $cacheId); // we wil remove this cache when a listing is deleted

        $conds = ['l.post_status != 2', 'l.view_id = 0', 'l.privacy = 0'];
        if (($items = $cache->get($cacheId, $cacheTime)) === false) {
            switch ($dataSource) {
                case 'featured':
                    $items = Phpfox::getService('advancedmarketplace')->getFeatured($limit);
                    break;
                case 'today':
                    $items = Phpfox::getService("advancedmarketplace")->frontend_getTodayListings(null, null, $limit);
                    break;
                case 'sponsored':
                    $items = Phpfox::getService('advancedmarketplace')->getSponsorListings($limit);
                    break;
                case 'most_viewed':
                    $conds[] = 'total_view > 0';
                    list($count, $items) = Phpfox::getService("advancedmarketplace")->frontend_getListings($conds,
                        'total_view DESC, time_stamp DESC', 0, $limit);
                    break;
                case 'most_liked':
                    $conds[] = 'total_like > 0';
                    list($count, $items) = Phpfox::getService("advancedmarketplace")->frontend_getListings($conds,
                        'total_like DESC, time_stamp DESC', 0, $limit);
                    break;
                case 'most_commented':
                    $conds[] = 'total_comment > 0';
                    list($count, $items) = Phpfox::getService("advancedmarketplace")->frontend_getListings($conds,
                        'total_comment DESC, time_stamp DESC', 0, $limit);
                    break;
                case 'most_reviewed':
                    list($count, $items) = phpfox::getService('advancedmarketplace')->getMostReviewedListing($limit);
                    foreach ($items as $key => $item) {
                        $items[$key]['rating'] = (int)$items[$key]['rating'] / 2;
                    }
                    break;
                case 'latest':
                    list($count, $items) = Phpfox::getService("advancedmarketplace")->frontend_getRecentListings($conds,
                        'time_stamp desc', 0, $limit);
                    break;
                case 'recent_viewed':
                    list($count, $items) = Phpfox::getService("advancedmarketplace")->frontend_getRecentViewListings(Phpfox::getUserId(),
                        null, $limit);
                    break;
                case 'interested':
                    if (empty($aListing)) {
                        return false;
                    }
                    list($count, $items) = Phpfox::getService("advancedmarketplace")->frontend_getInterestedListings($aListing['listing_id'], $limit);
                    break;
                case 'same_tag':
                    if (empty($aListing)) {
                        return false;
                    }
                    $tags = Phpfox::getService('advancedmarketplace')->getTagText($aListing['listing_id']);
                    if (empty($tags)) {
                        return false;
                    }
                    $tags = array_column($tags, 'tag_text');
                    $similarListings = Phpfox::getService('advancedmarketplace')->getSimilarListings($limit, $tags, $aListing['listing_id'], $isSideLocation);
                    foreach ($similarListings as $similarListing) {
                        $items[] = $similarListing;
                        $items[0]['rating'] = $items[0]['total_score'] / 2;
                    }
                    break;
                case 'more_from_seller':
                    if (empty($aListing)) {
                        return false;
                    }
                    list($count, $items) = Phpfox::getService("advancedmarketplace")->frontend_getSellerListings($aListing['listing_id'],
                        $aListing['user_id'], $limit);
                    break;
            }

            if ($cacheTime) {
                $cache->save($cacheId, $items);
            }
        }

        //check cache and remove item has the same id with current item id
        $checkSameItemCase = ['same_tag'];
        if (in_array($dataSource, $checkSameItemCase) && !empty($aListing)) {
            foreach ($items as $key => $item) {
                if ($item['listing_id'] == $aListing['listing_id']) {
                    unset($items[$key]);
                    break;
                }
            }
        }

        if (empty($items)) {
            return false;
        }

        // add sponsored view count
        if ($dataSource == 'sponsored' && Phpfox::isModule('ad')) {
            foreach ($items as $item) {
                Phpfox::getService('ad.process')->addSponsorViewsCount($item['sponsor_id'], 'advancedmarketplace');
            }
        }

        foreach ($items as $key => $item) {
            $defaultImage = $item['image_path'];
            $count = 1;
            if (!empty($item['images'])) {
                $selectedImages = [];
                foreach ($item['images'] as $image_key => $image) {
                    if (count($selectedImages) >= 3) {
                        break;
                    }
                    if ($image['image_path'] != $defaultImage) {
                        $count++;
                        $selectedImages[] = array_merge($image, ['position' => $count]);
                    }
                }
                $items[$key]['selected_images'] = $selectedImages;
            }
        }

        $noViewMoreLinksSources = ['today'];
        if (!in_array($dataSource, $noViewMoreLinksSources) && $this->getParam('display_view_more', 0)) {
            $aFooter = Phpfox::getService('advancedmarketplace.helper')->getFooterLink($dataSource, $aListing);
        }

        $isSlider = $this->getParam('is_slider', 0) && !$isSideLocation;

        $showStatistic = '';
        if (in_array($dataSource, ['most_viewed', 'recent_viewed', 'interested', 'sponsored', 'featured'])) {
            $showStatistic = 'view';
        } elseif (in_array($dataSource, ['most_commented'])) {
            $showStatistic = 'comment';
        } elseif (in_array($dataSource, ['most_liked'])) {
            $showStatistic = 'like';
        } elseif (in_array($dataSource, ['today', 'most_reviewed', 'same_tag'])) {
            $showStatistic = 'rating';
        }

        $this->template()->assign(array(
            'sHeader' => $this->getHeader($dataSource),
            'isSlider' => $isSlider,
            'isSideLocation' => $isSideLocation,
            'bShowModerator' => 0,
            'showConfigBtn' => 0,
            'corepath' => phpfox::getParam('core.path'),
            'aItems' => $items,
            'iLimit' => $limit,
            'sCustomClassName' => 'p-block',
            'pCustomClassName' => $dataSource == 'recent_viewed' ? 'p-advmarketplace-custom-gridview col-4' : 'col-6',
            'aFooter' => $aFooter,
            'sModeViewDefault' => $isSideLocation ? ($dataSource == 'sponsored' ? 'grid' : 'list') : 'grid',
            'dataSource' => $dataSource,
            'showStatistic' => $showStatistic,
            'showDescription' => in_array($dataSource, ['sponsored', 'featured'])
        ));

        return 'block';
    }

    private function getHeader($dataSource)
    {
        $header = '';
        switch ($dataSource) {
            case 'featured':
                $header = _p('featured_listings');
                break;
            case 'today':
                $header = _p('today_listings');
                break;
            case 'sponsored':
                $header = _p('sponsored_listing');
                break;
            case 'most_viewed':
                $header = _p('most_viewed_listings');
                break;
            case 'most_liked':
                $header = _p('most_liked_listing');
                break;
            case 'most_commented':
                $header = _p('most_discussed_listings');
                break;
            case 'most_reviewed':
                $header = _p('most_reviewed_listing');
                break;
            case 'latest':
                $header = _p('recent_listing');
                break;
            case 'recent_viewed':
                $header = _p('recent_viewed_listing');
                break;
            case 'interested':
                $header = _p('listing_you_may_interested');
                break;
            case 'same_tag':
                $header = _p('same_tag_listings');
                break;
            case 'more_from_seller':
                $header = _p('more_from_seller');
                break;
        }

        return $header;
    }

    /**
     * Block settings
     *
     * @return array
     */
    public function getSettings()
    {
        return [
            array(
                'info' => _p('data_source'),
                'value' => 'latest',
                'options' => array(
                    'featured' => _p('featured_listings'),
                    'today' => _p('today_listings'),
                    'sponsored' => _p('sponsored_listings'),
                    'most_viewed' => _p('most_viewed_listings'),
                    'most_liked' => _p('most_liked_listings'),
                    'most_commented' => _p('most_discussed_listings'),
                    'most_reviewed' => _p('most_reviewed_listings'),
                    'latest' => _p('latest_listings'),
                    'recent_viewed' => _p('recent_viewed_listings'),
                    'interested' => _p('based_on_listings_that_have_the_same_category'),
                    'same_tag' => _p('based_on_listings_that_have_the_same_tag'),
                    'more_from_seller' => _p('more_from_this_seller'),
                ),
                'type' => 'select',
                'var_name' => 'data_source',
            ),
            array(
                'info' => _p('display_view_more_link'),
                'description' => _p('advancedmarketplace_display_view_more_link_desc'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'display_view_more',
            ),
            array(
                'info' => _p('advancedmarketplace_block_listing_limit_info'),
                'description' => _p('advancedmarketplace_block_listing_limit_description'),
                'value' => Phpfox::getParam('advancedmarketplace.total_listing_more_from'),
                'var_name' => 'limit',
                'type' => 'integer'
            ),
            array(
                'info' => _p('cache_time'),
                'description' => _p('Define how long we should keep the cache for the listings by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ),
            array(
                'info' => _p('slider_format'),
                'value' => false,
                'type' => 'boolean',
                'var_name' => 'is_slider',
            ),
        ];
    }

    /**
     * Validation
     *
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int:required',
                'min' => 0,
                'title' => _p('advancedmarketplace_limit_must_greater_or_equal_0',
                    ['var_name' => _p('advancedmarketplace_block_recentlisting_limit_info')])
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_block_recentlisting_clean')) ? eval($sPlugin) : false);
    }
}
