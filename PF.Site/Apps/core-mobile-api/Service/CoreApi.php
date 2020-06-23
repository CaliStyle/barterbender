<?php

namespace Apps\Core_MobileApi\Service;

use Apps\Core_MobileApi\Adapter\MobileApp\MobileApp;
use Apps\Core_MobileApi\Adapter\MobileApp\MobileAppSettingInterface;
use Apps\Core_MobileApi\Adapter\MobileApp\Screen;
use Apps\Core_MobileApi\Adapter\MobileApp\ScreenSetting;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\Form\Form;
use Apps\Core_MobileApi\Api\Form\Type\AbstractOptionType;
use Apps\Core_MobileApi\Api\Form\Type\CountryStateType;
use Apps\Core_MobileApi\Api\Form\Type\HierarchyType;
use Apps\Core_MobileApi\Api\Resource\AccountResource;
use Apps\Core_MobileApi\Api\Resource\AttachmentResource;
use Apps\Core_MobileApi\Api\Resource\FileResource;
use Apps\Core_MobileApi\Api\Resource\LinkResource;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Apps\Core_MobileApi\Api\Resource\SearchResource;
use Apps\Core_MobileApi\Api\Resource\TagResource;
use Core\Payment\Trigger;
use Phpfox;
use Phpfox_Plugin;
use Phpfox_Url;

defined('ROUTE_MODULE_DETAIL') OR define('ROUTE_MODULE_DETAIL', 'module/detail');
defined('ROUTE_MODULE_HOME') OR define('ROUTE_MODULE_HOME', 'module/home');
defined('ROUTE_MODULE_LIST') OR define('ROUTE_MODULE_LIST', 'module/list-item');
defined('ROUTE_MODULE_ADD') OR define('ROUTE_MODULE_ADD', 'formEdit');
defined('ROUTE_MODULE_EDIT') OR define('ROUTE_MODULE_EDIT', 'formEdit');
define('PHPFOX_IS_MOBILE_API_CALL', true);

class CoreApi extends AbstractApi implements MobileAppSettingInterface
{

    protected $specialModules;
    protected $specialUCFirsts;

    public function __construct()
    {
        parent::__construct();
        $this->specialModules = [
            'video' => 'v',
            'page'  => 'pages',
            'group' => 'groups'
        ];
        $this->specialUCFirsts = [
            'by'
        ];
    }

    public function __naming()
    {
        return [
            'core/support-form-types' => [
                'get' => 'getFormTypes',
            ],
            'core/route'              => [
                'get' => 'getRoute',
            ],
            'core/site-settings'      => [
                'get' => 'getSiteSettings',
            ],
            'core/mobile-routes'      => [
                'get' => 'getMobileRoutes',
            ],
            'core/endpoint-urls'      => [
                'get' => 'getEndpointUrls',
            ],
            'core/routes-map'         => [
                'get' => 'getAllRoutesMapping',
            ],
            'core/app-settings'       => [
                'get' => 'getAppSettings',
            ],
            'core/actions'            => [
                'get' => 'getSiteActions',
            ],
            'core/url-to-route'       => [
                'get' => 'parseUrlToRoute',
            ],
            'core/phrase'             => [
                'get' => 'phrases'
            ],
            'ping'                    => [
                'get' => 'ping'
            ],
            'core/status'             => [
                'get' => 'getStatus'
            ],
            'core/gateway'            => [
                'get' => 'getGateway'
            ],
            'core/point-checkout'     => [
                'post' => 'checkoutWithPoints'
            ]
        ];
    }

    public function getStatus()
    {
        $coreHelper = Phpfox::getService('core.helper');
        //Get unseen friend request
        if (Phpfox::isModule('friend')) {
            $friendRequest = Phpfox::getService('friend.request')->getUnseenTotal();
            $friendRequest = $coreHelper->shortNumberOver100($friendRequest);
        } else {
            $friendRequest = 0;
        }
        //Get unseen notification
        $notification = (new NotificationApi())->getUnseenTotal();
        $notification = $coreHelper->shortNumberOver100($notification);

        if (Phpfox::isModule('feed')) {
            //Get new feed
            define('PHPFOX_CHECK_FOR_UPDATE_FEED', true);
            define('PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE', PHPFOX_TIME - 60);
            $aRows = Phpfox::getService('feed')->get(null, null, 0, false, false);
            $feed = $coreHelper->shortNumberOver100(count($aRows));
        }
        $data = [
            'new_notification'   => $notification ? $notification : null,
            'new_chat_message'   => null,
            'new_friend_request' => $friendRequest ? $friendRequest : null,
            'new_feed'           => isset($feed) ? $feed : null
        ];
        return $this->success($data);
    }

    public function getAppSettings($params)
    {
        $cacheLib = Phpfox::getLib('cache');
        $versionName = isset($params['api_version_name']) ? $params['api_version_name'] : 'mobile';
        $cacheId = $cacheLib->set("mobile_app_settings_{$this->getUserGroupId()}_{$this->getLanguageId()}_{$versionName}");
        $cacheLib->group('mobile', $cacheId);
        if (!($settings = $cacheLib->getLocalFirst($cacheId))) {
            $resources = NameResource::instance()->getResourceNames(true);

            $settings = Phpfox::getService("mobile.mobile_app_helper")->getAppSettings($resources, $params);

            (($sPlugin = Phpfox_Plugin::get('mobile.core_api_get_app_settings')) ? eval($sPlugin) : false);

            $cacheLib->saveBoth($cacheId, $settings);
            $cacheLib->group('settings', $cacheId);
        }

        (($sPlugin = Phpfox_Plugin::get('mobile.core_api_get_app_settings_no_cache')) ? eval($sPlugin) : false);

        return $this->success($settings);
    }

    public function getSiteActions($params)
    {
        $cacheLib = Phpfox::getLib('cache');
        $versionName = isset($params['api_version_name']) ? $params['api_version_name'] : 'mobile';
        $cacheId = $cacheLib->set("mobile_site_actions_{$this->getUserGroupId()}_{$this->getLanguageId()}_{$versionName}");
        $cacheLib->group('mobile', $cacheId);
        if (!($settings = $cacheLib->getLocalFirst($cacheId))) {
            $resources = NameResource::instance()->getResourceNames(true);

            $settings = Phpfox::getService("mobile.mobile_app_helper")->getActions($resources, $params);

            (($sPlugin = Phpfox_Plugin::get('mobile.core_api_get_site_actions')) ? eval($sPlugin) : false);

            $cacheLib->saveBoth($cacheId, $settings);
            $cacheLib->group('settings', $cacheId);
        }

        (($sPlugin = Phpfox_Plugin::get('mobile.core_api_get_site_actions_no_cache')) ? eval($sPlugin) : false);

        return $this->success($settings);
    }

    /**
     * @param $param
     *
     * @return MobileApp
     * @throws \Apps\Core_MobileApi\Api\Exception\UndefinedResourceName
     */
    public function getAppSetting($param)
    {
        $l = $this->getLocalization();
        return new MobileApp('core', [
            'title'           => $l->translate('general'),
            'other_resources' => [
                new SearchResource([]),
                new FileResource([]),
                new AccountResource([]),
                new AttachmentResource([]),
                new TagResource([]),
                new LinkResource([])
            ],
        ]);
    }

    /**
     * Api fallback method. This api is called if no api mapped found
     * @throws \Apps\Core_MobileApi\Api\Exception\NotFoundErrorException
     */
    public function fallbackCall()
    {
        return $this->notFoundError("Unknown API request");
    }

    public function getEndpointUrls()
    {

        $resourceNaming = new NameResource();
        $apiRoutes = $resourceNaming->generateEndpointUrls('mobile');
        return $this->success($apiRoutes);
    }

