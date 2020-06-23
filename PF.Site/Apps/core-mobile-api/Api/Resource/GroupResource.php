<?php


namespace Apps\Core_MobileApi\Api\Resource;

use Apps\Core_MobileApi\Adapter\MobileApp\Screen;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\Form\Validator\Filter\TextFilter;
use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;
use Apps\Core_MobileApi\Api\Resource\Object\Image;
use Apps\Core_MobileApi\Api\Resource\Object\Privacy;
use Apps\Core_MobileApi\Api\Resource\Object\Statistic;
use Apps\Core_MobileApi\Service\GroupApi;
use Apps\Core_MobileApi\Service\NameResource;
use Phpfox;

class GroupResource extends ResourceBase
{
    const RESOURCE_NAME = "groups";
    const NO_JOIN = 0;
    const JOINED = 1;
    const REQUESTED = 2;

    public $resource_name = self::RESOURCE_NAME;

    protected $idFieldName = "page_id";

    public $title;
    public $reg_method;
    public $reg_name;
    public $category;
    public $type;
    public $view_id;
    public $is_liked;
    public $is_admin;
    public $is_reg;
    public $is_invited;
    public $is_pending;
    public $is_featured;
    public $is_sponsor;

    public $membership;
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

    public $post_types;
    public $profile_menus;


    public $user;

    public $text;
    public $description;

    public $type_category;

    public $summary;

    protected $groupInfo = null;

