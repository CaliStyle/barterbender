<?php


namespace Apps\P_AdvMarketplaceAPI\Api\Resource;

use Apps\Core_MobileApi\Adapter\MobileApp\Screen;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\Form\Validator\Filter\TextFilter;
use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;
use Apps\Core_MobileApi\Api\Resource\Object\Image;
use Apps\Core_MobileApi\Api\Resource\Object\Privacy;
use Apps\Core_MobileApi\Api\Resource\Object\Statistic;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Apps\Core_MobileApi\Api\Resource\TagResource;
use Apps\Core_MobileApi\Service\NameResource;
use Apps\P_AdvMarketplaceAPI\Api\Form\MarketplaceSearchForm;
use Phpfox;

class MarketplaceResource extends ResourceBase
{
    const RESOURCE_NAME = "advancedmarketplace";
    const TAG_CATEGORY = 'advancedmarketplace';
    public $resource_name = self::RESOURCE_NAME;
    public $module_name = 'advancedmarketplace';
    /**
     * Custom ID Field Name
     */
    protected $idFieldName = "listing_id";
    public $title;

    public $description;
    public $short_description;
    public $text;

    public $view_id;
    public $is_sponsor;
    public $is_featured;
    public $is_friend;
    public $is_liked;
    public $is_sell;
    public $is_closed;
    public $is_expired;
    public $is_notified;
    public $is_pending;
    public $auto_sell;
    public $currency_id;
    public $price;
    public $location;
    public $address;
    public $payment_methods;
    public $expired_date;
    public $mark_sold;
    public $is_draft;
    public $is_wishlist;

    public $image;
    public $images;

    public $group_id;

    public $country;
    public $province;
    public $postal_code;
    public $city;

    public $country_iso;
    public $country_child_id;
    /**
     * @var Statistic
     */
    public $statistic;

    /**
     * @var Privacy
     */
    public $privacy;

    public $user;


    public $categories = [];

    public $tags = [];

    public $buy_now_link;

    public $time_stamp;

