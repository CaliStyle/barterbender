<?php


namespace Apps\Core_MobileApi\Api\Resource;

use Apps\Core_MobileApi\Adapter\MobileApp\Screen;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\Form\Validator\Filter\TextFilter;
use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;
use Apps\Core_MobileApi\Api\Resource\Object\Image;
use Apps\Core_MobileApi\Api\Resource\Object\Privacy;
use Apps\Core_MobileApi\Api\Resource\Object\Statistic;
use Apps\Core_MobileApi\Service\NameResource;
use Apps\Core_MobileApi\Service\PageApi;
use Phpfox;

class PageResource extends ResourceBase
{
    const RESOURCE_NAME = "pages";
    const LIKED = 1;
    const NO_LIKE = 0;

    public $resource_name = self::RESOURCE_NAME;

    protected $idFieldName = "page_id";

    public $title;
    public $category;
    public $type;
    public $view_id;
    public $is_liked;
    public $is_admin;
    public $is_reg;
    public $is_invited;
    public $claim_id;
    public $is_pending;
    public $is_featured;
    public $is_sponsor;


    public $image;
    public $covers;
    public $cover_photo_position;
    public $cover_photo_id;

    public $latitude;
    public $longitude;
    public $location_name;

    public $item_type;
    public $type_name;
    /**
     * @var Statistic
     */
    public $statistic;

    /**
     * @var Privacy
     */
    public $privacy;

    public $user;

    public $text;
    public $description;

    public $type_category;

    public $membership;

    public $summary;

    protected $pageInfo = null;

    public $post_types;
    public $profile_menus;