    public function getDefaultActionMenu()
    {
        $l = $this->getLocalization();
        return [
            'options' => [
                ['label' => $l->translate('edit'), 'value' => Screen::ACTION_EDIT_ITEM, 'acl' => 'can_edit'],
                ['label' => $l->translate('approve'), 'value' => Screen::ACTION_APPROVE_ITEM, 'show' => 'is_pending', 'acl' => 'can_approve'],
                ['label' => $l->translate('feature'), 'value' => Screen::ACTION_FEATURE_ITEM, 'show' => '!is_featured&&!is_pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('remove_feature'), 'value' => Screen::ACTION_FEATURE_ITEM, 'show' => 'is_featured&&!is_pending', 'acl' => 'can_feature'],
                ['label' => $l->translate('sponsor'), 'value' => Screen::ACTION_SPONSOR_ITEM, 'show' => '!is_sponsor&&!is_pending', 'acl' => 'can_sponsor'],
                ['label' => $l->translate('remove_sponsor'), 'value' => Screen::ACTION_SPONSOR_ITEM, 'show' => 'is_sponsor&&!is_pending', 'acl' => 'can_sponsor'],
                ['label' => $l->translate('report'), 'value' => Screen::ACTION_REPORT_ITEM, 'show' => '!is_owner', 'acl' => 'can_report',],
                ['label' => $l->translate('delete'), 'value' => Screen::ACTION_DELETE_ITEM, 'style' => 'danger', 'acl' => 'can_delete'],
            ],
        ];
    }

    public function getDefaultSortMenu()
    {
        $l = $this->getLocalization();
        return [
            'title'    => $l->translate('sort_by'),
            'queryKey' => 'sort',
            'options'  => [
                ['label' => $l->translate('latest'), 'value' => 'latest'],
                ['label' => $l->translate('most_viewed'), 'value' => 'most_viewed'],
                ['label' => $l->translate('most_liked'), 'value' => 'most_liked'],
                ['label' => $l->translate('most_discussed'), 'value' => 'most_discussed'],
            ],
        ];
    }

    public function getDefaultFilterMenu()
    {
        $l = $this->getLocalization();
        return [
            'title'    => $l->translate('filter_by'),
            'queryKey' => 'when',
            'options'  => [
                ['label' => $l->translate('all_time'), 'value' => 'all-time'],
                ['label' => $l->translate('this_month'), 'value' => 'this-month'],
                ['label' => $l->translate('this_week'), 'value' => 'this-week'],
                ['label' => $l->translate('today'), 'value' => 'today'],
            ],
        ];
    }

    public function getPostTypes()
    {
        $userId = $this->getUser()->getId();
        if (!$userId) {
            return [];
        }
        $postOptions[] = [
            'value'       => 'post.status',
            'label'       => $this->getLocalization()->translate('status'),
            'description' => $this->getLocalization()->translate('what_s_on_your_mind'),
            'icon'        => 'quotes-right',
            'icon_color'  => '#0f81d8',
        ];
        if (Phpfox::isAppActive('Core_Photos') && $this->getSetting()->getUserSetting('photo.can_upload_photos')) {
            $postOptions[] = [
                'value'       => 'post.photo',
                'label'       => $this->getLocalization()->translate('photo'),
                'description' => $this->getLocalization()->translate('say_something_about_this_photo'),
                'icon'        => 'photos',
                'icon_color'  => '#48c260',
            ];
        }
        if (Phpfox::isAppActive('PHPfox_Videos') && $this->getSetting()->getUserSetting('v.pf_video_share')) {
            $postOptions[] = [
                'value'       => 'post.video',
                'label'       => $this->getLocalization()->translate('videos'),
                'description' => $this->getLocalization()->translate('say_something_about_this_video'),
                'icon'        => 'videocam',
                'icon_color'  => '#ffac00',
            ];
        }

        if ($this->getSetting()->getAppSetting('feed.enable_check_in') && $this->getSetting()->getAppSetting('core.google_api_key')) {
            $postOptions[] = [
                'value'       => 'post.checkin',
                'label'       => $this->getLocalization()->translate('check_in'),
                'description' => '',
                'icon'        => 'checkin',
                'icon_color'  => '#f05d28',
            ];
        }

        (($sPlugin = Phpfox_Plugin::get('mobile.service_feedapi_getposttype_end')) ? eval($sPlugin) : false);

        return $postOptions;
    }

    public function getSiteSettings($params)
    {
        $cacheLib = Phpfox::getLib('cache');
        $versionName = isset($params['api_version_name']) ? $params['api_version_name'] : 'mobile';
        $cacheId = $cacheLib->set("mobile_site_settings_{$this->getUserGroupId()}_{$this->getLanguageId()}_{$versionName}");
        $cacheLib->group('mobile', $cacheId);
        if (!($data = $cacheLib->getLocalFirst($cacheId))) {
            $data['screen_setting'] = $this->getScreenSettings();
            $data['post_types'] = $this->getPostTypes();
            $data['general'] = $this->_getGeneralSetting();
            $data['mainMenu'] = (new MenuApi())->getMainMenu();
            $data['share'] = $this->_getShareSettings();
            $data['no_images'] = $this->getNoImages();
            $data['default'] = [
                'filter_menu' => $this->getDefaultFilterMenu(),
                'sort_menu'   => $this->getDefaultSortMenu(),
                'action_menu' => $this->getDefaultActionMenu(),
            ];

            (($sPlugin = Phpfox_Plugin::get('mobile.service_core_api_site_settings')) ? eval($sPlugin) : false);

            $cacheLib->saveBoth($cacheId, $data);
            $cacheLib->group('settings', $cacheId);
        }

        (($sPlugin = Phpfox_Plugin::get('mobile.service_core_api_site_settings_no_cache')) ? eval($sPlugin) : false);

        // no apply cache
        $data['chat'] = $this->_getChatSettings();

        return $this->success($data);
    }

    public function getScreenSettings($param = [])
    {
        $resources = NameResource::instance()->getResourceNames(true);

        $screenSettings = Phpfox::getService("mobile.mobile_app_helper")
            ->getScreenSettings($resources, $param);
        if (!isset($param['screen_only']) || !$param['screen_only']) {
            $screenSettings = Phpfox::getService('mobile.ad-config')->getAllConfigsToSetting($screenSettings);
        }

        (($sPlugin = Phpfox_Plugin::get('mobile.core_api_get_screen_settings')) ? eval($sPlugin) : false);

        return $screenSettings;
    }

    public function getScreenSetting($param)
    {
        $l = $this->getLocalization();
        $screenSetting = new ScreenSetting('core', []);
        $resourceName = '';
        $embedComponents = [];
        if (Phpfox::isAppActive('Core_Announcement')) {
            $embedComponents = [
                [
                    'component'     => 'announcement_list_view',
                    'title'         => $l->translate('announcement'),
                    'resource_name' => 'announcement',
                    'module_name'   => 'announcement'
                ]
            ];
        }
        $embedComponents[] = ['component' => 'stream_composer'];
        $screenSetting->addSetting($resourceName, 'home', [
            'header'       => [
                'component'    => 'home_header',
                'androidTitle' => 'home'
            ],
            'right'        => [
                [
                    'component'     => 'simple_list_block',
                    'module_name'   => 'friend',
                    'resource_name' => 'friend',
                    'title'         => $l->translate('friends')
                ],
                [
                    'component'     => 'simple_list_block',
                    'module_name'   => 'photo',
                    'resource_name' => 'photo_album',
                    'title'         => $l->translate('album'),
                    'limit'         => 4
                ]
            ],
            'content'      => [
                'component'       => 'stream_profile_feeds',
                'embedComponents' => $embedComponents
            ],
            'screen_title' => $l->translate('Core') . ' > ' . $l->translate('feed_steams')
        ]);
        return $screenSetting;
    }

    public function screenToController()
    {
        return [
            'home' => 'core.index-member',
        ];
    }

    private function getNoImages()
    {
        return [
            'no-conversation' => $this->getAppImage('no-conversation'),
            'no-notification' => $this->getAppImage('no-notification')
        ];
    }

    public function getAppImage($imageName = 'no-item')
    {
        $basePath = \Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/core-mobile-api/assets/images/app-images/';

        return $basePath . $imageName . '.png';
    }

    private function _getGeneralSetting()
    {
        list($smallLogo,) = Phpfox::getService('mobile.admincp.setting')->getAppLogo();

        $bAllowVideoUploading = false;

        if (Phpfox::isAppActive('PHPfox_Videos')) {
            $iMethodUpload = setting('pf_video_method_upload');
            if (setting('pf_video_support_upload_video') && (($iMethodUpload == 1 && setting('pf_video_key')) || ($iMethodUpload == 0 && setting('pf_video_ffmpeg_path')))) {
                $bAllowVideoUploading = true;
            }
        }

        $data = [
            'logo_url'                    => '',        // string: site logo url
            'app_small_logo_url'          => $smallLogo,        // string: header logo url,
            'login_type'                  => $this->getSetting()->getAppSetting('user.login_type'),   // string: login by email/or username ?
            'can_register'                => !!$this->getSetting()->getAppSetting('user.allow_user_registration') && !$this->getSetting()->getAppSetting('user.invite_only_community'),
            'can_login_by_facebook'       => !!setting('m9_facebook_enabled'),
            'can_login_by_apple'          => !!$this->getSetting()->getAppSetting('mobile.mobile_enable_apple_login'),
            'google_api_key'              => $this->getSetting()->getAppSetting('core.google_api_key'),
            'enable_tag_friends'          => !!$this->getSetting()->getAppSetting('feed.enable_tag_friends', 1),
            'enable_check_in'             => !!$this->getSetting()->getAppSetting('feed.enable_check_in'),
            'enable_hide_feed'            => !!$this->getSetting()->getAppSetting('feed.enable_hide_feed', 1),
            'enable_upload_video'         => !!$bAllowVideoUploading,
            'site_url'                    => $this->makeUrl(''),
            'min_char_global_search'      => $this->getSetting()->getAppSetting('core.min_character_to_search', 2),
            'allow_activity_point'        => Phpfox::isAppActive('Core_Activity_Points') && $this->getSetting()->getAppSetting('activitypoint.enable_activity_points'),
            'allow_registration_sms'      => !!$this->getSetting()->getAppSetting('core.registration_sms_enable'),
            'photo.photo_max_upload_size' => Phpfox::getLib('file')->getLimit($this->getSetting()->getUserSetting('photo.photo_max_upload_size') / 1024) * 1024,
            'enable_comment_sticker'      => Phpfox::isAppActive('Core_Comments') && class_exists('Apps\Core_Comments\Service\Stickers\Stickers')
        ];

        foreach ([
                     'photo.max_images_per_upload',
                 ] as $key) {
            $data[$key] = (int)$this->getSetting()->getUserSetting($key);
        }

        return $data;
    }


    private function _getChatSettings()
    {

        if (Phpfox::isApps('P_Rocketchat')) {
            $rocketServer = setting('p_rocketchat_server');
            if ($rocketServer) {
                return [
                    'enable'      => true,
                    'server'      => rtrim($rocketServer, '/'),
                    'server_type' => 'rocketchat',
                ];
            }
        }


        $path = '';
        // generate token
        if (!defined('PHPFOX_IM_TOKEN') || !PHPFOX_IM_TOKEN) {
            if (setting('pf_im_node_server_key')) {
                $imToken = md5(strtotime('today midnight') . setting('pf_im_node_server_key'));
                $lifeTime = time() + 86400;
            } else {
                $imToken = '';
                $lifeTime = '';
            }
            $server = setting('pf_im_node_server');
            $useFoxIM = false;
        } else {
            $aTokenData = storage()->get('im_host_token');
            $imToken = PHPFOX_IM_TOKEN;
            $server = rtrim(setting('pf_im_node_server'), '/');
            $path = '/socket.io/';
            if (isset($aTokenData->value->expired)) {
                $lifeTime = $aTokenData->value->expired;
            } else {
                $lifeTime = time() + 86400;
            }
            $useFoxIM = true;
        }
        //Get ban filter
        $filters = Phpfox::getService('ban')->getFilters('word');
        $banFilter = [];
        $banUser = [];
        if (is_array($filters)) {
            foreach ($filters as $filter) {
                $banFilter[$filter['find_value']] = html_entity_decode($filter['replacement']);
                $userGroupsAffected = $filter['user_groups_affected'];
                if (is_array($userGroupsAffected) && !empty($userGroupsAffected)) {
                    foreach ($userGroupsAffected as $userGroup) {
                        if ($userGroup['user_group_id'] == Phpfox::getUserBy('user_group_id')) {
                            if ($filter['return_user_group'] !== null) {
                                $banUser[$filter['find_value']] = $filter['ban_id'];
                            }
                            break;
                        }
                    }
                }
            }
        }
        $data = [
            'enable'              => Phpfox::isAppActive('PHPfox_IM'),
            'server'              => $server, // socket server url,
            'path'                => $path,
            'query'               => ['token' => $imToken, 'EIO' => 3, 'host' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'], // generated token
            'life_time'           => (int)$lifeTime, // expired token timestamp,
            'use_phpfox_im'       => $useFoxIM,
            'allow_non_friends'   => (bool)setting('pf_im_allow_non_friends', 0),
            'server_type'         => setting('pf_im_chat_server', 'nodejs'),
            'user_id'             => (int)$this->getUser()->getId(), // current user id
            'time_delete_message' => setting('pf_time_to_delete_message') * 86400000,
            'total_conversations' => (int)setting('pf_total_conversations'),
            'algolia_app_id'      => setting('pf_im_algolia_app_id'),
            'algolia_api_key'     => setting('pf_im_algolia_api_key'),
            'firebase_server_key' => setting('mobile.mobile_firebase_server_key'),
            'firebase_sender_id'  => setting('mobile.mobile_firebase_sender_id'),
            'banned_words'        => $banFilter,
            'ban_users'           => $banUser
        ];

        return $data;
    }

    public function getMobileRoutes()
    {
        return [
            'blog/add' => [
                'type'           => 'createFormEditScreen',
                'loadFormApiUrl' => 'mobile/blog/form',
                'formAction'     => null,
                'formName'       => 'blog/add',
            ],
        ];
    }

    private function _getShareSettings()
    {
        return [
            'menu' => [
                'title'   => null,
                'message' => null,
                'options' => [
                    ['label' => $this->getLocalization()->translate('share_on_your_wall'), 'value' => 'share.wall'],
                    ['label' => $this->getLocalization()->translate('share_on_friend_s_wall'), 'value' => 'share.friend'],
                    ['label' => $this->getLocalization()->translate('share_on_social'), 'value' => 'share.social'],
                ],

            ],
        ];
    }

    /**
     * Check service is alive
     *
     * @throws \Apps\Core_MobileApi\Api\Exception\ValidationErrorException
     */
    public function ping()
    {
        $phrases = $this->resolver->resolveSingle(null, 'p');
        $pong = [
            'status' => 'success',
            'data'   => [
                "site_status"  => ($this->getSetting()->getAppSetting('core.site_is_offline') ? 'offline' : "online"),
                "site_name"    => $this->getSetting()->getAppSetting("core.site_title"),
                "site_title"   => $this->getSetting()->getAppSetting("core.global_site_title"),
                "home_url"     => UrlUtility::makeHomeUrl(),
                "api_endpoint" => UrlUtility::apiEndpoint(),
                "copyright"    => $this->getSetting()->getAppSetting("core.site_copyright"),
            ],
        ];
        if (!empty($phrases)) {
            $ps = [];
            foreach ($phrases as $phrase) {
                $ps[$phrase] = $this->getLocalization()->translate($phrase);
            }
            $pong['data']['phrases'] = $ps;
        }
        header('Content-Type: application/json');
        echo json_encode($pong);
        exit();
    }

    public function getAll()
    {
        $friendRequestCount = $messageCount = $notificationCount = 0;
        if (Phpfox::isModule('friend')) {
            $friendRequestCount = Phpfox::getService('friend.request')->getUnseenTotal();
        }
        if (Phpfox::isAppActive('Core_Messages')) {
            $messageCount = Phpfox::getService('mail')->getUnseenTotal();
        }
        if (Phpfox::isModule('notification')) {
            $notificationCount = Phpfox::getService('notification')->getUnseenTotal();
        }

        $data = [
            'update_count'         => 0,
            'friend_request_count' => $friendRequestCount,
            'message_count'        => $messageCount,
            'notification_count'   => $notificationCount,
        ];

        return $this->success($data);
    }

    /**
     * @param $params
     *
     * @return array|bool
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    public function getFormTypes($params)
    {
        $pathToCheck = PHPFOX_PARENT_DIR . "PF.Site/Apps/core-mobile-api/Api/Form/Type";
        $pathToCheck = str_replace("/", PHPFOX_DS, $pathToCheck); // Window path compatible
        $form = $this->createForm(Form::class, [
            'title'       => 'All fields example Form',
            'description' => 'Description of form',
            'method'      => 'post',
            'action'      => 'example/end-point',
        ]);
        foreach (glob($pathToCheck . '/*.php') as $file) {
            $className = basename($file, '.php');
            $class = "Apps\\Core_MobileApi\\Api\\Form\\Type\\" . $className;
            if (class_exists($class) && $class != AbstractOptionType::class) {
                $className = str_replace("type", "", strtolower($className));
                $options = [
                    'label'       => "$className label",
                    'description' => "$className text description",
                    // 'value_default' => 'default_value',
                    // 'value' => 'current_value',
                    // 'required' => false,
                ];

                if (is_subclass_of(new $class, "Apps\\Core_MobileApi\\Api\\Form\\Type\\AbstractOptionType")) {
                    $options['options'] = [
                        [
                            'value' => 1,
                            'label' => 'Options is required for type extend AbstractChoiceTypes Only',
                        ],
                    ];
                }
                if ($class == HierarchyType::class) {
                    $options['options'] = [
                        [
                            'value' => 1,
                            'label' => 'Parents label',
                        ],
                    ];
                    $options['suboptions'] = [
                        1 => [
                            [
                                'value' => 1,
                                'label' => 'Children label',
                            ],
                        ],
                    ];
                }
                if ($class == CountryStateType::class) {
                    $options['value'] = ['US', '1'];
                    $options['options'] = [
                        [
                            'value' => 'US',
                            'label' => 'Parents label',
                        ],
                    ];
                    $options['suboptions'] = [
                        'US' => [
                            [
                                'value' => 1,
                                'label' => 'Children label',
                            ],
                        ],
                    ];
                }
                $options['metadata'] = true;

                $form->addField($className, $class, $options);
            }
        }

        if (isset($params['type'])) {
            return $this->success($form->getField(strtolower($params['type']))->getStructure());
        }

        return $this->success($form->getFormStructure());
    }

    public function getRoute($params)
    {
        return $this->success(NameResource::instance()->getRoutingTable("mobile"));
    }

    public function getAllRoutesMapping()
    {
        $mapping = [];

        $supportResource = [
            BlogApi::class,
            EventApi::class,
            ForumApi::class,
            ForumPostApi::class,
            ForumThreadApi::class,
            GroupApi::class,
            GroupInfoApi::class,
            GroupMemberApi::class,
            GroupProfileApi::class,
            MusicAlbumApi::class,
            MusicSongApi::class,
            MusicPlaylistApi::class,
            PageApi::class,
            PageInfoApi::class,
            PageMemberApi::class,
            PageInfoApi::class,
            PhotoAlbumApi::class,
            PhotoApi::class,
            PollApi::class,
            QuizApi::class,
            UserApi::class,
            VideoApi::class,
        ];

        (($sPlugin = Phpfox_Plugin::get('mobile.service_coreapi_getallroutesmapping_start')) ? eval($sPlugin) : false);

        foreach ($supportResource as $apiResource) {
            $instance = new $apiResource();
            if (method_exists($instance, $method = 'getRouteMap')) {
                $mapping = array_merge($mapping, $instance->$method());
            }
        }
        return $this->success($mapping);
    }

    /**
     * @param      $params
     * @param bool $returnArray
     *
     * @return array|bool
     * @throws \Apps\Core_MobileApi\Api\Exception\NotFoundErrorException
     * @throws \Apps\Core_MobileApi\Api\Exception\ValidationErrorException
     */
    public function parseUrlToRoute($params, $returnArray = false)
    {
        if (!$returnArray) {
            $url = $this->resolver->resolveSingle($params, 'url');
        } else {
            $url = $params;
        }

        (($sPlugin = Phpfox_Plugin::get('mobile.service_coreapi_parseUrlToRoute_start')) ? eval($sPlugin) : false);

        if (empty($url)) {
            return $this->notFoundError();
        }
        $nameResource = NameResource::instance();
        $relativePath = trim(str_replace(trim(Phpfox::getParam('core.path'), '/'), '', $url), '/');
        if (empty($relativePath)) {
            if ($returnArray) {
                return ['routeName' => 'home'];
            } else {
                return $this->success(['routeName' => 'home']);
            }
        }
        $pathPart = explode('/', $relativePath);
        $extra = isset($pathPart[1]) ? $pathPart[1] : '';
        $query = $this->parseQueryParams($relativePath);
        $isExtraResource = $extra ? $nameResource->hasApiResourceService($extra) : false;
        $data = [];
        $pathPart[0] = isset($this->specialModules[$pathPart[0]]) ? $this->specialModules[$pathPart[0]] : $pathPart[0];
        $pathPart[0] = Phpfox_Url::instance()->reverseRewrite($pathPart[0]); // support rewrite url
        $objectResources = $nameResource->getObjectResources();
        //Check vanity_url
        $vanity = $this->database()->select('pu.page_id, p.item_type')
            ->from(':pages_url', 'pu')
            ->join(':pages', 'p', 'p.page_id = pu.page_id')
            ->where('vanity_url = \'' . $pathPart[0] . '\'')
            ->execute('getRow');
        if (!empty($vanity)) {
            $pathPart[0] = $vanity['item_type'] == 0 ? 'pages' : 'groups';
            $extra = $vanity['page_id'];
        }

        if (isset($objectResources[$pathPart[0]])) {
            /**
             * @var ResourceBase $objResource
             */
            $objResource = (new $objectResources[$pathPart[0]]([]));
            $data = $objResource->getUrlMapping($relativePath);
        } else if (Phpfox::isModule($pathPart[0]) && $nameResource->hasApiResourceService($pathPart[0])) {
            if ($pathPart[0] == 'link' || !empty($query['link-id'])) {
                $linkId = !empty($query['link-id']) ? $query['link-id'] : $extra;
                $link = Phpfox::getService('link')->getLinkById($linkId);
                if ($link) {
                    $feedPrefix = $link['module_id'] === 'groups' ? 'pages' : $link['module_id'];
                    $feed = $this->getFeedFromItem('link', $linkId, $feedPrefix);
                    if ($feed) {
                        $data = [
                            'routeName' => 'viewItemDetail',
                            'params'    => [
                                'module_name'   => 'feed',
                                'resource_name' => 'feed',
                                'id'            => $feed['feed_id'],
                                'query'         => [
                                    'item_type' => $link['module_id'] ? $link['module_id'] : null,
                                    'item_id'   => $link['item_id'] ? (int)$link['item_id'] : null
                                ]
                            ]
                        ];
                    }
                }
            } else if (!empty($query['comment-id'])) {
                $feedPrefix = $pathPart[0] === 'groups' ? 'pages' : $pathPart[0];
                $feed = $this->getFeedFromItem($pathPart[0] . '_comment', $query['comment-id'], $feedPrefix);
                if ($feed) {
                    $data = [
                        'routeName' => 'viewItemDetail',
                        'params'    => [
                            'module_name'   => 'feed',
                            'resource_name' => 'feed',
                            'id'            => $feed['feed_id'],
                            'query'         => [
                                'item_type' => $pathPart[0],
                                'item_id'   => (int)(isset($feed['parent_user_id']) ? $feed['parent_user_id'] : $query['comment-id'])
                            ]
                        ]
                    ];
                }
            } else if (is_numeric($extra) && isset($pathPart[2]) && $nameResource->hasApiResourceService($pathPart[2])) {
                //Go to app listing
                $data = [
                    'routeName' => 'viewItemListing',
                    'params'    => [
                        'module_name'   => $pathPart[2],
                        'resource_name' => $pathPart[2],
                        'query'         => [
                            'module_id' => $pathPart[0],
                            'item_id'   => (int)$extra,
                        ],
                    ],
                ];
            } else if (is_numeric($extra)) {
                //Go to item detail
                $data = [
                    'routeName' => 'viewItemDetail',
                    'params'    => [
                        'id'            => (int)$extra,
                        'module_name'   => $pathPart[0],
                        'resource_name' => $pathPart[0],
                    ],

                ];
            }
        } else {
            $user = Phpfox::getService('user')->getByUserName($pathPart[0]);
            if ($user) {
                //Redirect to status detail
                if (!empty($query['feed'])) {
                    $data = [
                        'routeName' => 'viewItemDetail',
                        'params'    => [
                            'module_name'   => 'feed',
                            'resource_name' => 'feed',
                            'id'            => (int)$query['feed'],
                        ]
                    ];
                } else if (!empty($query['status-id']) || !empty($query['comment-id'])) {
                    $itemId = !empty($query['status-id']) ? $query['status-id'] : $query['comment-id'];
                    $itemType = !empty($query['status-id']) ? 'user_status' : 'feed_comment';
                    $feed = $this->getFeedFromItem($itemType, $itemId);
                    if ($feed) {
                        $data = [
                            'routeName' => 'viewItemDetail',
                            'params'    => [
                                'module_name'   => 'feed',
                                'resource_name' => 'feed',
                                'id'            => (int)$feed['feed_id'],
                            ]
                        ];
                    } else {
                        $data = [
                            'routeName' => 'viewItemDetail',
                            'params'    => [
                                'module_name'   => 'user',
                                'resource_name' => 'user',
                                'id'            => (int)$user['user_id'],
                            ]
                        ];
                    }
                } else if (!empty($query['link-id'])) {
                    $feed = $this->getFeedFromItem('link', $query['link-id']);
                    if ($feed) {
                        $data = [
                            'routeName' => 'viewItemDetail',
                            'params'    => [
                                'module_name'   => 'feed',
                                'resource_name' => 'feed',
                                'id'            => (int)$feed['feed_id'],
                            ]
                        ];
                    } else {
                        $data = [
                            'routeName' => 'viewItemDetail',
                            'params'    => [
                                'module_name'   => 'user',
                                'resource_name' => 'user',
                                'id'            => (int)$user['user_id'],
                            ]
                        ];
                    }
                } else if (!$user['profile_page_id']) {
                    //User profile
                    if (!$extra) {
                        $data = [
                            'routeName' => 'viewItemDetail',
                            'params'    => [
                                'module_name'   => 'user',
                                'resource_name' => 'user',
                                'id'            => (int)$user['user_id'],
                            ]
                        ];
                    } else if ($isExtraResource) {
                        $data = [
                            'routeName' => 'viewItemListing',
                            'params'    => [
                                'module_name'   => $extra,
                                'resource_name' => $extra,
                                'query'         => [
                                    'profile_id' => (int)$user['user_id'],
                                ],
                            ]
                        ];
                    } else {
                        $data = [
                            'routeName' => 'viewItemDetail',
                            'params'    => [
                                'module_name'   => 'user',
                                'resource_name' => 'user',
                                'id'            => (int)$user['user_id'],
                            ]
                        ];
                    }
                } else {
                    $module = Phpfox::getService('pages')->isPage($pathPart[0]) ? 'pages' : (Phpfox::getService('groups')->isPage($pathPart[0]) ? 'groups' : $extra);
                    //Is pages/groups
                    $data = [
                        'routeName' => $isExtraResource ? 'viewItemListing' : 'viewItemDetail',
                        'params'    => [
                            'module_name'   => $module,
                            'resource_name' => $isExtraResource ? $extra : $module,
                        ]
                    ];
                    if ($isExtraResource) {
                        $data['params']['query'] = [
                            'module_id' => $module,
                            'item_id'   => (int)$user['profile_page_id'],
                        ];
                    } else {
                        $data['params']['id'] = $user['profile_page_id'];
                    }
                }
            }
        }
        (($sPlugin = Phpfox_Plugin::get('mobile.service_coreapi_parseUrlToRoute_end')) ? eval($sPlugin) : false);

        //If can't get route, return original url
        if (empty($data)) {
            $data = ['url' => $url];
        }

        if ($returnArray) {
            return $data;
        }
        return $this->success($data);


    }

    /**
     * @param        $sModule
     * @param        $iItemId
     * @param string $prefix
     *
     * @return array|bool
     */
    private function getFeedFromItem($sModule, $iItemId, $prefix = null)
    {
        $aRow = $this->database()->select('*')
            ->from(!empty($prefix) ? Phpfox::getT($prefix . '_feed') : Phpfox::getT('feed'))
            ->where('type_id = \'' . $this->database()->escape($sModule) . '\' AND item_id = ' . (int)$iItemId)
            ->executeRow();

        if (isset($aRow['feed_id'])) {
            return $aRow;
        }

        return false;
    }

    private function parseQueryParams($url)
    {
        //Get query
        $parts = parse_url($url);
        $query = [];
        $otherQuery = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }
        if (isset($parts['path'])) {
            $aData = explode('/', $parts['path']);
            foreach ($aData as $data) {
                if (strpos($data, '_') !== false && count($item = explode('_', $data)) == 2) {
                    $otherQuery[$item[0]] = $item[1];
                }
            }
        }
        return array_merge($query, $otherQuery);
    }

    public function phrases()
    {
        $languageId = $this->getLanguageId();
        $cacheLib = Phpfox::getLib('cache');
        $cacheId = $cacheLib->set('mobile_phrases_' . $languageId);

        if (!($phrases = $cacheLib->getLocalFirst($cacheId))) {
            $mobilePhrases = $this->mobilePhrases();
            $phrases = [];
            foreach ($mobilePhrases as $mobilePhrase) {
                $phrases[$mobilePhrase] = $this->getLocalization()->translate($mobilePhrase, null, $languageId);
            }
            // update special uppercase character first
            foreach ($this->specialUCFirsts as $mobilePhrase) {
                $phrases[$mobilePhrase] = ucfirst($phrases[$mobilePhrase]);
            }
            $cacheLib->saveBoth($cacheId, $phrases);
            $cacheLib->group('locale', $cacheId);
        }
        $direction = 'ltr';
        $languageName = '';
        $language = $this->getLanguage();
        if ($language) {
            $direction = $language['direction'];
            $languageName = $this->getLocalization()->translate($language['title']);
        }

        return $this->success([
            'locale'    => $languageId,
            'name'      => $languageName,
            'direction' => $direction,
            'messages'  => $phrases
        ]);
    }

    private function mobilePhrases()
    {
        $phrasesList = [
            'oops',
            'delete',
            'ok',
            'cancel',
            'cancel_request',
            'edit',
            'create',
            'pending',
            'favourite',
            'members',
            'photos',
            'blog',
            'event',
            'photo',
            'photo_album',
            'music_playlist',
            'music_album',
            'music_song',
            'poll',
            'quiz',
            'video',
            'page',
            'item',
            'group',
            'listing',
            'with',
            'and_value_other',
            'and_value_others',
            'value_other',
            'value_others',
            'value_point',
            'value_points',
            'value_reply',
            'value_replies',
            'value_friend',
            'value_friends',
            'value_mutual_friends',
            'value_mutual_friend',
            'value_like',
            'value_likes',
            'value_view',
            'value_views',
            'value_comment',
            'value_comments',
            'value_share',
            'value_shares',
            'value_play',
            'value_plays',
            'value_favourite',
            'value_favourites',
            'value_photo',
            'value_photos',
            'value_album',
            'value_albums',
            'value_video',
            'value_videos',
            'value_song',
            'value_songs',
            'value_attending',
            'value_attendings',
            'value_members',
            'value_member',
            'value_thread',
            'value_threads',
            'value_post',
            'value_posts',
            'view_all_value_replies',
            'videos',
            'category_colon',
            'genres_colon',
            'tags_colon',
            'write_a_comment',
            'write_a_reply',
            'uploaded_total_photos_is_uploaded',
            'sign_in_to_your_account',
            'already_members_sign_in',
            'dont_have_an_account_sign_up',
            'info',
            'category_colon_value',
            'genre_colon_value',
            'choose_multiple_photos',
            'network_unreachable',
            'default',
            'request_permission',
            'this_app_needs_access_to_camera_to_work_properly_please_go_to_settings_and_turn_on_permission',
            'this_app_needs_access_to_microphone_to_work_properly_please_go_to_settings_and_turn_on_permission',
            'this_app_needs_access_to_photos_to_save_this_photo_please_go_to_settings_and_turn_on_permission',
            'this_app_needs_access_to_storage_to_save_this_photo_please_go_to_settings_and_turn_on_permission',
            'this_app_needs_access_to_storage_to_save_this_video_please_go_to_settings_and_turn_on_permission',
            'information',
            'map_direction',
            'location_colon',
            'your_activity_items',
            'you_still_need_to_verify_your_account',
            'invisible_mode',
            'app_sharing_items',
            'app_sharing_items_note',
            'invisible_mode_note',
            'edit_account',
            'edit_profile_info',
            'profile_privacy',
            'items_privacy',
            'email_notifications',
            'blocked_users',
            'change_password',
            'this_member_is_now_unblocked',
            'no_blocked_members',
            'comment',
            'comments',
            'likes',
            'view_all_replies',
            'replying_to',
            'end_colon',
            'posted_by_',
            'attending',
            'maybe',
            'awaiting',
            'ongoing',
            'upcoming',
            'ended',
            'created_by_',
            'maybe_attending',
            'not_attending',
            'show_map',
            'share_something_on_your_mind',
            'add_to_your_post',
            'at',
            'liked',
            'like',
            'post',
            'share',
            'more',
            'total_tagged_friends',
            'tagged_friends',
            'privacy',
            'everyone',
            'friends',
            'friends_of_friends',
            'only_me',
            'custom',
            'the_sharing_content_is_not_available_now',
            'closed',
            'sticky',
            'sponsored',
            'friend',
            'mutual_total',
            'no_requests_found',
            'is_your_friend_from_now',
            'add_new_friend_list',
            'friend_list',
            'add_custom_privacy',
            'select_friends',
            'select_members',
            'provide_a_name_for_your_list',
            'edit_friend_list',
            'name_of_new_list',
            'edit_group_detail',
            'edit_group_info',
            'groups_member',
            'groups_members',
            'join',
            'joined',
            'requested',
            'edit_avatar',
            'edit_cover',
            'edit_group',
            'select_admins',
            'manage_photos',
            'invites',
            'visited',
            'invited',
            'buy_now',
            'contact_seller',
            'listing_expired_and_not_available_main_section',
            'this_message_has_been_deleted',
            'no_conversations',
            'next__value_tracks',
            'genres_colon_value',
            'remove',
            'there_are_no_songs_to_be_played',
            'this_album_has_no_songs',
            'edit_page_detail',
            'edit_page_info',
            'edit_permissions',
            'edit_location',
            'admin',
            'invite',
            'edit_page',
            'manage_page',
            'manage_group',
            'person_liked_this',
            'people_liked_this',
            'people_also_like',
            'this_group',
            'this_page',
            'this_user',
            'this_item',
            'mobile_photo_mature_warning',
            'sorry_this_photo_can_only_be_viewed_by_those_older_than_the_age_of_limit',
            'permission_denied_cant_download_photo',
            'photo_downloaded_successfully',
            'voting_available_until_value',
            'you_need_to_write_at_least_2_answers',
            'enter_answer',
            'add_answer',
            'submit_your_vote',
            'view_results_value_votes',
            'vote_again',
            'value_vote',
            'value_votes',
            'total_value_vote',
            'total_value_votes',
            'voting_for_this_poll_was_closed',
            'this_poll_is_being_moderated_and_no_votes_can_be_added_until_it_has_been_approved',
            'correct_total_correct_total_question_percent_correct',
            'correct_total_correct_total_question',
            'you_have_reached_the_maximum_questions_allowed_per_quiz',
            'you_are_required_a_minimum_of_total_questions',
            'you_have_reached_the_maximum_answers_allowed_per_question',
            'you_are_required_a_minimum_of_total_answers_per_question',
            'question',
            'add_more_answer',
            'answer_index',
            'add_question',
            'enter_question',
            'submit_your_answer',
            'member_results_total',
            'view_all_member_results',
            'other_results',
            'selected_correct',
            'selected_incorrect',
            'correct_answer',
            'percent_correct',
            'member_results',
            'delete_question',
            'question_index',
            'about_me',
            'more_information',
            'email',
            'enter_email_address',
            'password',
            'enter_password',
            'invalid_password',
            'change_address',
            'forgot_password',
            'sign_in',
            'search',
            'by',
            'sort',
            'filter',
            'menu',
            'home',
            'requests',
            'messages',
            'notifications',
            'share_something',
            'create_post',
            'whats_on_your_mind',
            'remove_this_photo',
            'remove_this_link',
            'take_photo',
            'take_video',
            'choose_from_library',
            'attach_video_url',
            'invalid_video_url',
            'attach_link_url',
            'invalid_link_url',
            'attach_from_url',
            'done',
            'deselect_value',
            'there_are_no_items',
            'request_password',
            'create_account',
            'sign_up',
            'share_on_your_wall',
            'share_on_friend_s_wall',
            'sign_in_with_facebook',
            'there_are_no_filter_menu_available',
            'there_is_no_song_to_play',
            'you_cant_view_detail_of_this_photo_due_to_privacy_settings',
            'please_vote_an_option',
            'please_answer_all_questions',
            'custom_lists',
            'create_new_custom_list',
            'total_selected',
            'reply',
            'replies',
            'current_admins',
            'add_new_admin',
            'no_admins_found',
            'end',
            '_and_',
            'add_friend',
            'hide',
            'voted_at_',
            'answer',
            'correct',
            'incorrect',
            'confirm',
            'request_sent',
            'edit_profile_picture',
            'update_cover',
            'update_avatar',
            'activity_points',
            'about_info',
            'invalid_site_address',
            'can_not_login',
            'network_error',
            'invalid_email_address',
            'logged_in_failure',
            'could_not_select_more_than_limit_photos',
            'could_not_select_more_than_limit_files',
            'user_s_mutual_friends',
            'video_title',
            'add',
            'request_new_password',
            'filters',
            'feeds',
            'reset',
            'remove_this_video',
            'changes_you_made_may_not_be_saved',
            'leave_',
            'leave',
            'text',
            '_is_pending_approval',
            'awaiting_reply',
            'attach_link',
            'tag_friends',
            'add_photos',
            'add_videos',
            'check_in',
            'post_status',
            'edit_status',
            'update',
            'topics_colon',
            'list_name',
            'name',
            'mutual_friends',
            'admins',
            'are_you_sure',
            'user_cancelled',
            'this_feature_is_coming_soon',
            'alert_',
            'user_unblock',
            'write_a_message',
            'search_dot',
            'select',
            'thread',
            'view_results',
            'chat',
            'remove_all',
            'now_playing',
            'poll_results',
            'guests',
            'add_report',
            'edit_comment',
            'character_user_name',
            'songs',
            'untitled',
            'can_not_get_access_token',
            'could_not_found_user',
            'invariant_violation',
            'could_not_found_screen_configuration',
            'user_cancelled_selection',
            'wait_until_uploading_done_to_submit',
            'network_is_not_connected',
            'user_is_not_logged_in',
            'invalid_server_response',
            'missing_user_id',
            'select_video_file_is_required',
            'invalid_upload_video_item',
            'invalid_site_url',
            'deleted_user',
            'value_thanks',
            'no_songs_found',
            'listing_is_pending_approval',
            'this_blog_is_pending_an_admins_approval',
            'this_quiz_is_awaiting_moderation',
            'this_poll_is_being_moderated_and_no_votes_can_be_added_yet',
            'event_is_pending_approval',
            'are_you_sure_you_want_to_log_out',
            'see_more',
            'end_at',
            'item_is_pending_approval',
            'manage_admins',
            'this_post_is_waiting_for_approval_please_review_the_content',
            'thread_is_pending_approval',
            'sub_forums',
            'video_is_pending_approval',
            'all',
            'recently_active',
            'featured',
            'suggestions',
            'you_don_have_any_new_friend_request',
            'find_more_friends',
            'value_items',
            'value_item',
            'there_is_no_information',
            'cancel_account',
            'account_verification',
            'enter_your_phone',
            'get_token',
            'e_g_number',
            'text_message_was_sent_to_to_phone',
            'enter_a_verification_code',
            'resend_passcode',
            'enter_your_code',
            'free',
            'packages_detail',
            'membership',
            'my_subscription',
            'change',
            'subscribe_subscription_id',
            'subscribe_activation_date',
            'subscribe_expiration_date',
            'status',
            'cancel_subscription',
            'subscribe_cancel_title_block',
            'can_you_tell_us_why',
            'process_payment',
            'friend_requests',
            'all_users',
            'app_name_must_be_restart_to_apply_your_important_change',
            'thank_you_your_transaction_is_waiting_for_approve_by_seller',
            'thank_you_your_transaction_has_been_completed_this_might_take_several_minutes_to_update_your_membership',
            'sorry_your_transaction_has_been_denied',
            'no_gateways_found',
            'thank_you',
            'current_plan',
            'hide_thread',
            'choose_a_background',
            'status_background',
            'enter_username',
            'enter_email_or_username',
            'email_username',
            'username',
            'invalid_username',
            'you_reached_to_the_limited_number_of_questions',
            'release_to_cancel',
            'reaction',
            'reactions',
            'view_detail',
            'shorten_k_plus',
            'shorten_m_plus',
            'shorten_b_plus',
            'shorten_t_plus',
            'this_app_needs_access_to_storage_to_work_properly_please_go_to_settings_and_turn_on_permission',
            'from',
            'to',
            'search_by_full_name',
            'mobile_provide_an_email_with_account',
            'publish_date_colon',
            'sub_forums',
            'value_sub_forum',
            'value_sub_forums',
            'view_thread',
            'view_post',
            'mobile_membership_pending_purchase_notice',
            'sharing_multiple_types_is_not_supported_at_the_moment',
            'sharing_more_than_1_video_is_not_supported_at_the_moment',
            'clear',
            'quantity',
            'thank_you_your_transaction_has_been_completed_this_might_take_several_minutes_to_update_your_transaction',
            'remove_from_now_playing_list',
            'invoice_id',
            'pay',
            'mobile_you_wont_see_this_post_in_news_feed',
            'undo',
            'mobile_you_wont_see_posts_from_full_name',
            'manage_hidden',
            'feed_hidden',
            'hide_all_from_full_name_regular',
            'unhide',
            'post_as_label',
            'pending_requests',
            'all_members',
            'posted_in_',
            'next',
            'dark_mode',
            'appearance_upper',
            'language',
            'change_language_to_language_name',
            'app_name_will_restart_to_complete_this_change',
            'draft',
            'unexpected_network_problem_occurred_please_try_to_close_and_reopen_the_app',
            'view_profile_picture',
            'view_avatar',
            'view_cover',
            'read_more',
            'choose_from_photos',
            'no_categories_added',
            'please_log_in_before_sharing',
            'no_categories_added',
            'reload',
            'browse_map_to_explore_items_near_you',
            'no_items_found',
            'ios_location_enable_permission',
            'android_location_enable_permission',
            'error_detect_location',
            'near_me',
            'map',
            'no_internet_connection',
            'you_are_offline',
            'try_again',
            'say_hi_to_value_now',
            'no_requests',
            'find_friends',
            'no_notifications',
            'its_quiet_here',
            'no_messages',
            'you_dont_have_any_conversations',
            'new_conversation',
            'select_all',
            'deselect_all',
            'from_upper',
            'to_upper',
            'sign_in_with_apple',
            'fb_continue',
            'remove_preview',
            'edit_comment',
            'editing',
            'sticker_sets',
            'all_stickers',
            'my_stickers',
            'this_comment_has_been_hidden',
            'log_in',
            'log_in_with_facebook',
            'view_more_comments',
            'view_all_comments'
        ];

        (($sPlugin = Phpfox_Plugin::get('mobile.service_coreapi_mobilePhrases')) ? eval($sPlugin) : false);

        return $phrasesList;
    }

    protected function getLanguageId()
    {
        $languageId = $this->getUser()->language_id;
        if (!$languageId) {
            $languageId = \Phpfox_Locale::instance()->autoLoadLanguage();
        }
        return $languageId;
    }

    public function getLanguage()
    {
        $languageId = $this->getLanguageId();
        $cacheLib = Phpfox::getLib('cache');
        $sLangId = $cacheLib->set(['locale', 'language_' . $languageId]);
        if (!($aLanguage = $cacheLib->get($sLangId))) {
            $aLanguage = db()->select('*')
                ->from(Phpfox::getT('language'))
                ->where("language_id = '" . db()->escape($languageId) . "'")
                ->execute('getRow');
        }
        return $aLanguage;
    }

    protected function getUserGroupId()
    {
        return Phpfox::getUserBy('user_group_id');
    }

    public function getGateway($params)
    {
        $params = $this->resolver
            ->setDefined(['price', 'currency', 'seller', 'allow_point', 'allow_gateway'])
            ->setDefault([
                'allow_point'   => true,
                'allow_gateway' => true
            ])
            ->resolve($params)->getParameters();

        //Support PayPal only
        if ($params['allow_gateway']) {
            $gateways = $this->database()
                ->select('ag.*')
                ->from(':api_gateway', 'ag')
                ->where('ag.is_active = 1 && ag.gateway_id = "paypal"')
                ->execute('getSlaveRows');
        } else {
            $gateways = [];
        }

        $userGateways = [];
        if (!empty($params['seller'])) {
            $userGateways = Phpfox::getService('api.gateway')->getUserGateways((int)$params['seller']);
        }
        $results = [];
        if ($gateways) {
            foreach ($gateways as $gateway) {
                $clientId = $this->getSetting()->getAppSetting('mobile.mobile_paypal_client_id');
                $secretId = $this->getSetting()->getAppSetting('mobile.mobile_paypal_secret_id');
                if (empty($clientId) || empty($secretId)) {
                    continue;
                }
                $data = [
                    'gateway_id'  => $gateway['gateway_id'],
                    'title'       => html_entity_decode($gateway['title'], ENT_QUOTES),
                    'description' => html_entity_decode($gateway['description'], ENT_QUOTES),
                    'return_url'  => Phpfox::getLib('url')->makeUrl('mobile.gateway.callback-success.' . $gateway['gateway_id']),
                    'cancel_url'  => Phpfox::getLib('url')->makeUrl('mobile.gateway.callback-fail.' . $gateway['gateway_id']),
                    'sandbox'     => !!$gateway['is_test'],
                    'client_id'   => $clientId,
                    'secret_id'   => $secretId,
                ];
                if (!empty($params['seller'])) {
                    if (isset($userGateways[$gateway['gateway_id']]) && !empty($userGateways[$gateway['gateway_id']]['gateway'])) {
                        $data['setting'] = $userGateways[$gateway['gateway_id']]['gateway'];
                        $results[] = $data;
                    }
                } else {
                    $results[] = $data;
                }
            }
        }
        if ($params['allow_point']) {
            $this->getPointGateway($params, $results);
        }
        (($sPlugin = Phpfox_Plugin::get('mobil.service_subscription_api_get_gateways')) ? eval($sPlugin) : false);

        return $this->success($results);
    }

    private function getPointGateway($params, &$gateways)
    {
        if (isset($params['currency'], $params['price']) && Phpfox::isAppActive('Core_Activity_Points') && Phpfox::getUserParam('activitypoint.can_purchase_with_activity_points')) {
            $totalPoints = (int)$this->database()
                ->select('activity_points')
                ->from(Phpfox::getT('user_activity'))
                ->where('user_id = ' . (int)$this->getUser()->getId())
                ->execute('getSlaveField');
            $setting = Phpfox::getParam('activitypoint.activity_points_conversion_rate');
            $currency = $params['currency'];
            if (isset($setting[$currency]) && is_numeric($setting[$currency])) {
                $conversion = ($setting[$currency] != 0 ? ($params['price'] / $setting[$currency]) : 0);
                if ($totalPoints >= $conversion) {
                    $gateways[] = [
                        'gateway_id'  => 'activitypoints',
                        'title'       => $this->getLocalization()->translate('activity_points'),
                        'description' => $this->getLocalization()->translate('purchase_points_info', ['yourpoints' => number_format($totalPoints), 'yourcost' => number_format($conversion)]),
                        'notify_url'  => ''
                    ];
                }
            }
        }
    }

    public function checkoutWithPoints($params)
    {
        $params = $this->resolver
            ->setRequired(['price', 'currency', 'gateway_id', 'item_number'])
            ->resolve($params)->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->missingParamsError($this->resolver->getInvalidParameters());
        }
        if ($params['gateway_id'] == 'activitypoints' && Phpfox::isAppActive('Core_Activity_Points') && Phpfox::getUserParam('activitypoint.can_purchase_with_activity_points')) {
            $aParts = explode('|', $params['item_number']);
            if ($aReturn = Phpfox::getService('activitypoint.process')->purchaseWithPoints($aParts[0], $aParts[1],
                $params['price'], $params['currency'])
            ) {
                return $this->success([], [], $this->getLocalization()->translate('purchase_successfully_completed_dot'));
            }
        }
        return $this->permissionError();
    }

