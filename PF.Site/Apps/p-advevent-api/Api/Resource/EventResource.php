<?php


namespace Apps\P_AdvEventAPI\Api\Resource;


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

use Apps\P_AdvEventAPI\Service\EventApi;
use Apps\P_AdvEventAPI\API\Form\EventMassEmailForm;
use Apps\P_AdvEventAPI\Api\Form\EventSearchForm;

use Phpfox;

class EventResource extends ResourceBase
{
    const RESOURCE_NAME = "fevent";

    const NO_INVITE = -1;
    const INVITED = 0;
    const ATTENDING = 1;
    const MAYBE_ATTEND = 2;
    const NOT_ATTEND = 3;

    public $resource_name = self::RESOURCE_NAME;
    public $module_name = 'fevent';
    /**
     * Custom ID Field Name
     */
    protected $idFieldName = 'event_id';

    public $title;

    public $description;
    public $text;

    public $module_id;
    public $item_id;

    public $view_id;
    public $is_sponsor;
    public $is_featured;
    public $is_friend;
    public $is_liked;
    public $is_pending;
    public $mass_email;
    public $post_types;
    public $profile_menus;

    public $is_notified;

    public $image;
    public $images;

    public $full_address;
    public $location;
    public $country;
    public $province;
    public $postal_code;
    public $city;

    public $start_time_text;
    public $end_time_text;
//    public $start_time_date_text;
//    public $start_time_time_text;
//    public $end_time_date_text;
//    public $end_time_time_text;

//    public $start_time_date;
//    public $start_time_time;
//    public $end_time_date;
//    public $end_time_time;

    public $start_time;
    public $end_time;

    public $start_gmt_offset;
    public $end_gmt_offset;

    public $gmap;
    public $map_image;
    public $map_url;

    public $address;

    public $country_iso;
    public $country_child_id;

    public $rsvp;
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

    // Ticket properties
    public $has_ticket;
    public $ticket_type;
    public $ticket_price;
    public $ticket_url;

    // Ticket properties
    public $has_notification;
    public $notification_type;
    public $notification_value;

    public $isrepeat;
    public $after_number_event;
    public $timerepeat;
    public $query;

