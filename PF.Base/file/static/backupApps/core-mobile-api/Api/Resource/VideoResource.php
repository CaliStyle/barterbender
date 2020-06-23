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
use Phpfox;

class VideoResource extends ResourceBase
{
    const RESOURCE_NAME = "video";
    public $resource_name = self::RESOURCE_NAME;

    public $title;

    public $description;
    public $text;

    public $in_process;
    public $is_stream;
    public $is_featured;
    public $is_spotlight;
    public $is_sponsor;
    public $is_liked;
    public $is_friend;
    public $is_pending;
    public $view_id;

    public $module_id;
    public $item_id;

    public $destination;
    public $video_url;

    public $file_ext;
    public $duration;

    public $resolution_x;
    public $resolution_y;
    public $embed_code;

    public $image;

    public $status_info;

    public $page_user_id;
    public $location_latlng;
    public $location_name;

    /**
     * @var Statistic
     */
    public $statistic;

    /**
     * @var Privacy
     */
    public $privacy;

    /**
     * @var UserResource
     */
    public $user;

    public $categories;

    public $tags;

    /**
     * VideoResource constructor.
     *
     * @param $data
     *
     * @throws \Apps\Core_MobileApi\Api\Exception\UndefinedResourceName
     */
    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return Phpfox::permalink('video.play', $this->id, $this->title);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getCategories()
    {
        if (empty($this->categories) || isset($this->rawData['categories'])) {
            $this->categories = NameResource::instance()
                ->getApiServiceByResourceName(VideoCategoryResource::RESOURCE_NAME)
                ->getByVideoId($this->id);
        }
        return $this->categories;
    }

    /**
     * @return mixed
     */
    public function getEmbedCode()
    {
        if (!$this->embed_code) {
            $aEmbedVideo = Phpfox::getLib('database')
                ->select('video_url, embed_code')
                ->from(Phpfox::getT('video_embed'))
                ->where('video_id = ' . $this->getId())
                ->execute('getSlaveRow');

            $this->embed_code = isset($aEmbedVideo['embed_code'])
                ? $aEmbedVideo['embed_code']
                : null;
        } else {
            // check fb url
            $regex = "/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/";
            preg_match($regex, $this->video_url, $matches);
            if (count($matches) > 2) {
                $code = $matches[2];
                if ($code) {
                    $this->embed_code = '<iframe width="100%" height="360" src="//www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Ffacebook%2Fvideos%2F' . $code . '" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';
                }
            }
        }
        return $this->embed_code;
    }

    public function getTags()
    {
        if (!Phpfox::isModule('tag')) {
            return null;
        }
        $tag = Phpfox::getService('tag')->getTagsById('v', $this->id);
        if (!empty($tag[$this->id])) {
            $tags = [];
            foreach ($tag[$this->id] as $tag) {
                $tags[] = TagResource::populate($tag)->displayShortFields()->toArray();
            }
            return $tags;
        }
        return null;
    }

    public function getText()
    {
        if ($this->text === null && isset($this->rawData['text'])) {
            $this->text = $this->rawData['text'];
        }
        TextFilter::pureHtml($this->text, true);
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        if ($this->description === null && isset($this->rawData['text'])) {
            $this->description = $this->rawData['text'];
        }
        TextFilter::pureText($this->description, null, true);
        return $this->description;
    }

    /**
     * @return Image|mixed|null|string
     */
    public function getImage()
    {
        $image = $this->rawData['image_path'];
        if (strpos($image, 'default_thumbnail.png') !== false || strpos($image, 'video_default_photo') !== false) {
            return $this->getDefaultImage();
        }
        return $this->rawData['image_path'];
    }

    public function setStatistic($statistic)
    {
        $statistic->total_score = isset($this->rawData['total_score']) ? $this->rawData['total_score'] : 0;
        $statistic->total_rating = isset($this->rawData['total_rating']) ? $this->rawData['total_rating'] : 0;
        $this->statistic = $statistic;
    }

    public function getIsLiked()
    {
        return empty($this->is_liked) ? false : $this->is_liked;
    }

    public function getCommentTypeId()
    {
        return 'v';
    }

    public function getLikeTypeId()
    {
        return 'v';
    }

    public function getShortFields()
    {
        return [
            'id', 'title', 'description', 'embed_code', 'image', 'creation_date', 'resource_name',
            'is_featured', 'is_sponsor', 'is_liked', 'module_id', 'item_id', 'statistic', 'user', 'duration', 'extra', 'is_pending'
        ];
    }

