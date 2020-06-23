<?php

namespace Apps\P_AdvMarketplace;

use Core\App;

/**
 * Class Install
 * @author  Neil J. <neil@phpfox.com>
 * @version 4.6.0
 * @package Apps\P_AdvMarketplace
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'P_AdvMarketplace';
    }

    protected function setAlias()
    {
        $this->alias = 'advancedmarketplace';
    }

    protected function setName()
    {
        $this->name = _p('P_AdvMarketplace');
    }

    protected function setVersion()
    {
        $this->version = '4.03p1';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.7.2';
    }

    protected function setSettings()
    {
        $this->settings = array(
            'advancedmarketplace_paging_mode' =>
                array(
                    'var_name' => 'advancedmarketplace_paging_mode',
                    'info' => 'Pagination Style',
                    'description' => 'Select Pagination Style at Search Page.',
                    'type' => 'select',
                    'value' => 'loadmore',
                    'options' =>
                        array(
                            'loadmore' => 'Scrolling down to Load More items',
                            'next_prev' => 'Use Next and Prev buttons',
                            'pagination' => 'Use Pagination with page number'
                        ),
                ),
            'can_follow_listings' =>
                array(
                    'var_name' => 'can_follow_listings',
                    'info' => 'Can users follow listings?',
                    'description' => 'Can users follow listings?',
                    'type' => 'boolean',
                    'value' => '1',
                ),
            'advancedmarketplace_view_time_stamp' =>
                array(
                    'var_name' => 'advancedmarketplace_view_time_stamp',
                    'info' => 'Marketplace View Time Stamp',
                    'description' => 'Marketplace View Time Stamp',
                    'type' => 'string',
                    'value' => 'F j, Y',
                ),
            'advmarketplace_display_update_date' =>
                array(
                    'var_name' => 'advmarketplace_display_update_date',
                    'info' => 'Allow displaying Updated Date of the listings',
                    'type' => 'boolean',
                    'value' => '0',
                ),
            'advmarketplace_days_to_notify_expire' =>
                array(
                    'var_name' => 'advmarketplace_days_to_notify_expire',
                    'info' => 'Days to Notify Expiring Listing',
                    'description' => 'When you allow listings to expire you can also set a notification to be sent automatically to the owner of the listing, you can define here how many days in advanced to notify them. If you set this to 0 no email will be sent to the owner.',
                    'type' => 'integer',
                    'value' => '0',
                ),
            'advmarketplace_meta_description' =>
                array(
                    'var_name' => 'advmarketplace_meta_description',
                    'info' => 'Advanced MarketPlace Meta Description',
                    'description' => 'Meta description added to pages related to the Advanced MarketPlace module.',
                    'type' => 'large_string',
                    'value' => 'Read up on the latest items of MarketPlace on Site Name.',
                ),
            'advmarketplace_meta_keywords' =>
                array(
                    'var_name' => 'advmarketplace_meta_keywords',
                    'info' => 'Advanced MarketPlace Meta Keywords',
                    'description' => 'Meta keywords that will be displayed on sections related to the Advanced MarketPlace module.',
                    'type' => 'large_string',
                    'value' => 'Price,Sales',
                ),
            'advmarketplace_custom_url' => [
                'var_name' => 'advmarketplace_custom_url',
                'info' => 'Update URL name for the app',
                'description' => '',
                'type' => 'string',
                'value' => 'advancedmarketplace',
            ],
        );
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = array(
            'can_create_listing' =>
                array(
                    'var_name' => 'can_create_listing',
                    'info' => 'Can create a listing?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'can_sell_items_on_advancedmarketplace' =>
                array(
                    'var_name' => 'can_sell_items_on_advancedmarketplace',
                    'info' => 'Can sell items on the marketplace?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'can_approve_listings' =>
                array(
                    'var_name' => 'can_approve_listings',
                    'info' => 'Can approve marketplace listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'can_access_advancedmarketplace' =>
                array(
                    'var_name' => 'can_access_advancedmarketplace',
                    'info' => 'Can browse and view listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 1,
                            4 => 1,
                            5 => 1,
                        ),
                ),
            'can_feature_listings' =>
                array(
                    'var_name' => 'can_feature_listings',
                    'info' => 'Can feature a listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'listing_approve' =>
                array(
                    'var_name' => 'listing_approve',
                    'info' => 'Enable if listings should be approved first before they are displayed publicly.',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 0,
                            2 => 1,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'max_upload_size_listing' =>
                array(
                    'var_name' => 'max_upload_size_listing',
                    'info' => 'Max file size for photos upload in kilobits (kb), (1024 kb = 1 mb). For unlimited add "0" without quotes.',
                    'type' => 'integer',
                    'value' => 500
                ),
            'can_delete_other_listings' =>
                array(
                    'var_name' => 'can_delete_other_listings',
                    'info' => 'Can delete all marketplace listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'can_post_comment_on_listing' =>
                array(
                    'var_name' => 'can_post_comment_on_listing',
                    'info' => 'Can post a comment on marketplace listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'can_edit_other_listing' =>
                array(
                    'var_name' => 'can_edit_other_listing',
                    'info' => 'Can edit all marketplace listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'can_delete_own_listing' =>
                array(
                    'var_name' => 'can_delete_own_listing',
                    'info' => 'Can delete own marketplace listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'can_edit_own_listing' =>
                array(
                    'var_name' => 'can_edit_own_listing',
                    'info' => 'Can edit own marketplace listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'can_post_a_review' =>
                array(
                    'var_name' => 'can_post_a_review',
                    'info' => 'Can review listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'can_delete_own_review' =>
                array(
                    'var_name' => 'can_delete_own_review',
                    'info' => 'Can delete own review?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'flood_control_advancedmarketplace' =>
                array(
                    'var_name' => 'flood_control_advancedmarketplace',
                    'info' => 'How many minutes should a user wait before they can create another marketplace listing? Note: Setting it to "0" (without quotes) is default and users will not have to wait.',
                    'type' => 'integer',
                    'value' => 0
                ),
            'can_view_draft_listings' =>
                array(
                    'var_name' => 'can_view_draft_listings',
                    'info' => 'Can view draft listings?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 0,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'total_photo_upload_limit' =>
                array(
                    'var_name' => 'total_photo_upload_limit',
                    'info' => 'Control how many photos a user can upload to a marketplace listing.',
                    'type' => 'integer',
                    'value' => 6
                ),
            'delete_other_reviews' =>
                array(
                    'var_name' => 'delete_other_reviews',
                    'info' => 'Can delete all reviews?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'points_advancedmarketplace' =>
                array(
                    'var_name' => 'points_advancedmarketplace',
                    'info' => 'Points received when creating a listing?',
                    'type' => 'integer',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 1,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
            'can_purchase_sponsor' =>
                array(
                    'var_name' => 'can_purchase_sponsor',
                    'info' => 'Can members of this user group purchase a sponsored ad space for their items?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'can_sponsor_advancedmarketplace' =>
                array(
                    'var_name' => 'can_sponsor_advancedmarketplace',
                    'info' => 'Can sponsor an advanced marketplace listing?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 0,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'auto_publish_sponsored_item' =>
                array(
                    'var_name' => 'auto_publish_sponsored_item',
                    'info' => 'Auto publish sponsored item?',
                    'description' => 'After the user has purchased a sponsored space, should the item be published right away? If set to No, the admin will have to approve each new purchased sponsored item space before it is shown in the site.',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 0,
                            3 => 0,
                            4 => 0,
                            5 => 0,
                        ),
                ),
            'advancedmarketplace_sponsor_price' =>
                array(
                    'var_name' => 'advancedmarketplace_sponsor_price',
                    'info' => 'How much is the sponsor space worth? This works in a CPM basis.',
                    'type' => 'currency'
                ),
            'can_view_expired' =>
                array(
                    'var_name' => 'can_view_expired',
                    'info' => 'Can members of this user group view the section "Expired" in the marketplace?',
                    'type' => 'boolean',
                    'value' =>
                        array(
                            1 => 1,
                            2 => 0,
                            3 => 0,
                            4 => 1,
                            5 => 0,
                        ),
                ),
        );
    }

    protected function setComponent()
    {
        $this->component = array(
            'block' =>
                array(
                    'listinglist' => '',
                    'category' => '',
                    'tag' => '',
                    'owner' => '',
                    'topcategories' => '',
                    'topsellers' => '',
                ),
            'controller' =>
                array(
                    'index' => 'advancedmarketplace.index',
                    'search' => 'advancedmarketplace.search',
                    'invoice' => 'advancedmarketplace.invoice',
                    'detail' => 'advancedmarketplace.detail',
                ),
        );
    }

    protected function setComponentBlock()
    {
        $index_blocks = $search_blocks = $detail_blocks = array();

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "advancedmarketplace.index"')
            ->executeField();

        if (!$iCnt) {
            $index_blocks = array(
                'Featured Listings' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.index',
                        'component' => 'listinglist',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' =>
                            array(
                                'data_source' => 'featured',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '5',
                                'is_slider' => '1',
                            ),
                    ),
                'Today Listings' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.index',
                        'component' => 'listinglist',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' =>
                            array(
                                'data_source' => 'today',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '5',
                                'is_slider' => '0',
                            ),
                    ),
                'Top Categories' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.index',
                        'component' => 'topcategories',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '3',
                        'params' =>
                            array(
                                'limit' => '8',
                                'cache_time' => '5',
                            ),
                    ),
                'Recent Listings' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.index',
                        'component' => 'listinglist',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '4',
                        'params' =>
                            array(
                                'data_source' => 'latest',
                                'display_view_more' => '0',
                                'limit' => '6',
                                'cache_time' => '5',
                                'is_slider' => '0',
                            ),
                    ),
                'Top Sellers' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.index',
                        'component' => 'topsellers',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '5',
                        'params' =>
                            array(
                                'limit' => '6',
                                'cache_time' => '5',
                            ),
                    ),
                'Recently Viewed' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.index',
                        'component' => 'listinglist',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '6',
                        'params' =>
                            array(
                                'data_source' => 'recent_viewed',
                                'display_view_more' => '0',
                                'limit' => '4',
                                'cache_time' => '5',
                                'is_slider' => '0',
                            ),
                    ),
            );
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "advancedmarketplace.search"')
            ->executeField();

        if (!$iCnt) {
            $search_blocks = array(
                'Categories' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.search',
                        'component' => 'category',
                        'location' => '1',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' => '',
                    ),
            );
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "advancedmarketplace.detail"')
            ->executeField();

        if (!$iCnt) {
            $detail_blocks = array(
                'Seller' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.detail',
                        'component' => 'owner',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' => '',
                    ),
                'You might interested' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.detail',
                        'component' => 'listinglist',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' =>
                            array(
                                'data_source' => 'interested',
                                'display_view_more' => '0',
                                'limit' => '3',
                                'cache_time' => '5',
                                'is_slider' => '0',
                            ),
                    ),
                'More from this seller' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'advancedmarketplace.detail',
                        'component' => 'listinglist',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '3',
                        'params' =>
                            array(
                                'data_source' => 'more_from_seller',
                                'display_view_more' => '0',
                                'limit' => '3',
                                'cache_time' => '5',
                                'is_slider' => '0',
                            ),
                    ),
            );
        }

        $this->component_block = array_merge($index_blocks, $search_blocks, $detail_blocks);
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->_apps_dir = 'p-advmarketplace';
        $this->_writable_dirs = [
            'PF.Base/file/pic/advancedmarketplace/'
        ];

        $this->menu = [
            'phrase_var_name' => 'menu_advancedmarketplace',
            'url' => 'advancedmarketplace',
            'icon' => 'usd',
        ];

        $this->database = [
            'Advancedmarketplace',
            'Advancedmarketplace_Category',
            'Advancedmarketplace_Category_Customgroup_Data',
            'Advancedmarketplace_Category_Data',
            'Advancedmarketplace_Custom_Field',
            'Advancedmarketplace_Custom_Field_Data',
            'Advancedmarketplace_Custom_Group',
            'Advancedmarketplace_Custom_Option',
            'Advancedmarketplace_Follow',
            'Advancedmarketplace_Image',
            'Advancedmarketplace_Invite',
            'Advancedmarketplace_Invoice',
            'Advancedmarketplace_Rate',
            'Advancedmarketplace_Recent_View',
            'Advancedmarketplace_Setting',
            'Advancedmarketplace_Text',
            'Advancedmarketplace_Today_Listing',
            'Advancedmarketplace_Track',
            'Advancedmarketplace_Wishlist'
        ];

        $this->admincp_route = "/advancedmarketplace/admincp";
        $this->_admin_cp_menu_ajax = false;
        $this->admincp_menu = [
            'Listings Statistics' => '#',
            'Manage Listings' => 'advancedmarketplace.advancedmarketplace',
            'Today Listings' => 'advancedmarketplace.todaylisting',
            'Add Category' => 'advancedmarketplace.add',
            'Manage Categories' => 'advancedmarketplace.index',
            'Migration' => 'advancedmarketplace.migration',
            'Location Setting' => 'advancedmarketplace.setting',
        ];

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
    }
}