    public function getMobileSettings($params = [])
    {
        $permission = NameResource::instance()
            ->getApiServiceByResourceName($this->resource_name)
            ->getAccessControl()
            ->getPermissions();

        $l = $this->getLocalization();
        $searchFilter = (new MarketplaceSearchForm());
        $searchFilter->setLocal($l);

        return self::createSettingForResource([
            'acl' => $permission,
            'resource_name' => $this->resource_name,
            'schema' => [
                'definition' => [
                    'categories' => 'advancedmarketplace_category[]'
                ],
            ],
            'search_input' => [
                'placeholder' => $l->translate('search_listings'),
            ],
            'sort_menu' => [
                'title' => $l->translate('sort_by'),
                'options' => $searchFilter->getSortOptions()
            ],
            'filter_menu' => [
                'title' => $l->translate('filter_by'),
                'options' => [
                    ['value' => 'all-time', 'label' => $l->translate('all_time')],
                    ['value' => 'this-month', 'label' => $l->translate('this_month')],
                    ['value' => 'this-week', 'label' => $l->translate('this_week')],
                    ['value' => 'today', 'label' => $l->translate('today')],
                ]
            ],
            'detail_view' => [
                'component_name' => 'marketplace_detail',
            ],
            'list_view.tablet' => [
                'numColumns' => 3,
            ],
            'list_view' => [
                'item_view' => 'marketplace',
                'noItemMessage' => $l->translate('no_advancedmarketplace_listings_found'),
                'alignment' => 'left',
                'numColumns' => 2,
                'layout' => Screen::LAYOUT_GRID_VIEW,
            ],
            'feed_view' => [
                'item_view' => 'embed_marketplace',
                'alignment' => 'left'
            ],
            'forms' => [
                'addItem' => [
                    'headerTitle' => $l->translate('add_new_listing'),
                    'apiUrl' => UrlUtility::makeApiUrl('advancedmarketplace/form')
                ],
                'editItem' => [
                    'headerTitle' => $l->translate('editing_listing'),
                    'apiUrl' => UrlUtility::makeApiUrl('advancedmarketplace/form/:id')
                ],
                'photos' => [
                    'headerTitle' => $l->translate('manage_photos'),
                    'apiUrl' => 'mobile/advancedmarketplace-photo/form/:id'
                ],
                'invite' => [
                    'headerTitle' => $l->translate('invite_friends'),
                    'apiUrl' => 'mobile/advancedmarketplace-invite/form/:id'
                ],
            ],
            'action_menu' => [
                ['label' => $l->translate('edit'), 'value' => Screen::ACTION_EDIT_ITEM, 'acl' => 'can_edit'],
                ['label' => $l->translate('manage_photos'), 'value' => 'advancedmarketplace/photos', 'acl' => 'can_manage_photo'],
                ['value' => 'advancedmarketplace/invite', 'label' => $l->translate('send_invitations'), 'acl' => 'can_invite'],
                ['value' => 'advancedmarketplace/wishlist', 'label' => $l->translate('advancedmarketplace_add_to_wishlist'), 'acl' => 'can_wishlist', 'show' => '!is_draft&&!is_pending&&!is_wishlist'],
                ['value' => 'advancedmarketplace/wishlist', 'label' => $l->translate('advancedmarketplace_remove_from_wishlist'), 'acl' => 'can_wishlist', 'show' => '!is_draft&&!is_pending&&is_wishlist'],
                ['value' => Screen::ACTION_APPROVE_ITEM, 'label' => $l->translate('approve'), 'acl' => 'can_approve', 'show' => 'is_pending'],
                ['value' => Screen::ACTION_FEATURE_ITEM, 'label' => $l->translate('feature'), 'acl' => 'can_feature', 'show' => '!is_draft&&!is_pending&&!is_featured'],
                ['value' => Screen::ACTION_FEATURE_ITEM, 'label' => $l->translate('un_feature'), 'acl' => 'can_feature', 'show' => '!is_draft&&!is_pending&&is_featured'],
                ['value' => Screen::ACTION_SPONSOR_ITEM, 'label' => $l->translate('sponsor'), 'acl' => 'can_sponsor', 'show' => '!is_draft&&!is_pending&&!is_sponsor'],
                ['value' => Screen::ACTION_SPONSOR_ITEM, 'label' => $l->translate('advancedmarketplace_un_sponsor'), 'acl' => 'can_sponsor', 'show' => '!is_draft&&!is_pending&&is_sponsor'],
                ['value' => Screen::ACTION_REPORT_ITEM, 'label' => $l->translate('report'), 'acl' => 'can_report', 'show' => '!is_owner'],
                ['value' => Screen::ACTION_DELETE_ITEM, 'label' => $l->translate('delete'), 'acl' => 'can_delete', 'style' => 'danger'],
            ],
            'app_menu' => [
                ['label' => $l->translate('all_listings'), 'params' => ['initialQuery' => ['view' => '']]],
                ['label' => $l->translate('my_listings'), 'params' => ['initialQuery' => ['view' => 'my']]],
                ['label' => $l->translate('my_wishlist'), 'params' => ['initialQuery' => ['view' => 'my-wishlist']]],
                ['label' => $l->translate('listing_invites'), 'params' => ['initialQuery' => ['view' => 'invites']]],
                ['label' => $l->translate('friends_listings'), 'params' => ['initialQuery' => ['view' => 'friend']]],
                ['label' => $l->translate('pending_listings'), 'params' => ['initialQuery' => ['view' => 'pending']], 'acl' => 'can_approve'],
                ['label' => $l->translate('expired_listings'), 'params' => ['initialQuery' => ['view' => 'expired']], 'acl' => 'can_view_expired'],
            ],
        ]);
    }