    public function callbackBillingPlan($response)
    {
        $recurringSubscription = false;
        if (!empty($response['event_type']) && in_array($response['event_type'], ['BILLING.SUBSCRIPTION.UPDATED', 'BILLING.SUBSCRIPTION.RE-ACTIVATED', 'BILLING.SUBSCRIPTION.CANCELLED', 'PAYMENT.SALE.COMPLETED', 'PAYMENT.SALE.DENIED', 'PAYMENT.CAPTURE.DENIED', 'PAYMENT.CAPTURE.COMPLETED'])) {
            $resource = $response['resource'];
            if (in_array($response['event_type'], ['PAYMENT.SALE.COMPLETED', 'PAYMENT.SALE.DENIED']) && !empty($resource['billing_agreement_id'])) {
                //Should fetch agreement id first
                $agreement = $this->getPayPalAgreement($resource['billing_agreement_id']);
                if (empty($agreement)) {
                    return false;
                }
                $price = isset($resource['amount']['total']) ? $resource['amount']['total'] : 0;
                $responseStatus = isset($resource['state']) ? $resource['state'] : $agreement['state'];
                $invoice = $agreement['description'];
                $recurringSubscription = true;
            } else if (in_array($response['event_type'], ['PAYMENT.CAPTURE.DENIED', 'PAYMENT.CAPTURE.COMPLETED'])) {
                $price = isset($resource['amount']['value']) ? $resource['amount']['value'] : ($response['amount']['total'] ? $response['amount']['total'] : 0);
                $responseStatus = isset($resource['status']) ? $resource['status'] : (isset($response['state']) ? $response['state'] : '');
                $invoice = isset($resource['invoice_id']) ? $resource['invoice_id'] : '';
            } else {
                $responseStatus = isset($resource['state']) ? $resource['state'] : (isset($resource['status']) ? $resource['status'] : '');
                $price = isset($resource['agreement_details']) ? $resource['agreement_details']['last_payment_amount']['value'] : 0;
                $invoice = isset($resource['description']) ? $resource['description'] : '';
                $recurringSubscription = true;
            }
            $this->processPaypalBillingCallback($invoice, $responseStatus, $price, $recurringSubscription);
        } else {
            return $this->callbackPaymentApi($_REQUEST);
        }
        return true;
    }