    public $image_id;


    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return Phpfox::permalink('pages', $this->id);
    }

    /**
     * @return mixed
     */
    public function getPostTypes()
    {
        return (new PageApi())->getPostTypes($this->getId());
    }

    public function getProfileMenus()
    {
        return (new PageApi())->getProfileMenus($this->getId());
    }

    /**
     * @return Image|string
     */
    public function getImage()
    {
        if (!empty($this->rawData['is_detail'])) {
            $sUrl = $this->rawData['pages_image_path'];
        } else {
            $sUrl = $this->rawData['image_path'];
        }
        if (!empty($sUrl)) {
            $aSizes = [50, 120, 200];
            return Image::createFrom([
                'file'      => $sUrl,
                'server_id' => $this->rawData['image_server_id'],
                'path'      => 'pages.url_image'
            ], $aSizes);
        }
        return $this->getDefaultImage();
    }

    public function getImageId()
    {
        if (isset($this->rawData['page_user_id'])) {
            $avatar = storage()->get('user/avatar/' . $this->rawData['page_user_id']);
            if (!empty($avatar)) {
                $this->image_id = (int)$avatar->value;
            }
        }
        return $this->image_id;
    }

    /**
     * @return Image|string
     */
    public function getCovers()
    {
        if (!empty($this->rawData['cover_photo_id'])) {
            $cover = NameResource::instance()
                ->getApiServiceByResourceName(PhotoResource::RESOURCE_NAME)
                ->loadResourceById($this->rawData['cover_photo_id']);
            if ($cover) {
                $aSizes = Phpfox::getService('pages')->getPhotoPicSizes();
                return Image::createFrom([
                    'file'      => $cover['destination'],
                    'server_id' => $cover['server_id'],
                    'path'      => 'photo.url_photo'
                ], $aSizes, false);
            }
        }
        return $this->getDefaultImage(true);
    }

    public function getText()
    {
        if ($this->pageInfo === null) {
            $this->pageInfo = Phpfox::getService('pages')->getInfo($this->id, true);
        }
        return TextFilter::pureHtml($this->pageInfo, true);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        if ($this->pageInfo === null) {
            $this->pageInfo = Phpfox::getService('pages')->getInfo($this->id, true);
        }
        return TextFilter::pureText($this->pageInfo, null, true);
    }

    public function getCategory()
    {
        if (!$this->category || !is_array($this->category)) {
            if (!empty($this->rawData['category_id'])) {
                $item = NameResource::instance()
                    ->getApiServiceByResourceName(PageCategoryResource::RESOURCE_NAME)
                    ->loadResourceById($this->rawData['category_id']);
                if ($item) {
                    $this->category = PageCategoryResource::populate($item)->displayShortFields()->toArray();
                }
            }
        }
        return $this->category;
    }

    public function getType()
    {
        if (!$this->type || !is_array($this->type)) {
            if (!empty($this->rawData['type_id'])) {
                $item = NameResource::instance()
                    ->getApiServiceByResourceName(PageTypeResource::RESOURCE_NAME)
                    ->loadResourceById($this->rawData['type_id']);
                if ($item) {
                    $this->type = PageTypeResource::populate($item)->displayShortFields()->toArray();
                }
            }
        }
        return $this->type;
    }

    public function getSummary()
    {
        $parent = '';
        if (!empty($this->rawData['parent_category_name'])) {
            $parent = $this->parse->cleanOutput($this->getLocalization()->translate($this->rawData['parent_category_name']));
        } else if (!empty($this->rawData['type_id'])) {
            $type = $this->getType();
            $parent = isset($type['name']) ? $type['name'] : '';
        }
        return $parent;
    }

    public function getTypeName()
    {
        return $this->parse->cleanOutput($this->getLocalization()->translate($this->type_name));
    }

    public function getTypeCategory()
    {
        if (!empty($this->rawData['is_form'])) {
            $type = $this->getType();
            if (isset($type['id'])) {
                $type['id'] = "type_{$type['id']}";
            }
            $category = $this->getCategory();
            if (isset($category['id'])) {
                $category['id'] = "category_{$category['id']}";
            }
            return [$type, $category];
        }
        return null;
    }

    protected function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('view_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('is_liked', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_admin', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_reg', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_invited', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_pending', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_sponsor', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_featured', ['type' => ResourceMetadata::BOOL])
            ->mapField('latitude', ['type' => ResourceMetadata::FLOAT])
            ->mapField('longitude', ['type' => ResourceMetadata::FLOAT])
            ->mapField('item_type', ['type' => ResourceMetadata::INTEGER])
            ->mapField('claim_id', ['type' => ResourceMetadata::INTEGER]);
    }

    public function getMembership()
    {
        $isLike = $this->is_liked;
        $status = self::NO_LIKE;
        if ($isLike) {
            $status = self::LIKED;
        }
        return $status;
    }

    public function getMobileSettings($params = [])
    {
        $resourceName = $this->getResourceName();
        $permission = NameResource::instance()->getApiServiceByResourceName($resourceName)->getAccessControl()->getPermissions();
        $l = $this->getLocalization();
        return self::createSettingForResource([
            'acl'             => $permission,
            'resource_name'   => $this->getResourceName(),
            'schema'          => [
                'definition' => [
                    'type'     => 'page_type',
                    'category' => 'page_category',
                ]
            ],
            'search_input'    => [
                'placeholder' => $l->translate('search_pages'),
            ],
            'list_view'       => [
                'item_view'       => 'pages_item',
                'noItemMessage'   => [
                    'image'     => $this->getAppImage('no-item'),
                    'label'     => $l->translate('no_pages_found'),
                    'sub_label' => $l->translate('start_adding_items_by_create_new_stuffs'),
                    'action'    => [
                        'resource_name' => $this->getResourceName(),
                        'module_name'   => $this->getModuleName(),
                        'value'         => Screen::ACTION_ADD,
                        'label'         => $l->translate('add_new_item')
                    ]
                ],
                'noResultMessage' => [
                    'image'     => $this->getAppImage('no-result'),
                    'label'     => $l->translate('no_results'),
                    'sub_label' => $l->translate('try_another_search'),
                ],
                "apiUrl"          => "mobile/pages",
                "numColumns"      => 1,
            ],
            'detail_view'     => [
                'component_name' => 'pages_detail',
                ['label' => 'Share', 'value' => Screen::ACTION_SHARE_ITEM],
            ],
            'action_menu'     => [
                ['label' => $l->translate('manage'), 'value' => 'pages/manage', 'show' => 'can_edit'],
                ['label' => $l->translate('claim_page'), 'value' => 'pages/claim', 'show' => '!is_admin', 'acl' => 'can_claim'],
                ['label' => $l->translate('approve'), 'value' => Screen::ACTION_APPROVE_ITEM, 'show' => 'is_pending', 'acl' => 'can_approve'],
                ['label' => $l->translate('feature'), 'value' => Screen::ACTION_FEATURE_ITEM, 'show' => '!is_featured&&!is_pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('remove_feature'), 'value' => Screen::ACTION_FEATURE_ITEM, 'show' => 'is_featured&&!is_pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('sponsor'), 'value' => Screen::ACTION_SPONSOR_ITEM, 'show' => '!is_sponsor&&!is_pending', 'acl' => 'can_sponsor'],
                ['label' => $l->translate('remove_sponsor'), 'value' => Screen::ACTION_SPONSOR_ITEM, 'show' => 'is_sponsor&&!is_pending', 'acl' => 'can_sponsor'],
                ['label' => $l->translate('report'), 'value' => Screen::ACTION_REPORT_ITEM, 'show' => '!is_admin', 'acl' => 'can_report'],
                ['label' => $l->translate('delete_this_page'), 'value' => Screen::ACTION_DELETE_ITEM, 'style' => 'danger', 'show' => 'can_delete'],
            ],
            'detail_menu'     => [
                ['label' => $l->translate('update_cover'), 'value' => Screen::ACTION_EDIT_COVER, 'show' => 'can_edit', 'acl' => 'can_add_cover'],
                ['label' => $l->translate('update_avatar'), 'value' => Screen::ACTION_EDIT_AVATAR, 'show' => 'can_edit'],
                ['label' => $l->translate('remove_cover_photo'), 'value' => 'pages/remove_cover', 'show' => 'can_edit', 'acl' => 'can_remove_cover'],
            ],
            'sort_menu'       => [
                'title'    => $l->translate('sort_by'),
                'queryKey' => 'sort',
                'options'  => [
                    ['label' => $l->translate('latest'), 'value' => 'latest'],
                    ['label' => $l->translate('most_liked'), 'value' => 'most_liked'],
                ],
            ],
            'forms'           => [
                'addItem'        => [
                    'headerTitle' => $l->translate('add_new_page'),
                    'apiUrl'      => UrlUtility::makeApiUrl('pages/form'),
                ],
                'editAvatar'     => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('pages/avatar/:id'),
                ],
                'editCover'      => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('pages/cover/:id'),
                ],
                'editLocation'   => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('pages/location/:id'),
                ],
                'editAdmin'      => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('page-admin/:id'),
                    'submitMethod' => 'put',
                    'itemType'     => 'page-admin'
                ],
                'editProfile'    => [
                    'headerTitle' => $l->translate('edit_detail'),
                    'apiUrl'      => UrlUtility::makeApiUrl('page-profile/form/:id'),
                ],
                'editInfo'       => [
                    'headerTitle' => $l->translate('edit_info'),
                    'apiUrl'      => UrlUtility::makeApiUrl('page-info/form/:id'),
                ],
                'editPermission' => [
                    'apiUrl' => UrlUtility::makeApiUrl('page-permission/form/:id')
                ],
                'claimThisPage'  => [
                    'headerTitle' => $l->translate('claim_page'),
                    'apiUrl'      => UrlUtility::makeApiUrl('page-claim/form/:id')
                ],
                'invite'         => [
                    'headerTitle' => $l->translate('invite_friends'),
                    'apiUrl'      => 'mobile/page-invite/form/:id',
                ]
            ],
            'app_menu'        => [
                ['label' => $l->translate('all_pages'), 'params' => ['initialQuery' => ['view' => '']]],
                ['label' => $l->translate('my_pages'), 'params' => ['initialQuery' => ['view' => 'my']]],
                ['label' => $l->translate('friends_pages'), 'params' => ['initialQuery' => ['view' => 'friend']]],
                ['label' => $l->translate('pending_pages'), 'params' => ['initialQuery' => ['view' => 'pending']], 'acl' => 'can_approve'],
            ],
            'moderation_menu' => [
                ['label' => $l->translate('approve'), 'value' => Screen::ACTION_APPROVE_ITEMS, 'style' => 'primary', 'show' => 'view==pending', 'acl' => 'can_approve'],
                ['label' => $l->translate('feature'), 'value' => Screen::ACTION_FEATURE_ITEMS, 'style' => 'primary', 'show' => 'view!=pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('remove_feature'), 'value' => Screen::ACTION_REMOVE_FEATURE_ITEMS, 'style' => 'primary', 'show' => 'view!=pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('delete'), 'value' => Screen::ACTION_DELETE_ITEMS, 'style' => 'danger', 'acl' => 'can_delete'],
            ]
        ]);
    }

    /**
     * @return mixed
     */
    public function getItemType()
    {
        return $this->item_type ? 'groups' : 'pages';
    }

    public function getShortFields()
    {
        return [
            'id',
            'title',
            'membership',
            'image',
            'statistic',
            'user',
            'summary',
            'view_id',
            'covers',
            'privacy',
            'is_liked',
            'is_admin',
            'creation_date',
            'modification_date',
            'resource_name',
            'is_pending',
            'is_sponsor',
            'is_featured'
        ];
    }

    public function getLatitude()
    {
        $this->latitude = isset($this->rawData['location_latitude']) ? $this->rawData['location_latitude'] : null;
        return $this->latitude;
    }

    public function getLongitude()
    {
        $this->longitude = isset($this->rawData['location_longitude']) ? $this->rawData['location_longitude'] : null;
        return $this->longitude;
    }

    public function getIsLiked()
    {
        $this->is_liked = isset($this->rawData['is_liked']) ? $this->rawData['is_liked'] : Phpfox::getService('pages')->isMember($this->getId());
        return (bool)$this->is_liked;
    }

    public function getIsAdmin()
    {
        $this->is_admin = isset($this->rawData['is_admin']) ? $this->rawData['is_admin'] : Phpfox::getService('pages')->isAdmin($this->getId());
        return (bool)$this->is_admin;
    }

    public function getIsPending()
    {
        $this->is_pending = !!$this->view_id;
        return $this->is_pending;
    }
}