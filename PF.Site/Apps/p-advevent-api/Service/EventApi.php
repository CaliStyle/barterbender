<?php


namespace Apps\P_AdvEventAPI\Service;

use Apps\Core_MobileApi\Api\Form\Validator\DateTimeFormatValidator;
use Apps\Core_MobileApi\Api\Security\Blog\BlogAccessControl;
use Apps\Core_MobileApi\Api\Security\Music\MusicPlaylistAccessControl;
use Apps\P_AdvEventAPI\Api\Resource\EventResource;
use Apps\P_AdvEventAPI\Api\Resource\EventCategoryResource;
use Apps\P_AdvEventAPI\Api\Security\EventAccessControl;
use Apps\P_AdvEventAPI\Api\Resource\EventInviteResource;
use Apps\P_AdvEventAPI\Api\Resource\EventPhotoResource;
use Apps\P_AdvEventAPI\Api\Resource\EventAdminResource;
use Apps\P_AdvEventAPI\Api\Form\EventForm;
use Apps\P_AdvEventAPI\Api\Form\EventMassEmailForm;

use Phpfox;
use Phpfox_Database;
use Apps\Core_MobileApi\Adapter\MobileApp\MobileApp;
use Apps\Core_MobileApi\Adapter\MobileApp\MobileAppSettingInterface;
use Apps\Core_MobileApi\Adapter\MobileApp\Screen;
use Apps\Core_MobileApi\Adapter\MobileApp\ScreenSetting;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\Core_MobileApi\Api\ActivityFeedInterface;
use Apps\Core_MobileApi\Api\Resource\FeedResource;
use Apps\Core_MobileApi\Api\Resource\Object\HyperLink;
use Apps\Core_MobileApi\Service\Helper\Pagination;
use Apps\Core_MobileApi\Api\Security\AppContextFactory;
use Apps\Core_MobileApi\Service\NameResource;
use Apps\Core_MobileApi\Api\Resource\TagResource;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;

class EventApi extends AbstractResourceApi implements ActivityFeedInterface, MobileAppSettingInterface
{
    private $eventService;

    private $categoryService;

    private $processService;

    private $helperService;

    private $browserService;

    private $userService;

    private $adProcessService = null;

    public function __construct()
    {
        parent::__construct();

        $this->eventService = Phpfox::getService('fevent');
        $this->categoryService = Phpfox::getService('fevent.category');
        $this->helperService = Phpfox::getService('fevent.helper');
        $this->processService = Phpfox::getService('fevent.process');
        $this->browserService = Phpfox::getService('fevent.browse');
        $this->userService = Phpfox::getService('user');

        if (Phpfox::isAppActive('Core_BetterAds')) {
            $this->adProcessService = Phpfox::getService('ad.process');
        }
    }

    public function __naming()
    {
        return [
            'fevent/mass-email-form/:id' => [
                'get' => 'getMassEmailForm',
                'where' => [
                    'id' => '(\d+)'
                ]
            ],
            'fevent/mass-email' => [
                'post' => 'sendMassEmail',
                'where' => [
                    'id' => '(\d+)'
                ]
            ],
        ];
    }

    public function getAppSetting($param)
    {
        $l = $this->getLocalization();
        $app = new MobileApp('fevent', [
            'title' => $l->translate('menu_fevent_events'),
            'home_view' => 'menu',
            'main_resource' => new EventResource([]),
            'category_resource' => new EventCategoryResource([]),
            'other_resources' => [
                new FeedResource([]),
                new EventPhotoResource([]),
                new EventInviteResource([]),
                new EventAdminResource([])
            ],
        ]);
        $resourceName = (new EventResource([]))->getResourceName();

        $headerButtons[$resourceName] = [
            [
                'icon' => 'list-bullet-o',
                'action' => Screen::ACTION_FILTER_BY_CATEGORY,
            ],
        ];

        if ($this->getAccessControl()->isGranted(EventAccessControl::ADD)) {
            $headerButtons[$resourceName][] = [
                'icon' => 'plus',
                'action' => Screen::ACTION_ADD,
                'params' => [
                    'module_name' => 'fevent',
                    'resource_name' => $resourceName
                ]
            ];
        }

        $app->addSetting('home.header_buttons', $headerButtons);

        return $app;
    }