    protected function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('in_process', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_stream', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_featured', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_spotlight', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_sponsor', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_liked', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_friend', ['type' => ResourceMetadata::BOOL])
            ->mapField('view_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('item_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('page_user_id', ['type' => ResourceMetadata::INTEGER]);
    }

    public function getMobileSettings($params = [])
    {
        $permission = NameResource::instance()->getApiServiceByResourceName($this->resource_name)->getAccessControl()->getPermissions();
        $l = $this->getLocalization();
        return self::createSettingForResource([
            'acl'              => $permission,
            'resource_name'    => $this->resource_name,
            'schema'           => [
                'definition' => [
                    'categories' => 'video_category[]'
                ],
            ],
            'search_input'     => [
                'placeholder' => $l->translate('search_videos'),
            ],
            'forms'            => [
                'addItem'  => [
                    'headerTitle' => $l->translate('share_a_video'),
                    'apiUrl'      => UrlUtility::makeApiUrl('video/form'),
                    'formName'    => 'video/formEditVideo'
                ],
                'editItem' => [
                    'headerTitle' => $l->translate('editing_video'),
                    'apiUrl'      => UrlUtility::makeApiUrl('video/form/:id'),
                    'formName'    => 'video/formEditVideo'
                ],
            ],
            'detail_view'      => [
                'component_name' => 'video_detail',
            ],
            'list_view.tablet' => [
                'numColumns'      => 2,
                'layout'          => Screen::LAYOUT_GRID_VIEW,
                'noItemMessage'   => [
                    'image'     => $this->getAppImage('no-item'),
                    'label'     => $l->translate('no_videos_found'),
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
                'alignment'       => 'top', // top, left, right
                'item_view'       => 'video',
            ],
            'list_view'        => [
                'numColumns'      => 1,
                'noItemMessage'   => [
                    'image'     => $this->getAppImage('no-item'),
                    'label'     => $l->translate('no_videos_found'),
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
                'alignment'       => 'top', // top, left, right
                'item_view'       => 'video',
            ],
            'app_menu'         => [
                ['label' => $l->translate('all_videos'), 'params' => ['initialQuery' => ['view' => '']]],
                ['label' => $l->translate('my_videos'), 'params' => ['initialQuery' => ['view' => 'my']]],
                ['label' => $l->translate('friends_videos'), 'params' => ['initialQuery' => ['view' => 'friend']]],
                ['label' => $l->translate('pending_videos'), 'params' => ['initialQuery' => ['view' => 'pending']], 'acl' => 'can_approve'],
            ],
            'moderation_menu'  => [
                ['label' => $l->translate('approve'), 'value' => Screen::ACTION_APPROVE_ITEMS, 'style' => 'primary', 'show' => 'view==pending', 'acl' => 'can_approve'],
                ['label' => $l->translate('feature'), 'value' => Screen::ACTION_FEATURE_ITEMS, 'style' => 'primary', 'show' => 'view!=pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('remove_feature'), 'value' => Screen::ACTION_REMOVE_FEATURE_ITEMS, 'style' => 'primary', 'show' => 'view!=pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('delete'), 'value' => Screen::ACTION_DELETE_ITEMS, 'style' => 'danger', 'acl' => 'can_delete'],
            ]
        ]);
    }

    public function getFeedDisplay()
    {
        $this->setDisplayFields(['id', 'resource_name', 'title', 'description', 'embed_code', 'image', 'privacy', 'duration']);
        $embed = $this->toArray();
        return $embed;
    }

    public function getDuration()
    {
        if (empty($this->duration)) {
            return $this->duration = null;
        }
        if (strpos($this->duration, ':') === false) {
            $this->duration = Phpfox::getService('v.video')->getDuration($this->duration);
        }
        return $this->duration;
    }

    public function getIsPending()
    {
        $this->is_pending = $this->view_id == 2;
        return $this->is_pending;
    }

    public function getSponsoredDisplay()
    {
        $this->setDisplayFields([
            'resource_name', 'title', 'image', 'statistic', 'user', 'duration', 'embed_code', 'id', 'sponsor_id'
        ]);
        return $this->toArray();
    }

    public function getFeaturedDisplay()
    {
        $this->setDisplayFields([
            'resource_name', 'title', 'image', 'statistic', 'user', 'duration', 'embed_code', 'id'
        ]);
        return $this->toArray();
    }
}