    public function getMobileSettings($params = [])
    {
        $permission = NameResource::instance()->getApiServiceByResourceName($this->resource_name)->getAccessControl()->getPermissions();
        $l = $this->getLocalization();
        $searchFilter = (new EventSearchForm());
        $searchFilter->setLocal($l);

        return self::createSettingForResource([
            'acl' => $permission,
            'schema' => [
                'definition' => [
                    'categories' => 'fevent_category[]'
                ]
            ],
            'resource_name' => $this->getResourceName(),
            'search_input' => [
                'placeholder' => $l->translate('search_events'),
            ],
            'sort_menu' => [
                'title'    => $l->translate('sort_by'),
                'options' => $searchFilter->getSortOptions()
            ],
            'filter_menu' => [
                'title'    => $l->translate('filter_by'),
                'options' => $searchFilter->getWhenOptions()
            ],
            'detail_view' => [
                'component_name' => 'event_detail',
            ],
            'list_view.tablet' => [
                'numColumns' => 2,
                'layout' => Screen::LAYOUT_GRID_VIEW,
                'noItemMessage' => $l->translate('no_events_found'),
                'item_view' => 'event',
            ],
            'list_view' => [
                'noItemMessage' => $l->translate('no_events_found'),
                'item_view' => 'event',
            ],
            'feed_view' => [
                'item_view' => 'embed_event'
            ],
            'forms' => [
                'addItem' => [
                    'headerTitle' => $l->translate('create_new_event'),
                    'apiUrl' => UrlUtility::makeApiUrl('fevent/form'),
                ],
                'editItem' => [
                    'headerTitle' => $l->translate('event_details'),
                    'apiUrl' => UrlUtility::makeApiUrl('fevent/form/:id'),
                ],
                'photos' => [
                    'headerTitle' => $l->translate('manage_photos'),
                    'apiUrl' => 'mobile/fevent-photo/form/:id'
                ],
                'invite' => [
                    'headerTitle' => $l->translate('invited_events'),
                    'apiUrl' => 'mobile/fevent-invite/form/:id',
                ],
                'massEmail' => [
                    'headerTitle' => $l->translate('mass_email'),
                    'apiUrl' => 'mobile/fevent/mass-email-form/:id',
                ],
                'editAdmin' => [
                    'submitApiUrl' => UrlUtility::makeApiUrl('fevent-admin/:id'),
                    'submitMethod' => 'put',
                    'itemType' => 'fevent-admin'
                ],
            ],
            'membership_menu' => [
                ['label' => $l->translate('attending'), 'value' => 'fevent/rsvp/attending'],
                ['label' => $l->translate('not_attending'), 'value' => 'fevent/rsvp/notAttending'],
            ],
            'action_menu' => [
                ['value' => Screen::ACTION_EDIT_ITEM, 'label' => $l->translate('edit_event'), 'acl' => 'can_edit'],
                ['label' => $l->translate('manage_photos'), 'value' => 'fevent/photos', 'acl' => 'can_edit'],
                ['label' => $l->translate('invite_guest'), 'value' => 'fevent/invite', 'acl' => 'can_invite'],
//                ['label' => $l->translate('guest_list'), 'value' => 'fevent/guest_list', 'acl' => 'can_edit'],
                ['label' => $l->translate('mass_email'), 'value' => 'fevent/mass_email', 'acl' => 'can_mass_email'],
                ['label' => $l->translate('admins'), 'value' => 'fevent/manage_admins', 'acl' => 'can_edit'],
                ['label' => $l->translate('approve'), 'value' => Screen::ACTION_APPROVE_ITEM, 'show' => 'is_pending', 'acl' => 'can_approve'],
                ['label' => $l->translate('feature'), 'value' => Screen::ACTION_FEATURE_ITEM, 'show' => '!is_featured&&!is_pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('unfeature'), 'value' => Screen::ACTION_FEATURE_ITEM, 'show' => 'is_featured&&!is_pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('sponsor'), 'value' => Screen::ACTION_SPONSOR_ITEM, 'show' => '!is_sponsor&&!is_pending', 'acl' => 'can_sponsor'],
                ['label' => $l->translate('unsponsor'), 'value' => Screen::ACTION_SPONSOR_ITEM, 'show' => 'is_sponsor&&!is_pending', 'acl' => 'can_sponsor'],
                ['label' => $l->translate('report'), 'value' => Screen::ACTION_REPORT_ITEM, 'show' => '!is_owner', 'acl' => 'can_report'],
                ['label' => $l->translate('delete_event'), 'value' => Screen::ACTION_DELETE_ITEM, 'style' => 'danger', 'acl' => 'can_delete'],
            ],
            'app_menu' => [
                ['label' => $l->translate('all_events'), 'params' => ['initialQuery' => ['view' => '']]],
                ['label' => $l->translate('my_events'), 'params' => ['initialQuery' => ['view' => 'my']],],
                ['label' => $l->translate('friends_events'), 'params' => ['initialQuery' => ['view' => 'friend']],],
                ['label' => $l->translate('events_i_m_attending'), 'params' => ['initialQuery' => ['view' => 'attending']],],
                ['label' => $l->translate('events_i_may_attend'), 'params' => ['initialQuery' => ['view' => 'may-attend']],],
                ['label' => $l->translate('events_invites'), 'params' => ['initialQuery' => ['view' => 'invites']],],
                ['label' => $l->translate('sponsored_events'), 'params' => ['initialQuery' => ['view' => 'sponsor']],],
                ['label' => $l->translate('featured_events'), 'params' => ['initialQuery' => ['view' => 'feature']],],
                ['label' => $l->translate('pending_events'), 'params' => ['initialQuery' => ['view' => 'pending']], 'acl' => 'can_approve'],
            ],
            'settings' => [
                'time_format' => Phpfox::getParam('event.event_time_format')
            ]
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
            ->mapField('is_notified', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_pending', ['type' => ResourceMetadata::BOOL])
            ->mapField('country_child_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('has_ticket', ['type' => ResourceMetadata::BOOL])
            ->mapField('isrepeat', ['type' => ResourceMetadata::INTEGER]);
    }

    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return Phpfox::permalink('fevent', $this->id, $this->title);
    }

    public function getPostTypes()
    {
        return (new EventApi())->getPostTypes($this->getId());
    }

    public function getProfileMenus()
    {
        return [];
    }

    public function setStatistic($statistic)
    {
        $statistic->total_attending = (int)Phpfox::getService('fevent')->getNumbersOfAttendee($this->id, 1);
        $statistic->total_maybe_attending = (int)Phpfox::getService('fevent')->getNumbersOfAttendee($this->id, 2);
        $statistic->total_awaiting_reply = (int)Phpfox::getService('fevent')->getNumbersOfAttendee($this->id, 0);
        $this->statistic = $statistic;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        if (empty($this->categories) || isset($this->rawData['categories'])) {
            $this->categories = NameResource::instance()
                ->getApiServiceByResourceName(EventCategoryResource::RESOURCE_NAME)
                ->getByEventId($this->id);
        }
        return $this->categories;
    }

    /**
     * @return Image|array|string
     */
    public function getImage()
    {
        $sizes = Phpfox::getParam('fevent.thumbnail_sizes');

        if (!empty($this->rawData['image_path'])) {
            return $this->rawData['image_path'];
        } else {
            return $this->getDefaultImage();
        }
    }

    public function getFeedImage()
    {
        if (!empty($this->rawData['image_path'])) {
            $aSizes = Phpfox::getParam('fevent.thumbnail_sizes');
            return Image::createFrom([
                'file' => $this->rawData['image_path'],
                'server_id' => $this->rawData['server_id'],
                'path' => 'event.url_image'
            ], $aSizes)->toArray();
        }

        return $this->getDefaultImage();
    }

    public function getImages()
    {
        if (empty($this->images) && !empty($this->rawData['images_list'])) {
            $images = [];
            $sizes = Phpfox::getParam('fevent.thumbnail_sizes');
            foreach ($this->rawData['images_list'] as $image) {
                $images[] = Image::createFrom([
                    'file' => $image['image_path'],
                    'server_id' => $image['server_id'],
                    'path' => 'event.url_image'
                ], $sizes)->toArray();
            }
            $this->images = $images;
        }
        return $this->images;
    }

//    public function getStartTimeDate()
//    {
//        if ($this->start_time) {
//            $startTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->start_time, Phpfox::getTimeZone());
//            $this->start_time_date = date('Y-m-d', $startTimeEdit);
//        }
//        return $this->start_time_date;
//    }
//
//    public function getStartTimeTime()
//    {
//        if ($this->start_time) {
//            $startTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->start_time, Phpfox::getTimeZone());
//            $this->start_time_time = date('H:i', $startTimeEdit);
//        }
//        return $this->start_time_time;
//    }
//
//    public function getEndTimeDate()
//    {
//        if ($this->end_time) {
//            $endTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->end_time, Phpfox::getTimeZone());
//            $this->end_time_date = date('Y-m-d', $endTimeEdit);
//        }
//        return $this->end_time_date;
//    }
//
//    public function getEndTimeTime()
//    {
//        if ($this->end_time) {
//            $endTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->end_time, Phpfox::getTimeZone());
//            $this->end_time_time = date('H:i', $endTimeEdit);
//        }
//        return $this->end_time_time;
//    }

    public function getStartTime()
    {
        if ($this->start_time) {
            $this->start_time = $this->convertDatetime($this->start_time);
        }
        return $this->start_time;
    }

    public function getEndTime()
    {
        if ($this->end_time) {
            $this->end_time = $this->convertDatetime($this->end_time);
        }
        return $this->end_time;
    }

    public function getStartTimeText()
    {
        if ($this->start_time) {
            $startTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->start_time, Phpfox::getTimeZone());
            $this->start_time_text = date('D, M d, Y g:iA', $startTimeEdit);
        }

        return $this->start_time_text;
    }

    public function getEndTimeText()
    {
        if ($this->end_time) {
            $endTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->end_time, Phpfox::getTimeZone());
            $this->end_time_text = date('D, M d, Y g:iA', $endTimeEdit);
        }

        return $this->end_time_text;
    }

//    public function getStartTimeDateText()
//    {
//        if ($this->start_time) {
//            $startTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->start_time, Phpfox::getTimeZone());
//            $this->start_time_date_text = date('D, M d, Y', $startTimeEdit);
//        }
//
//        return $this->start_time_date_text;
//    }
//
//    public function getStartTimeTimeText()
//    {
//        if ($this->start_time) {
//            $startTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->start_time, Phpfox::getTimeZone());
//            $this->start_time_time_text = date('g:iA', $startTimeEdit);
//        }
//
//        return $this->start_time_time_text;
//    }
//
//    public function getEndTimeDateText()
//    {
//        if ($this->end_time) {
//            $endTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->end_time, Phpfox::getTimeZone());
//            $this->end_time_date_text = date('D, M d, Y', $endTimeEdit);
//        }
//
//        return $this->end_time_date_text;
//    }
//
//    public function getEndTimeTimeText()
//    {
//        if ($this->end_time) {
//            $endTimeEdit = Phpfox::getLib('date')->convertFromGmt($this->end_time, Phpfox::getTimeZone());
//            $this->end_time_time_text = date('g:iA', $endTimeEdit);
//        }
//
//        return $this->end_time_time_text;
//    }

    public function getTimerepeat()
    {
        if ($this->timerepeat) {
            $this->timerepeat = $this->convertDatetime($this->timerepeat);
        }
        return $this->timerepeat;
    }

    public function getCountry()
    {
        $this->country = '';
        if (!empty($this->rawData['event_country_iso'])) {
            $this->country = html_entity_decode(Phpfox::getService('core.country')->getCountry($this->rawData['event_country_iso']), ENT_QUOTES);
        }

        return $this->country;
    }

    public function getCountryIso()
    {
        if (!empty($this->event_country_iso)) {
            $this->country_iso = $this->event_country_iso;
        }

        return $this->country_iso;
    }

    public function getProvince()
    {
        $this->province = '';
        if (!empty($this->rawData['country_child_id'])) {
            $this->province = html_entity_decode(Phpfox::getService('core.country')->getChild($this->rawData['country_child_id']), ENT_QUOTES);
        }
        return $this->province;
    }


    public function getMapImage()
    {
        $apiKey = Phpfox::getParam('core.google_api_key');

        if (empty($apiKey)) {
            return null;
        }

        if (empty($this->rawData['map_location'])) {
            return null;
        }

        $center = $this->rawData['map_location'];
        $extra = [
            'center' => $center,
            'zoom' => 16,
            'sensor' => 'false',
            'size' => '600x200',
            'maptype' => 'roadmap',
            'key' => $apiKey,
            'scale' => 2,
            'markers' => 'size:small|color:red|' . $center,
        ];
        return (PHPFOX_IS_HTTPS ? 'https' : 'http') . '://maps.googleapis.com/maps/api/staticmap?' . http_build_query($extra);
    }

    public function getMapUrl()
    {
        if (!empty($this->rawData['map_location'])) {
            return (PHPFOX_IS_HTTPS ? 'https' : 'http') . '://maps.google.com/?q=' . $this->rawData['map_location'];
        }
        return null;
    }

    public function getIsPending()
    {
        $this->is_pending = $this->view_id == 1;
        return $this->is_pending;
    }

    public function getText()
    {
        if (empty($this->text) && !empty($this->rawData['description'])) {
            $this->text = TextFilter::pureHtml($this->rawData['description'], true);
        }
        return $this->text;
    }

    public function getDescription()
    {
        if (empty($this->description) && !empty($this->rawData['description'])) {
            $this->description = $this->rawData['description'];
        }
        TextFilter::pureText($this->description, null, true);
        return $this->description;
    }


    public function getAddress()
    {
        $this->address = !empty($this->rawData['address']) ? $this->rawData['address'] : null;
        return $this->address;
    }

    public function getFullAddress()
    {
        $this->full_address = $this->location;
        $hasFirst = false;
        if (!empty($this->address)) {
            $this->full_address .= ' - ' . $this->address;
            $hasFirst = true;
        }
        if (!empty($this->city)) {
            $this->full_address .= (!$hasFirst ? ' - ' : ', ') . $this->city;
            $hasFirst = true;
        }
        $country = $this->getCountry();
        if (!empty($country)) {
            $this->full_address .= (!$hasFirst ? ' - ' : ', ') . $country;
            $hasFirst = true;
        }
        $province = $this->getProvince();
        if (!empty($province)) {
            $this->full_address .= (!$hasFirst ? ' - ' : ', ') . $province;
        }
        return $this->full_address;
    }

    public function getTags()
    {
        if (empty($this->tags)) {
            $originalTags = Phpfox::getService('tag')->getTagsById('fevent', $this->id);
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

    public function getRsvp()
    {
        if (empty($this->rsvp)) {
            $rsvp = NameResource::instance()->getApiServiceByResourceName(EventInviteResource::RESOURCE_NAME)->getUserInvite($this->id, Phpfox::getUserId());
            if (!empty($rsvp)) {
                switch ($rsvp['rsvp_id']) {
                    case 0:
                        $this->rsvp = self::INVITED;
                        break;
                    case 1:
                        $this->rsvp = self::ATTENDING;
                        break;
                    case 2:
                        $this->rsvp = self::MAYBE_ATTEND;
                        break;
                    case 3:
                        $this->rsvp = self::NOT_ATTEND;
                        break;
                }
            } else {
                $this->rsvp = self::NO_INVITE;
            }
        }
        return $this->rsvp;
    }

    public function getFeedDisplay()
    {
        $this->setDisplayFields(['id', 'resource_name', 'title', 'description', 'image', 'categories', 'privacy', 'start_time', 'end_time', 'location', 'statistic']);
        $eventFeed = $this->toArray();
        $eventFeed['image'] = $this->getFeedImage();
        $eventFeed['time_format'] = Phpfox::getParam('event.event_time_format');
        return $eventFeed;
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

    public function getTicketType()
    {
        if (!$this->has_ticket) {
            $this->ticket_type = 'no_ticket';
        }
        return $this->ticket_type;
    }

    public function getQuery()
    {
        return ['id' => $this->getId()];
    }
}