    public function getScreenSetting($param)
    {
        $l = $this->getLocalization();
        $screenSetting = new ScreenSetting('fevent', []);
        $resourceName = EventResource::populate([])->getResourceName();
        $screenSetting->addSetting($resourceName, ScreenSetting::MODULE_HOME);
        $screenSetting->addSetting($resourceName, ScreenSetting::MODULE_LISTING);
        $screenSetting->addSetting($resourceName, ScreenSetting::MODULE_DETAIL, [
            ScreenSetting::LOCATION_HEADER => [
                'component' => 'item_header',
                'transition' => 'transparent'
            ],
            ScreenSetting::LOCATION_MAIN => [
                'component' => ScreenSetting::STREAM_PROFILE_FEEDS,
                'embedComponents' => [
                    'stream_event_header_info',
                    'stream_profile_description',
                    'stream_composer'
                ]
            ],
            ScreenSetting::LOCATION_RIGHT => [
                'component' => 'simple_list_block',
                'module_name' => 'fevent',
                'resource_name' => $resourceName,
                'title' => 'events',
                'query' => ['sort' => 'upcoming']
            ],
            'screen_title' => $l->translate('events') . ' > ' . $l->translate('event') . ' - ' . $l->translate('mobile_detail_page')
        ]);
        $screenSetting->addSetting($resourceName, 'smartFeventFeventGuest', [
            ScreenSetting::LOCATION_HEADER => [
                'component' => ScreenSetting::SIMPLE_HEADER,
                'title' => 'guests'
            ],
            ScreenSetting::LOCATION_MAIN => [
                'component' => ScreenSetting::SMART_TABS,
                'tabs' => [
                    [
                        'label' => 'attending',
                        'component' => ScreenSetting::SMART_RESOURCE_LIST,
                        'module_name' => 'fevent',
                        'resource_name' => EventInviteResource::populate([])->getResourceName(),
                        'item_view' => 'event_invite',
                        'search' => true,
                        'use_query' => ['rsvp_id' => 1, 'event_id' => ':id']
                    ],
                    [
                        'label' => 'maybe',
                        'component' => ScreenSetting::SMART_RESOURCE_LIST,
                        'module_name' => 'fevent',
                        'resource_name' => EventInviteResource::populate([])->getResourceName(),
                        'item_view' => 'event_invite',
                        'search' => true,
                        'use_query' => ['rsvp_id' => 2, 'event_id' => ':id']
                    ],
                    [
                        'label' => 'awaiting',
                        'component' => ScreenSetting::SMART_RESOURCE_LIST,
                        'module_name' => 'fevent',
                        'resource_name' => EventInviteResource::populate([])->getResourceName(),
                        'item_view' => 'event_invite',
                        'search' => true,
                        'use_query' => ['rsvp_id' => 0, 'event_id' => ':id']
                    ],
                    [
                        'label' => 'not_attending',
                        'component' => ScreenSetting::SMART_RESOURCE_LIST,
                        'module_name' => 'fevent',
                        'resource_name' => EventInviteResource::populate([])->getResourceName(),
                        'item_view' => 'event_invite',
                        'search' => true,
                        'use_query' => ['rsvp_id' => 3, 'event_id' => ':id']
                    ]
                ],
            ],
            'screen_title' => $l->translate('events') . ' > ' . $l->translate('guests'),
        ]);

        $screenSetting->addBlock($resourceName, ScreenSetting::MODULE_HOME, ScreenSetting::LOCATION_RIGHT, [
            [
                'component' => ScreenSetting::SIMPLE_LISTING_BLOCK,
                'title' => $l->translate('featured_events'),
                'resource_name' => $resourceName,
                'module_name' => 'fevent',
                'refresh_time' => 3000, //secs
                'query' => ['view' => 'feature']
            ],
            [
                'component' => ScreenSetting::SIMPLE_LISTING_BLOCK,
                'title' => $l->translate('sponsored_event'),
                'resource_name' => $resourceName,
                'module_name' => 'fevent',
                'refresh_time' => 3000, //secs
                'item_props' => [
                    'click_ref' => '@view_sponsor_item',
                ],
                'query' => ['view' => 'sponsor']
            ]
        ]);
        return $screenSetting;
    }

    function findAll($params = [])
    {
        $this->denyAccessUnlessGranted(EventAccessControl::VIEW);

        $params = $this->resolver->setDefined([
            'view',
            'module_id',
            'item_id',
            'category',
            'q',
            'sort',
            'profile_id',
            'limit',
            'page',
            'when',
            'location'
        ])
            ->setAllowedValues('sort', ['latest', 'most_viewed', 'most_liked', 'most_discussed'])
            ->setAllowedValues('view',
                ['my', 'pending', 'friend', 'attending', 'may-attend', 'not-attending', 'invites', 'sponsor', 'feature'])
            ->setAllowedValues('when', ['all-time', 'today', 'tomorrow', 'this-week', 'this-weekend', 'next-week', 'this-month', 'upcoming', 'ongoing'])
            ->setAllowedTypes('limit', 'int', [
                'min' => Pagination::DEFAULT_MIN_ITEM_PER_PAGE,
                'max' => Pagination::DEFAULT_MAX_ITEM_PER_PAGE
            ])
            ->setAllowedTypes('category', 'int')
            ->setAllowedTypes('page', 'int')
            ->setAllowedTypes('profile_id', 'int')
            ->setAllowedTypes('item_id', 'int')
            ->setDefault([
                'limit' => Pagination::DEFAULT_ITEM_PER_PAGE,
                'page' => 1
            ])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            $this->validationParamsError($this->resolver->getInvalidParameters());
        }

        $view = $params['view'];
        $sort = $params['sort'];
        $when = $params['when'];

        $parentModule = null;
        if (!empty($params['module_id']) && !empty($params['item_id'])) {
            $parentModule = [
                'module_id' => $params['module_id'],
                'item_id' => $params['item_id'],
            ];
        }
        $isProfile = $params['profile_id'];
        if ($isProfile) {
            $user = $this->userService->get($isProfile);
            if (empty($user)) {
                return $this->notFoundError();
            }
            $this->search()->setCondition('AND m.user_id = ' . $user['user_id']);
        }
        $this->search()->setBIsIgnoredBlocked(true);
        $browseParams = [
            'module_id' => 'fevent',
            'alias' => 'm',
            'field' => 'event_id',
            'table' => Phpfox::getT('fevent'),
            'hide_view' => ['pending', 'my'],
            'service' => 'fevent.browse',
        ];