    private function getPayPalAgreement($id)
    {
        $payPal = $this->database()
            ->select('ag.*')
            ->from(':api_gateway', 'ag')
            ->where('ag.is_active = 1 && ag.gateway_id = "paypal"')
            ->execute('getRow');
        if (!$payPal || !$this->getSetting()->getAppSetting('mobile.mobile_paypal_client_id') || !$this->getSetting()->getAppSetting('mobile.mobile_paypal_secret_id')) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $payPal['is_test'] ? 'https://api.sandbox.paypal.com/v1/oauth2/token' : 'https://api.paypal.com/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->getSetting()->getAppSetting('mobile.mobile_paypal_client_id') . ":" . $this->getSetting()->getAppSetting('mobile.mobile_paypal_secret_id'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $result = curl_exec($ch);
        $result = $result !== false ? json_decode($result, true) : [];
        curl_close($ch);
        if (isset($result['access_token'])) {
            $authorization = 'Authorization: Bearer ' . $result['access_token'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
            curl_setopt($ch, CURLOPT_URL, ($payPal['is_test'] ? 'https://api.sandbox.paypal.com/v1/payments/billing-agreements/' : 'https://api.paypal.com/v1/payments/billing-agreements/') . '/' . $id);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response !== false ? json_decode($response, true) : false;
        }
        return false;
    }

    /**
     * @param $invoice
     * @param $responseStatus
     * @param $price
     * @param $recurringSubscription
     */
    private function processPaypalBillingCallback($invoice, $responseStatus, $price, $recurringSubscription = false)
    {
        $parts = explode('|', $invoice);
        if (substr($parts[0], 0, 5) == '@App/') {
            $isApp = true;
            Phpfox::log('Is an APP.');
        } else {
            $isApp = Phpfox::isAppAlias($parts[0]);
        }

        //Force Auto Renew for recurring subscription
        if ($parts[0] == 'subscribe' && $recurringSubscription) {
            $this->database()->update(':subscribe_purchase', ['renew_type' => 1], 'purchase_id =' . (int)$parts[1]);
        }

        if ($isApp || Phpfox::isModule($parts[0])) {
            if ($isApp || (Phpfox::isModule($parts[0]) && Phpfox::hasCallback($parts[0], 'paymentApiCallback'))) {
                $status = null;
                if (!empty($responseStatus)) {
                    switch ($responseStatus) {
                        case 'Active':
                        case 'COMPLETED':
                        case 'completed':
                            $status = 'completed';
                            break;
                        case 'Pending':
                            $status = 'pending';
                            break;
                        case 'Suspended':
                        case 'Cancelled':
                        case 'Expired':
                        case 'denied':
                            $status = 'cancel';
                            break;
                    }
                    if ($status !== null) {
                        Phpfox::log('Executing module callback');

                        $params = [
                            'gateway'     => 'paypal',
                            'ref'         => '',
                            'status'      => $status,
                            'item_number' => $parts[1],
                            'total_paid'  => $price
                        ];

                        if ($isApp && !Phpfox::isAppAlias($parts[0])) {
                            $callback = str_replace('@App/', '', $parts[0]);
                            Phpfox::log('Running app callback on: ' . $callback);
                            Trigger::event($callback, $params);
                        } else {
                            Phpfox::callback($parts[0] . '.paymentApiCallback', $params);
                        }

                        header('HTTP/1.1 200 OK');
                    }
                }
            }
        }
    }

    public function callbackPaymentApi($response)
    {
        Phpfox::log('Starting PayPal callback');
        // Read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        // Loop through each of the variables posted by PayPal
        foreach ($response as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }
        Phpfox::log('Attempting callback');
        // Post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
        $header .= "Host: " . (true ? 'www.sandbox.paypal.com' : 'www.paypal.com') . "\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n";
        $header .= "Connection: Close\r\n\r\n";
        $fp = fsockopen((true ? 'ssl://www.sandbox.paypal.com' : 'www.paypal.com'), (true ? 443 : 80), $error_no, $error_msg, 30);
        fputs($fp, $header . $req);
        $bVerified = false;
        while (!feof($fp)) {
            $res = fgets($fp, 1024);
            $res = strtoupper($res);
            if (strpos($res, 'VERIFIED') == 0) {
                $bVerified = true;
                break;
            }
        }
        fclose($fp);

        if ($bVerified === true) {
            $aParts = explode('|', $response['invoice']);
            if (substr($aParts[0], 0, 5) == '@App/') {
                $isApp = true;
                Phpfox::log('Is an APP.');
            } else {
                $isApp = Phpfox::isAppAlias($aParts[0]);
            }

            if ($isApp || Phpfox::isModule($aParts[0])) {
                if ($isApp || (Phpfox::isModule($aParts[0]) && Phpfox::hasCallback($aParts[0], 'paymentApiCallback'))) {
                    $sStatus = null;
                    if (isset($response['payment_status'])) {
                        switch ($response['payment_status']) {
                            case 'Completed':
                                $sStatus = 'completed';
                                break;
                            case 'Pending':
                                $sStatus = 'pending';
                                break;
                            case 'Refunded':
                            case 'Reversed':
                                $sStatus = 'cancel';
                                break;
                        }
                    }
                    if (isset($response['txn_type'])) {
                        switch ($response['txn_type']) {
                            case 'subscr_cancel':
                            case 'subscr_failed':
                                $sStatus = 'cancel';
                                break;
                        }
                    }

                    if ($sStatus !== null) {
                        Phpfox::log('Executing module callback');

                        $params = [
                            'gateway'     => 'paypal',
                            'ref'         => $response['txn_id'],
                            'status'      => $sStatus,
                            'item_number' => $aParts[1],
                            'total_paid'  => (isset($response['mc_gross']) ? $response['mc_gross'] : null)
                        ];

                        if ($isApp && !Phpfox::isAppAlias($aParts[0])) {
                            $callback = str_replace('@App/', '', $aParts[0]);
                            Phpfox::log('Running app callback on: ' . $callback);
                            Trigger::event($callback, $params);
                        } else {
                            Phpfox::callback($aParts[0] . '.paymentApiCallback', $params);
                        }

                        header('HTTP/1.1 200 OK');
                    } else {
                        Phpfox::log('Status is NULL. Nothing to do');
                    }
                } else {
                    Phpfox::log('Module callback is not valid.');
                }
            } else {
                Phpfox::log('Module is not valid.');
            }
        } else {
            Phpfox::log('Callback FAILED');
        }
    }
}