    protected function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('view_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('is_sponsor', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_featured', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_friend', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_liked', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_closed', ['type' => ResourceMetadata::INTEGER])
            ->mapField('is_notified', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_pending', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_sell', ['type' => ResourceMetadata::INTEGER])
            ->mapField('auto_sell', ['type' => ResourceMetadata::INTEGER])
            ->mapField('price', ['type' => ResourceMetadata::FLOAT])
            ->mapField('group_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('country_child_id', ['type' => ResourceMetadata::INTEGER]);
    }

    public function getPrice()
    {
        if (!empty($this->rawData['is_edit'])) {
            return $this->price;
        } else {
            if (isset($this->price) && isset($this->rawData['currency_id'])) {
                if ($this->price == '0.00') {
                    return $this->getLocalization()->translate('free');
                } else {
                    return html_entity_decode(Phpfox::getService('core.currency')->getCurrency($this->price, $this->rawData['currency_id']), ENT_QUOTES);
                }
            }
        }
        return null;
    }

    /**
     * @return Image|array|string
     */
    public function getImage()
    {
        $sizes = Phpfox::getParam('advancedmarketplace.thumbnail_sizes');

        if (!empty($this->rawData['image_path'])) {
            return Image::createFrom([
                'file' => $this->rawData['image_path'],
                'server_id' => $this->rawData['server_id'],
                'path' => 'advancedmarketplace.url_pic'
            ], $sizes);
        } else {
            return $this->getDefaultImage();
        }

    }

    public function getImages()
    {
        if (empty($this->images) && !empty($this->rawData['images_list'])) {
            $images = [];
            $sizes = Phpfox::getParam('advancedmarketplace.thumbnail_sizes');
            foreach ($this->rawData['images_list'] as $image) {
                $images[] = Image::createFrom([
                    'file' => $image['image_path'],
                    'server_id' => $image['server_id'],
                    'path' => 'advancedmarketplace.url_pic'
                ], $sizes)->toArray();
            }
            $this->images = $images;
        }
        return $this->images;
    }

    public function getCategories()
    {
        if (empty($this->categories) || isset($this->rawData['categories'])) {
            $this->categories = NameResource::instance()
                ->getApiServiceByResourceName(MarketplaceCategoryResource::RESOURCE_NAME)
                ->getByListingId($this->id);
        }
        return $this->categories;
    }

    public function getCountry()
    {
        $this->country = '';
        if (!empty($this->rawData['country_iso'])) {
            $this->country = html_entity_decode(Phpfox::getService('core.country')->getCountry($this->rawData['country_iso']), ENT_QUOTES);
        }
        return $this->country;
    }

    public function getProvince()
    {
        $this->province = '';
        if (!empty($this->rawData['country_child_id'])) {
            $this->province = html_entity_decode(Phpfox::getService('core.country')->getChild($this->rawData['country_child_id']), ENT_QUOTES);
        }
        return $this->province;
    }

    public function getIsPending()
    {
        $this->is_pending = $this->view_id == 1;
        return $this->is_pending;
    }

    public function getLocation()
    {
        $this->location = !empty($this->rawData['location']) ? $this->rawData['location'] : null;
        return $this->location;
    }

    public function getAddress()
    {
        $this->address = !empty($this->rawData['address']) ? $this->rawData['address'] : null;
        return $this->address;
    }

    public function getPaymentMethods()
    {
        $this->payment_methods = [];
        if (!empty($this->rawData['payment_methods'])) {
            $paymentMethod = unserialize($this->rawData['payment_methods']);
            if (is_array($paymentMethod)) {
                $this->payment_methods = $paymentMethod;
            }
        }
        return $this->payment_methods;
    }

    public function getExpiredDate()
    {
        $this->expired_date = '';
        if (!empty($this->rawData['expiry_date'])) {
            $this->expired_date = Phpfox::getTime('Y/m/d', $this->rawData['expiry_date']);
        }
        return $this->expired_date;
    }

    public function getMarkSold()
    {
        $this->mark_sold = $this->view_id == 2;
        return $this->mark_sold;
    }

    public function getIsDraft()
    {
        $this->is_draft = $this->rawData['post_status'] == 2;
        return $this->is_draft;
    }

    public function getDescription()
    {
        if (empty($this->description) && !empty($this->rawData['description'])) {
            $this->description = $this->rawData['description'];
        }
        TextFilter::pureText($this->description, null, true);
        return $this->description;
    }

    public function getShortDescription()
    {
        return TextFilter::pureHtml($this->rawData['short_description'], true);
    }

    public function getTags()
    {
        if (empty($this->tags)) {
            $originalTags = Phpfox::getService('tag')->getTagsById('advancedmarketplace', $this->id);
            if (!empty($originalTags[$this->id])) {
                $tags = [];
                foreach ($originalTags[$this->id] as $tag) {
                    $tags[] = TagResource::populate($tag)->displayShortFields()->toArray();
                }
                $this->tags = $tags;
            }
        }
        return $this->tags;
    }

    public function getText()
    {
        if (empty($this->text) && !empty($this->rawData['description'])) {
            $this->text = TextFilter::pureHtml($this->rawData['description'], true);
        }
        return $this->text;
    }

    public function getLink()
    {
        return Phpfox::permalink('advancedmarketplace.detail', $this->id, $this->title);
    }

    public function getFeedDisplay()
    {
        $this->setDisplayFields(['id', 'resource_name', 'title', 'short_description', 'price', 'image', 'categories', 'privacy', 'country', 'province', 'city']);
        return $this->toArray();
    }

    public function getUrlMapping($url)
    {
        $result = $url;
        $parts = explode('/', $url);

        if (count($parts) > 2 && $parts[0] == $this->module_name && $parts[1] == 'detail') {
            $result = [
                'routeName' => 'viewItemDetail',
                'params' => [
                    'module_name' => $this->module_name,
                    'resource_name' => $this->resource_name,
                    'id' => (int)$parts[2]
                ]
            ];
        }

        return $result;
    }

    public function getIsWishlist()
    {
        return !!$this->rawData['is_wishlist'];
    }
}