        // sort
        switch ($sort) {
            case 'most_viewed':
                $sort = 'm.total_view DESC';
                break;
            case 'most_liked':
                $sort = 'm.total_like DESC';
                break;
            case 'most_discussed':
                $sort = 'm.total_comment DESC';
                break;
            default:
                switch ($when) {
                    case 'ongoing':
                        $sort = 'm.start_time DESC';
                        break;
                    case 'upcoming':
                    case 'today':
                    case 'tomorrow':
                    case 'this-week':
                    case 'this-weekend':
                    case 'next-week':
                    case 'this-month':
                        $sort = 'm.start_time ASC';
                        break;
                    case 'past':
                        $sort = 'm.end_time DESC';
                        break;
                    default:
                        $sort = 'm.start_time DESC';
                        break;
                }
                break;
        }

        switch ($view) {
            case 'pending':
                if (Phpfox::getUserParam('fevent.can_approve_events')) {
                    $this->search()->setCondition('AND m.view_id = 1');
                } else {
                    return $this->permissionError();
                }
                break;
            case 'my':
                if (Phpfox::isUser()) {
                    $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
                } else {
                    return $this->permissionError();
                }
                break;
            case 'sponsor':
                $this->search()->setCondition('AND m.is_sponsor = 1');
                break;
            case 'feature':
                $this->search()->setCondition('AND m.is_featured = 1');
                break;
            default:
                if ($parentModule !== null) {
                    $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.module_id = \'' . Phpfox_Database::instance()->escape($parentModule['module_id']) . '\' AND m.item_id = ' . (int)$parentModule['item_id'] . '');
                } else {
                    switch ($view) {
                        case 'attending':
                            $this->browserService->attending(1);
                            break;
                        case 'may-attend':
                            $this->browserService->attending(2);
                            break;
                        case 'not-attending':
                            $this->browserService->attending(3);
                            break;
                        case 'invites':
                            $this->browserService->attending(0);
                            break;
                    }

                    if ($view == 'attending' || $view === 'invites' || $view == 'may-attend') {
                        $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)');
                    } else {
                        if ((Phpfox::getParam('fevent.fevent_display_event_created_in_page') || Phpfox::getParam('fevent.fevent_display_event_created_in_group'))) {
                            $aModules = ['fevent'];
                            if (Phpfox::getParam('fevent.fevent_display_event_created_in_group') && Phpfox::isAppActive('PHPfox_Groups')) {
                                $aModules[] = 'groups';
                            }
                            if (Phpfox::getParam('fevent.fevent_display_event_created_in_page') && Phpfox::isAppActive('Core_Pages')) {
                                $aModules[] = 'pages';
                            }
                            if (count($aModules)) {
                                $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND (m.module_id IN ("' . implode('","',
                                        $aModules) . '") OR m.module_id = \'event\')');
                            } else {
                                $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.module_id = \'fevent\'');
                            }
                        } else {
                            $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.item_id = 0');
                        }
                    }
                }
                break;
        }
        //location
        if ($params['location']) {
            $this->search()->setCondition('AND m.country_iso = \'' . Phpfox_Database::instance()->escape($params['location']) . '\'');
        }
        // search
        if (!empty($params['q'])) {
            $this->search()->setCondition('AND m.title LIKE "' . Phpfox::getLib('parse.input')->clean('%' . $params['q'] . '%') . '"');
        }

        //category
        if ($params['category']) {
            $this->browserService->category($params['category']);
            $childCategoryIds= $this->categoryService->getChildIds($params['category']);
            $where = '('. (int)$params['category'] . (!empty($childCategoryIds) ? ','. trim($childCategoryIds,',') : ''). ')';
            $this->search()->setCondition('AND mcd.category_id IN ' . $where);
        }

        //when
        if ($when) {
            $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
            switch ($when) {
                case 'today':
                    $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'),
                        Phpfox::getTime('Y'));
                    $this->search()->setCondition(' AND (m.start_time >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND m.start_time < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')');
                    break;
                case 'tomorrow':
                    $searchStart = strtotime('tomorrow');
                    $searchEnd = strtotime('tomorrow +1 day');
                    $this->search()->setCondition(" AND ($searchStart <= m.start_time AND $searchEnd >= m.start_time)");
                    break;
                case 'this-week':
                    $this->search()->setCondition(' AND m.start_time >= ' . (int)Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart()));
                    $this->search()->setCondition(' AND m.start_time <= ' . (int)Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd()));
                    break;
                case 'this-weekend':
                    $searchStart = strtotime('saturday');
                    $searchEnd = strtotime('sunday +1 day');
                    $this->search()->setCondition(" AND ($searchStart <= m.start_time AND $searchEnd >= m.start_time)");
                    break;
                case 'next-week':
                    $searchStart = strtotime('next monday');
                    $searchEnd = strtotime('next monday +7 days');
                    $this->search()->setCondition(" AND ($searchStart <= m.start_time AND $searchEnd >= m.start_time)");
                    break;
                case 'this-month':
                    $this->search()->setCondition(' AND m.start_time >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth()) . '\'');
                    $iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'),
                        Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
                    $this->search()->setCondition(' AND m.start_time <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'');
                    break;
                case 'upcoming':
                    $this->search()->setCondition(' AND m.start_time >= \'' . PHPFOX_TIME . '\'');
                    break;
                case 'ongoing':
                    $this->search()->setCondition(' AND m.start_time <= \'' . PHPFOX_TIME . '\'');
                    $this->search()->setCondition(' AND m.end_time > \'' . PHPFOX_TIME . '\'');
                    break;
                default:
                    break;
            }
        }

        $this->search()->setSort($sort)->setLimit($params['limit'])->setPage($params['page']);

        $this->browse()->params($browseParams)->execute();

        $items = $this->browse()->getRows();

        if ($items) {
            $this->processRows($items);
        }

        return $this->success($items);
    }

    function findOne($params)
    {
        $id = $this->resolver->resolveId($params);

        $item = $this->eventService->getEvent($id);

        if (!$item) {
            return $this->notFoundError();
        }

        if ($item['image_path'] == '') {
            $item['image_path'] = $this->eventService->getDefaultPhoto(false);
        } else {
            $item['image_path'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => isset($item['event_server_id']) ? $item['event_server_id'] : $item['server_id'],
                'path' => 'event.url_image',
                'file' => $item['image_path'],
                'suffix' => '',
                'return_url' => true
            ));
        }
        $item['images_list'] = $this->eventService->getImages($item['event_id']);
        $item['is_detail'] = true;

        $resource = $this->populateResource(EventResource::class, $item);
        $this->denyAccessUnlessGranted(EventAccessControl::VIEW, $resource);

        $this->setHyperlinks($resource, true);

        // Increment the view counter
        $this->processService->updateView($item['event_id']);

        $resource->setExtra($this->getAccessControl()->getPermissions($resource));

        return $this->success($resource->lazyLoad(['user'])->loadFeedParam()->toArray());
    }

    function create($params)
    {
        $this->denyAccessUnlessGranted(EventAccessControl::ADD);

        if (($iFlood = $this->getSetting()->getUserSetting('fevent.flood_control_events')) !== 0) {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT('fevent'), // Database table we plan to check
                    'condition' => 'user_id = ' . $this->getUser()->getId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                return $this->error($this->getLocalization()->translate('you_are_creating_an_event_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
            }
        }

        /** @var EventForm $form */
        $form = $this->createForm(EventForm::class);

        if ($form->isValid()) {
            $values = $form->getValues();
            $id = $this->processCreate($values);
            if ($id) {
                return $this->success([
                    'id' => $id,
                    'resource_name' => EventResource::populate([])->getResourceName()
                ], [], $this->getLocalization()->translate('event_successfully_added'));
            } else {
                return $this->error($this->getErrorMessage());
            }
        } else {
            return $this->validationParamsError($form->getInvalidFields());
        }
    }

    /**
     * @param $values
     * @return int
     */
    private function processCreate($values)
    {
        $this->convertSubmitForm($values);
        return $this->processService->add($values, $values['module_id'], $values['item_id']);
    }

    private function convertSubmitForm(&$vals, $edit = false)
    {
        $allowChangeDate = $this->setting->getAppSetting('fevent.allow_change_date_recurrent_event');
        $allowChangeTime = $this->setting->getAppSetting('fevent.allow_change_time_recurrent_event');
        $isRepeatEvent = isset($vals['isrepeat']) && $vals['isrepeat'] > -1;
        $event = null;

        $inValid = [];

        if (!empty($vals['categories']) && is_array($vals['categories'])) {
            $vals['category'] = end($vals['categories']);
            $parentCategory = $this->categoryService->getParentCategoryId($vals['category']);
            if ($parentCategory === '') {
                return $this->error($this->getLocalization()->translate('category_not_found'));
            }
        }

        if (!$edit) {
            $vals['temp_file'] = (!empty($vals['file']) && !empty($vals['file']['temp_file'])) ? $vals['file']['temp_file'] : 0;
            $aFile = Phpfox::getService('core.temp-file')->get($vals['temp_file']);
            if (empty($aFile)) {
                $inValid[] = 'file';
            }
        } else {
            $event = $this->loadResourceById($vals['event_id'], true)->toArray();
            $isRepeatEvent = isset($event['isrepeat']) && $event['isrepeat'] > -1; // make sure get the correct data
        }

//        if ($edit && $isRepeatEvent && (!$allowChangeDate || !$allowChangeTime)) {
//            $startTimeDate = (new \DateTime($event['start_time_date']));
//            $endTimeDate = (new \DateTime($event['end_time_date']));
//            $startTimeTime = (new \DateTime($event['start_time_time']));
//            $endTimeTime = (new \DateTime($event['end_time_time']));
//
//            if ($allowChangeDate) {
//                $startTimeDate = (new \DateTime($vals['start_time_date']));
//                if (empty($startTimeDate)) {
//                    $inValid[] = 'start_time_date';
//                }
//                $endTimeDate = (new \DateTime($vals['end_time_date']));
//                if (empty($endTimeDate)) {
//                    $inValid[] = 'end_time_date';
//                }
//            }
//            if ($allowChangeTime) {
//                $startTimeTime = (new \DateTime($vals['start_time_time']));
//                if (empty($startTimeTime)) {
//                    $inValid[] = 'start_time_time';
//                }
//                $endTimeTime = (new \DateTime($vals['end_time_time']));
//                if (empty($endTimeTime)) {
//                    $inValid[] = 'end_time_time';
//                }
//            }
//        } else {
//            $startTimeDate = (new \DateTime($vals['start_time_date']));
//            if (empty($startTimeDate)) {
//                $inValid[] = 'start_time_date';
//            }
//            $startTimeTime = (new \DateTime($vals['start_time_time']));
//            if (empty($startTimeTime)) {
//                $inValid[] = 'start_time_time';
//            }
//            $endTimeDate = (new \DateTime($vals['end_time_date']));
//            if (empty($endTimeDate)) {
//                $inValid[] = 'end_time_date';
//            }
//            $endTimeTime = (new \DateTime($vals['end_time_time']));
//            if (empty($endTimeTime)) {
//                $inValid[] = 'end_time_time';
//            }
//        }

//        $startTime = new \DateTime($startTimeDate->format('Y-m-d') . ' ' . $startTimeTime->format('H:i:s'));
//        $endTime = new \DateTime($endTimeDate->format('Y-m-d') . ' ' . $endTimeTime->format('H:i:s'));

        $startTime = (new \DateTime($vals['start_time']));
        if (empty($startTime)) {
            $inValid[] = 'start_time';
        }

        $endTime = (new \DateTime($vals['end_time']));
        if (empty($endTime)) {
            $inValid[] = 'end_time';
        }

        if (!empty($inValid)) {
            return $this->validationParamsError($inValid);
        }

        if ($endTime <= $startTime) {
            $endTime = $startTime;
            $endTime->modify('+1 hour');
        }

        $vals['start_month'] = $startTime->format('m');
        $vals['start_day'] = $startTime->format('d');
        $vals['start_year'] = $startTime->format('Y');
        $vals['start_hour'] = $startTime->format('H');
        $vals['start_minute'] = $startTime->format('i');
        $vals['start_time'] = $vals['start_hour'] . ':' . $vals['start_minute'];

        $vals['end_month'] = $endTime->format('m');
        $vals['end_day'] = $endTime->format('d');
        $vals['end_year'] = $endTime->format('Y');
        $vals['end_hour'] = $endTime->format('H');
        $vals['end_minute'] = $endTime->format('i');
        $vals['end_time'] = $vals['end_hour'] . ':' . $vals['end_minute'];

        if (!empty($vals['text'])) {
            $vals['description'] = $vals['text'];
        }
        if (!empty($vals['file'])) {
            if (!$edit) {
                if (!empty($vals['file']['temp_file'])) {
                    $vals['temp_file'] = $vals['file']['temp_file'];
                }
            } else {
                if ($vals['file']['status'] == FileType::NEW_UPLOAD || $vals['file']['status'] == FileType::CHANGE) {
                    $vals['temp_file'] = $vals['file']['temp_file'];
                } elseif ($vals['file']['status'] == FileType::REMOVE) {
                    $vals['remove_photo'] = 1;
                }
            }
        }
        if (!$edit) {
            if (empty($vals['module_id'])) {
                $vals['module_id'] = 'fevent';
            }
            if (empty($vals['item_id'])) {
                $vals['item_id'] = 0;
            }
        }
        if (!empty($vals['attachment'])) {
            $vals['attachment'] = implode(",", $vals['attachment']);
        }

        $vals['has_ticket'] = $vals['ticket_type'] == 'no_ticket' ? 0 : 1;
        $vals['has_notification'] = $vals['notification_type'] == 'no_remind' ? 0 : 1;

        if (!empty($vals['after_number_event'])) {
            $vals['repeat_section_end_repeat'] = 'after_number_event';
            $vals['repeat_section_after_number_event'] = $vals['after_number_event'];
        } elseif (!empty($vals['timerepeat'])) {
            $vals['repeat_section_end_repeat'] = 'repeat_until';

            $timeRepeat = (new \DateTime($vals['timerepeat']));
            $vals['repeat_section_repeatuntil_month'] = $timeRepeat->format('m');
            $vals['repeat_section_repeatuntil_day'] = $timeRepeat->format('d');
            $vals['repeat_section_repeatuntil_year'] = $timeRepeat->format('Y');
        }

        $vals['range_type'] = 0;

        $vals['user_id'] = Phpfox::getUserId();
        $vals['image_path'] = $aFile['path'];
        $vals['server_id'] = $aFile['server_id'];
    }

    /**
     * @param $params
     * @return array|bool|mixed|void
     * @throws \Apps\Core_MobileApi\Api\Exception\NotFoundErrorException
     * @throws \Apps\Core_MobileApi\Api\Exception\UndefinedResourceName
     * @throws \Apps\Core_MobileApi\Api\Exception\UnknownErrorException
     * @throws \Apps\Core_MobileApi\Api\Exception\ValidationErrorException
     */
    function update($params)
    {
        $id = $this->resolver->resolveId($params);
        /** @var EventForm $form */
        $form = $this->createForm(EventForm::class);
        $event = $this->loadResourceById($id, true);

        if (empty($event)) {
            return $this->notFoundError();
        }
        $this->denyAccessUnlessGranted(EventAccessControl::EDIT, $event);
        $form->setEditing(true);
        $form->setIsrepeat($event->isrepeat);
        if ($form->isValid() && ($values = $form->getValues())) {
            $success = $this->processUpdate($id, $values);
            if ($success) {
                return $this->success([
                    'id' => $id,
                    'resource_name' => EventResource::populate([])->getResourceName()
                ], [], $this->getLocalization()->translate('event_successfully_updated'));
            } else {
                return $this->error($this->getErrorMessage());
            }
        } else {
            return $this->validationParamsError($form->getInvalidFields());
        }
    }

    /**
     * @param $id
     * @param $values
     * @return bool|mixed
     */
    private function processUpdate($id, $values)
    {
        $this->convertSubmitForm($values, true);
        $values['event_id'] = $id;
        $aEvent = $event = $this->loadResourceById($id);
        return $this->processService->update($id, $values, $aEvent);
    }

    function patchUpdate($params)
    {
        // TODO: Implement patchUpdate() method.
    }

    function delete($params)
    {
        $itemId = $this->resolver->resolveId($params);
        $item = $this->loadResourceById($itemId, true);

        if (!$itemId || !$item) {
            return $this->notFoundError();
        }
        $this->denyAccessUnlessGranted(EventAccessControl::DELETE, $item);

        if ($this->processService->delete($itemId)) {
            return $this->success([], [], $this->getLocalization()->translate('successfully_deleted_event'));
        }

        return $this->permissionError();
    }

    function form($params = [])
    {
        $editId = $this->resolver->resolveSingle($params, 'id');
        /** @var EventForm $form */
        $form = $this->createForm(EventForm::class, [
            'title' => 'create_new_event',
            'method' => 'POST',
            'action' => UrlUtility::makeApiUrl('fevent')
        ]);
        $form->setCategories($this->categoryService->getForBrowse());
        $event = $this->loadResourceById($editId, true);
        if ($editId && empty($event)) {
            return $this->notFoundError();
        }
        if ($event) {
            $this->denyAccessUnlessGranted(EventAccessControl::EDIT, $event);
            $form->setEditing(true);
            $form->setTitle('edit_event')
                ->setAction(UrlUtility::makeApiUrl('fevent/:id', $editId))
                ->setMethod('PUT');
            $form->assignValues($event);
        } else {
            $this->denyAccessUnlessGranted(EventAccessControl::ADD);
            if (($iFlood = $this->getSetting()->getUserSetting('fevent.flood_control_events')) !== 0) {
                $aFlood = array(
                    'action' => 'last_post', // The SPAM action
                    'params' => array(
                        'field' => 'time_stamp', // The time stamp field
                        'table' => Phpfox::getT('fevent'), // Database table we plan to check
                        'condition' => 'user_id = ' . $this->getUser()->getId(), // Database WHERE query
                        'time_stamp' => $iFlood * 60 // Seconds);
                    )
                );

                // actually check if flooding
                if (Phpfox::getLib('spam')->check($aFlood)) {
                    return $this->error($this->getLocalization()->translate('you_are_creating_an_event_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }
        }

        return $this->success($form->getFormStructure());
    }

    function approve($params)
    {
        $id = $this->resolver->resolveId($params);

        /** @var EventResource $item */
        $item = $this->loadResourceById($id, true);

        if (!$item) {
            return $this->notFoundError();
        }
        $this->denyAccessUnlessGranted(EventAccessControl::APPROVE, $item);
        if ($this->processService->approve($id)) {
            $item = $this->loadResourceById($id, true);
            $permission = $this->getAccessControl()->getPermissions($item);
            return $this->success(array_merge($permission, ['is_pending' => false]), [], $this->getLocalization()->translate('event_has_been_approved'));
        }

        return $this->error();
    }

    function feature($params)
    {
        $id = $this->resolver->resolveId($params);
        $feature = (int)$this->resolver->resolveSingle($params, 'feature', null, ['1', '0'], 1);

        $item = $this->loadResourceById($id, true);

        if (empty($item)) {
            return $this->notFoundError();
        }

        $this->denyAccessUnlessGranted(EventAccessControl::FEATURE, $item);

        if ($this->processService->feature($id, $feature)) {
            return $this->success([
                'is_featured' => !!$feature
            ], [], $feature ? $this->getLocalization()->translate('event_successfully_featured') : $this->getLocalization()->translate('event_successfully_unfeatured'));
        }
        return $this->error();
    }

    function sponsor($params)
    {
        $id = $this->resolver->resolveId($params);
        $sponsor = (int)$this->resolver->resolveSingle($params, 'sponsor', null, ['1', '0'], 1);

        /** @var EventResource $item */
        $item = $this->loadResourceById($id, true);
        if (empty($item)) {
            return $this->notFoundError();
        }
        $this->denyAccessUnlessGranted(EventAccessControl::SPONSOR, $item);

        if ($this->processService->sponsor($id, $sponsor)) {
            if ($sponsor == 1) {
                $sModule = $this->getLocalization()->translate('fevent');
                Phpfox::getService('ad.process')->addSponsor([
                    'module' => 'fevent',
                    'item_id' => $id,
                    'name' => $this->getLocalization()->translate('default_campaign_custom_name', ['module' => $sModule, 'name' => $item->getTitle()])
                ], false);
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('fevent', $id);
            }
            return $this->success([
                'is_sponsor' => !!$sponsor
            ], [], $sponsor ? $this->getLocalization()->translate('event_successfully_sponsored') : $this->getLocalization()->translate('event_successfully_un_sponsored'));
        }

        return $this->error();
    }

    function loadResourceById($id, $returnResource = false)
    {
        $event = $this->eventService->getEvent($id);
        if (!empty($event['event_country_iso'])) {
            $event['country_iso'] = $event['event_country_iso'];
        }
        if (empty($event['event_id'])) {
            return null;
        }
        if ($returnResource) {
            $event['is_detail'] = true;
            return EventResource::populate($event);
        }
        return $event;
    }

    public function processRow($item)
    {
        /** @var EventResource $resource */
        $resource = $this->populateResource(EventResource::class, $item);
        $this->setHyperlinks($resource);

        $shortFields = [];
        $view = $this->request()->get('view');
        if (in_array($view, ['sponsor', 'feature'])) {
            $shortFields = [
                'resource_name', 'title', 'description', 'image', 'statistic', 'user', 'id', 'start_time', 'end_time', 'location', 'link', 'is_sponsor', 'is_featured', 'query'
            ];
            if ($view == 'sponsor') {
                $shortFields[] = 'sponsor_id';
            }
        }

        $result = $resource
            ->setViewMode(ResourceBase::VIEW_LIST)
            ->setExtra($this->getAccessControl()->getPermissions($resource))
            ->displayShortFields()
            ->toArray($shortFields);
        $result['description'] = 'If you are going to use a passage of Lorem Ipsum';
        return $result;
    }

    /**
     * @param EventResource $resource
     * @param bool $includeLinks
     */
    private function setHyperlinks(EventResource $resource, $includeLinks = false)
    {
        $resource->setSelf([
            EventAccessControl::VIEW => $this->createHyperMediaLink(EventAccessControl::VIEW, $resource,
                HyperLink::GET, 'event/:id', ['id' => $resource->getId()]),
            EventAccessControl::DELETE => $this->createHyperMediaLink(EventAccessControl::DELETE, $resource,
                HyperLink::DELETE, 'event/:id', ['id' => $resource->getId()]),
            EventAccessControl::EDIT => $this->createHyperMediaLink(EventAccessControl::EDIT, $resource,
                HyperLink::GET, 'event/form/:id', ['id' => $resource->getId()]),
        ]);
        if ($includeLinks) {
            $resource->setLinks([
                'likes' => $this->createHyperMediaLink(EventAccessControl::VIEW, $resource, HyperLink::GET, 'like', ['item_id' => $resource->getId(), 'item_type' => 'fevent']),
                'comments' => $this->createHyperMediaLink(EventAccessControl::VIEW, $resource, HyperLink::GET, 'comment', ['item_id' => $resource->getId(), 'item_type' => 'fevent'])
            ]);
        }
    }

    public function createAccessControl()
    {
        $this->accessControl = new EventAccessControl($this->getSetting(), $this->getUser());

        $moduleId = $this->request()->get("module_id");
        $itemId = $this->request()->get("item_id");

        if ($moduleId && $itemId) {
            $context = AppContextFactory::create($moduleId, $itemId);
            if ($context === null) {
                return $this->notFoundError();
            }
            $this->accessControl->setAppContext($context);
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function getPostTypes($id)
    {
        if (empty($this->loadResourceById($id))) {
            return [];
        }

        $postOptions = [];
        $userId = $this->getUser()->getId();

        if (!$userId || !$this->getSetting()->getUserSetting('fevent.can_post_comment_on_event')) {
            return [];
        }
        $postOptions[] = [
            'value' => 'post.status',
            'label' => $this->getLocalization()->translate('post'),
            'description' => $this->getLocalization()->translate('write_something'),
            'icon' => 'quotes-right',
            'icon_color' => '#0f81d8',
        ];

        if (Phpfox::isAppActive('Core_Photos') && $this->getSetting()->getUserSetting('photo.can_upload_photos')) {
            $postOptions[] = [
                'value' => 'post.photo',
                'label' => $this->getLocalization()->translate('photo'),
                'description' => $this->getLocalization()->translate('say_something_about_this_photo'),
                'icon' => 'photos',
                'icon_color' => '#48c260',
            ];
        }

        (($sPlugin = \Phpfox_Plugin::get('mobile.service_EventApi_getposttype_end')) ? eval($sPlugin) : false);

        return $postOptions;
    }

    public function getActions()
    {
        $l = $this->getLocalization();
        return [
            'fevent/photos' => [
                'routeName' => 'formEdit',
                'params' => [
                    'module_name' => 'fevent',
                    'resource_name' => 'fevent',
                    'formType' => 'photos'
                ]
            ],
            'fevent/invite' => [
                'routeName' => 'formEditItem',
                'params' => [
                    'data' => 'id',
                    'module_name' => 'fevent',
                    'resource_name' => 'fevent',
                    'formType' => 'invite'
                ]
            ],
            'fevent/rsvp/attending' => [
                'method' => 'put',
                'url' => 'mobile/fevent/rsvp/:id',
                'data' => 'id,rsvp=1',
                'new_state' => 'rsvp=1, tracker=1',
            ],
            'fevent/rsvp/maybe' => [
                'method' => 'put',
                'url' => 'mobile/fevent/rsvp/:id',
                'data' => 'id,rsvp=2',
                'new_state' => 'rsvp=2, tracker=2',
            ],
            'fevent/rsvp/notAttending' => [
                'method' => 'put',
                'url' => 'mobile/fevent/rsvp/:id',
                'data' => 'id,rsvp=3',
                'new_state' => 'rsvp=3, tracker=3',
            ],
            'fevent/guest_list' => [
                'routeName' => 'smartRoute',
                'params' => [
                    'screen_type' => 'guest'
                ]
            ],
            'fevent/mass_email' => [
                'routeName' => 'formEdit',
                'params' => [
                    'module_name' => 'fevent',
                    'resource_name' => 'fevent',
                    'formType' => 'massEmail',
                ]
            ],
            'fevent/manage_admins' => [
                'routeName' => 'module/manage-admin',
                'params' => [
                    'apiUrl' => 'fevent/event-admin',
                    'module_name' => 'fevent',
                    'resource_name' => 'fevent_admin'
                ]
            ],
        ];
    }

    public function searchFriendFilter($id, $friends)
    {
        $aInviteCache = $this->eventService->isAlreadyInvited($id, $friends);
        if (is_array($aInviteCache)) {
            foreach ($friends as $iKey => $friend) {
                if (isset($aInviteCache[$friend['user_id']])) {
                    $friends[$iKey]['is_active'] = $aInviteCache[$friend['user_id']];
                }
            }
        }
        return $friends;
    }

    /**
     * Get for display on activity feed
     * @param array $feed
     * @param array $item detail data from database
     * @return array
     */
    function getFeedDisplay($feed, $item)
    {
        if (empty($item) && !$item = $this->loadResourceById($feed['item_id'])) {
            return null;
        }
        $event = EventResource::populate($item)->getFeedDisplay();
        return $event;
    }

    public function getMassEmailForm($params)
    {
        $eventId = $this->resolver->resolveSingle($params, 'id');
        $event = $this->loadResourceById($eventId, true);

        if (!$event) {
            return $this->notFoundError();
        }

        $this->denyAccessUnlessGranted(EventAccessControl::MASS_EMAIL, $event);

        $form = $this->createForm(EventMassEmailForm::class, [
            'title' => 'mass_email',
            'method' => 'POST',
            'action' => UrlUtility::makeApiUrl('fevent/mass-email'),
        ]);

        $form->setItemId($eventId);

        return $this->success($form->getFormStructure());
    }

    public function sendMassEmail($params)
    {
        $postedText = $params['text'];
        $params = $this->resolver
            ->setRequired(['event_id', 'subject', 'text'])
            ->setAllowedTypes('event_id', 'int')
            ->resolve($params)
            ->getParameters();
        $params['text'] = $postedText;

        if (!$this->resolver->isValid()) {
            $this->validationParamsError($this->resolver->getInvalidParameters());
        }

        $eventId = $this->resolver->resolveSingle($params, 'event_id');
        $event = $this->loadResourceById($eventId, true);

        if (!$event) {
            return $this->notFoundError();
        }

        $this->denyAccessUnlessGranted(EventAccessControl::MASS_EMAIL, $event);
        if (!$this->eventService->canSendEmails($eventId)) {
            return $this->error(
                $this->getLocalization()->translate('you_are_unable_to_send_out_any_mass_emails_at_the_moment')
            );
        }

        $page = 1;

        while ($page < 100) {
            $sent = Phpfox::getService('fevent.process')->massEmail($eventId, $page, $params['subject'], $params['text']);
            if (empty($sent)) {
                break;
            }
            $page++;
        }

        return $this->success([], [], $this->getLocalization()->translate('done'));
    }

    public static function checkPermission($item)
    {
        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $item['user_id'])) {
            return false;
        }
        if ($item['item_id'] && Phpfox::hasCallback($item['module_id'], 'viewEvent')) {
            if (isset($item['module_id']) && Phpfox::isModule($item['module_id']) && Phpfox::hasCallback($item['module_id'],
                    'checkPermission')
            ) {
                if (!Phpfox::callback($item['module_id'] . '.checkPermission', $item['item_id'],
                    'fevent.view_browse_events')
                ) {
                    return false;
                }
            }
        }
        if (Phpfox::isModule('privacy')) {
            if (!Phpfox::getService('privacy')->check('fevent', $item['event_id'], $item['user_id'], $item['privacy'],
                $item['is_friend'], true)
            ) {
                return false;
            }
        }
        return true;
    }
}