    public $image_id;

    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return Phpfox::permalink('groups', $this->id);
    }

    /**
     * @return Image|String
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
     * @return Image|null
     */
    public function getCovers()
    {
        if (!empty($this->rawData['cover_photo_id'])) {
            $cover = NameResource::instance()
                ->getApiServiceByResourceName(PhotoResource::RESOURCE_NAME)
                ->loadResourceById($this->rawData['cover_photo_id']);
            if ($cover) {
                $aSizes = Phpfox::getService('groups')->getPhotoPicSizes();
                return Image::createFrom([
                    'file'      => $cover['destination'],
                    'server_id' => $cover['server_id'],
                    'path'      => 'photo.url_photo'
                ], $aSizes, false);
            }
        }

        return $this->getDefaultImage(true);
    }


    /**
     * @return mixed
     */
    public function getPostTypes()
    {
        return (new GroupApi())->getPostTypes($this->getId());
    }

    public function getProfileMenus()
    {
        return (new GroupApi())->getProfileMenus($this->getId());
    }

    public function getText()
    {
        if ($this->groupInfo === null) {
            $this->groupInfo = Phpfox::getService('groups')->getInfo($this->id, true);
        }
        return TextFilter::pureHtml($this->groupInfo, true);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        if ($this->groupInfo === null) {
            $this->groupInfo = Phpfox::getService('groups')->getInfo($this->id, true);
        }
        return TextFilter::pureText($this->groupInfo, null, true);
    }

    public function getCategory()
    {
        if (!$this->category || !is_array($this->category)) {
            if (!empty($this->rawData['category_id'])) {
                $item = NameResource::instance()
                    ->getApiServiceByResourceName(GroupCategoryResource::RESOURCE_NAME)
                    ->loadResourceById($this->rawData['category_id']);
                if ($item) {
                    $this->category = GroupCategoryResource::populate($item)->displayShortFields()->toArray();
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
                    ->getApiServiceByResourceName(GroupTypeResource::RESOURCE_NAME)
                    ->loadResourceById($this->rawData['type_id']);
                if ($item) {
                    $this->type = GroupTypeResource::populate($item)->displayShortFields()->toArray();
                }
            }
        }
        return $this->type;
    }

    public function getRegName()
    {
        if (isset($this->reg_method)) {
            switch ($this->reg_method) {
                case 1:
                    $reg = $this->getLocalization()->translate('closed');
                    break;
                case 2:
                    $reg = $this->getLocalization()->translate('Secret');
                    break;
                default:
                    $reg = $this->getLocalization()->translate('public');
                    break;
            }
            return $reg;
        }
        return null;
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

    public function getSummary()
    {
        $reg_name = $this->reg_name;
        if (!$this->reg_name) {
            $reg_name = $this->getRegName();
        }
        $parent = '';
        if (!empty($this->rawData['parent_category_name'])) {
            $parent = $this->parse->cleanOutput($this->getLocalization()->translate($this->rawData['parent_category_name']));
        } else if (!empty($this->rawData['type_id'])) {
            $type = $this->getType();
            $parent = isset($type['name']) ? $type['name'] : '';
        }
        return $reg_name . (!empty($parent) ? ' · ' : '') . $parent;
    }

    public function getMembership()
    {
        $isLike = $this->is_liked;
        $joinRequest = Phpfox::getService('groups')->joinGroupRequested($this->id);
        $status = self::NO_JOIN;
        if ($isLike) {
            $status = self::JOINED;
        } else if ($joinRequest) {
            $status = self::REQUESTED;
        }
        return $status;
    }

    protected function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('reg_method', ['type' => ResourceMetadata::INTEGER])
            ->mapField('view_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('is_liked', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_admin', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_reg', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_invited', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_sponsor', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_featured', ['type' => ResourceMetadata::BOOL])
            ->mapField('latitude', ['type' => ResourceMetadata::FLOAT])
            ->mapField('longitude', ['type' => ResourceMetadata::FLOAT])
            ->mapField('item_type', ['type' => ResourceMetadata::INTEGER]);
    }

    public function getMobileSettings($params = [])
    {
        $resourceName = $this->getResourceName();
        $permission = NameResource::instance()->getApiServiceByResourceName($resourceName)->getAccessControl()->getPermissions();
        $l = $this->getLocalization();
        return self::createSettingForResource([
            'resource_name'   => $resourceName,
            'acl'             => $permission,
            'schema'          => [
                'definition' => [
                    'type'     => 'group_type',
                    'category' => 'group_category',
                ]
            ],
            'search_input'    => [
                'placeholder' => $l->translate('search_groups'),
            ],
            'detail_view'     => [
                "component_name" => 'groups_detail',
            ],
            'list_view'       => [
                'item_view'       => 'groups_item',
                'noItemMessage'   => [
                    'image'     => $this->getAppImage('no-item'),
                    'label'     => $l->translate('no_groups_found'),
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
            ],
            'action_menu'     => [
                ['label' => $l->translate('manage'), 'value' => 'groups/manage', 'show' => 'can_edit'],
                ['label' => $l->translate('approve'), 'value' => Screen::ACTION_APPROVE_ITEM, 'show' => 'is_pending', 'acl' => 'can_approve'],
                ['label' => $l->translate('feature'), 'value' => Screen::ACTION_FEATURE_ITEM, 'show' => '!is_featured&&!is_pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('remove_feature'), 'value' => Screen::ACTION_FEATURE_ITEM, 'show' => 'is_featured&&!is_pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('sponsor'), 'value' => Screen::ACTION_SPONSOR_ITEM, 'show' => '!is_sponsor&&!is_pending', 'acl' => 'can_sponsor'],
                ['label' => $l->translate('remove_sponsor'), 'value' => Screen::ACTION_SPONSOR_ITEM, 'show' => 'is_sponsor&&!is_pending', 'acl' => 'can_sponsor'],
                ['label' => $l->translate('report'), 'value' => Screen::ACTION_REPORT_ITEM, 'show' => 'can_report'],
                ['label' => $l->translate('delete_this_group'), 'value' => Screen::ACTION_DELETE_ITEM, 'style' => 'danger', 'show' => 'can_delete'],
            ],
            'detail_menu'     => [
                ['label' => $l->translate('update_cover'), 'value' => Screen::ACTION_EDIT_COVER, 'show' => 'can_edit', 'acl' => 'can_add_cover'],
                ['label' => $l->translate('update_avatar'), 'value' => Screen::ACTION_EDIT_AVATAR, 'show' => 'can_edit'],
                ['label' => $l->translate('remove_cover_photo'), 'value' => 'groups/remove_cover', 'show' => 'can_edit', 'acl' => 'can_remove_cover'],
            ],
            'sort_menu'       => [
                'title'    => $l->translate('sort_by'),
                'queryKey' => 'sort',
                'options'  => [
                    ['label' => $l->translate('latest'), 'value' => 'latest'],
                    ['label' => $l->translate('most_popular'), 'value' => 'most_liked'],
                ],
            ],
            'forms'           => [
                'addItem'        => [
                    'headerTitle' => $l->translate('add_new_group'),
                    'apiUrl'      => UrlUtility::makeApiUrl('groups/form'),
                ],
                'editAvatar'     => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('groups/avatar/:id'),
                ],
                'editCover'      => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('groups/cover/:id'),
                ],
                'editLocation'   => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('groups/location/:id'),
                ],
                'editProfile'    => [
                    'headerTitle' => $l->translate('edit_detail'),
                    'apiUrl'      => UrlUtility::makeApiUrl('group-profile/form/:id'),
                ],
                'editInfo'       => [
                    'headerTitle' => $l->translate('edit_info'),
                    'apiUrl'      => UrlUtility::makeApiUrl('group-info/form/:id'),
                ],
                'editPermission' => [
                    'apiUrl' => UrlUtility::makeApiUrl('group-permission/form/:id')
                ],
                'invite'         => [
                    'headerTitle' => $l->translate('invite_friends'),
                    'apiUrl'      => 'mobile/group-invite/form/:id',
                ],
                'editAdmin'      => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('group-admin/:id'),
                    'submitMethod' => 'put',
                    'itemType'     => 'group-admin'
                ],
            ],
            'app_menu'        => [
                ['label' => $l->translate('All Groups'), 'params' => ['initialQuery' => ['view' => '']]],
                ['label' => $l->translate('My Groups'), 'params' => ['initialQuery' => ['view' => 'my']]],
                ['label' => $l->translate('Friends\' Groups'), 'params' => ['initialQuery' => ['view' => 'friend']]],
                ['label' => $l->translate('Pending Groups'), 'params' => ['initialQuery' => ['view' => 'pending']], 'acl' => 'can_approve'],
            ],
            'moderation_menu' => [
                ['label' => $l->translate('approve'), 'value' => Screen::ACTION_APPROVE_ITEMS, 'style' => 'primary', 'show' => 'view==pending', 'acl' => 'can_approve'],
                ['label' => $l->translate('feature'), 'value' => Screen::ACTION_FEATURE_ITEMS, 'style' => 'primary', 'show' => 'view!=pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('remove_feature'), 'value' => Screen::ACTION_REMOVE_FEATURE_ITEMS, 'style' => 'primary', 'show' => 'view!=pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('delete'), 'value' => Screen::ACTION_DELETE_ITEMS, 'style' => 'danger', 'acl' => 'can_delete'],
            ],
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
            'covers',
            'reg_name',
            'view_id',
            'privacy',
            'is_liked',
            'is_admin',
            'is_invited',
            'reg_method',
            'creation_date',
            'modification_date',
            'extra',
            'resource_name',
            'is_pending',
            'is_sponsor',
            'is_featured'
        ];
    }

    public function getIsPending()
    {
        $this->is_pending = !!$this->view_id;
        return $this->is_pending;
    }

    public function setStatistic($statistic)
    {
        $statistic->total_pending_requests = (int)Phpfox::getService('groups')->getPendingUsers($this->id, true);
        $this->statistic = $statistic;
    }
}