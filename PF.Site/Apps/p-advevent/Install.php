<?php

namespace Apps\P_AdvEvent;

use Core\App;

/**
 * Class Install
 * @author  Neil J. <neil@phpfox.com>
 * @version 4.6.0
 * @package Apps\P_AdvEvent
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'P_AdvEvent';
    }

    protected function setAlias()
    {
        $this->alias = 'fevent';
    }

    protected function setName()
    {
        $this->name = _p('P_AdvEvent');
    }

    protected function setVersion()
    {
        $this->version = '4.03p2';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.7.3';
    }

    protected function setSettings()
    {
        $this->settings = [
            'fevent_paging_mode' => [
                'var_name' => 'fevent_paging_mode',
                'info' => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page.',
                'type' => 'select',
                'value' => 'loadmore',
                'options' => [
                    'loadmore' => 'Scrolling down to Load More items',
                    'next_prev' => 'Use Next and Prev buttons',
                    'pagination' => 'Use Pagination with page number'
                ],
            ],
            'fevent_default_sort_time' => [
                'var_name' => 'fevent_default_sort_time',
                'info' => 'Default time to sort events',
                'description' => 'Select default time time to sort events in listing events page (Except Pending page, My page and Profile page) and some blocks',
                'type' => 'select',
                'value' => 'all-time',
                'options' => [
                    'all-time' => 'All Time',
                    'this-month' => 'This Month',
                    'this-week' => 'This week',
                    'today' => 'Today',
                    'upcoming' => 'Upcoming',
                    'ongoing' => 'Ongoing'
                ],
                'ordering' => 2
            ],
            'fevent_meta_keywords' => [
                'var_name' => 'fevent_meta_keywords',
                'info' => 'Advanced Events Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Advanced Events app. To edit this setting, please go to AdminCP > Globalization > Phrases, search and edit phrase have var name is: "seo_fevent_meta_keywords" ',
                'type' => '',
                'value' => '{_p var=\'seo_fevent_meta_keywords\'}',
            ],
            'fevent_meta_description' => [
                'var_name' => 'fevent_meta_description',
                'info' => 'Advanced Events Meta Description',
                'description' => 'Meta description added to pages related to the Advanced Events app. To edit this setting, please go to AdminCP > Globalization > Phrases, search and edit phrase have var name is: "seo_fevent_meta_description" ',
                'type' => '',
                'value' => '{_p var=\'seo_fevent_meta_description\'}',
            ],
            'fevent_display_event_created_in_group' => [
                'var_name' => 'fevent_display_event_created_in_group',
                'info' => 'Display events which created in Group to the All Events page at the Advanced Events app',
                'description' => 'Enable to display all public events to the both Advanced Events page in group detail and All Events page in Advanced Events app. Disable to display events created by an users to the both Advanced Events page in group detail and My Events page of this user in Advanced Events app and nobody can see these events in Advanced Events app but owner.',
                'type' => 'boolean',
                'value' => '0',
            ],
            'fevent_display_event_created_in_page' => [
                'var_name' => 'fevent_display_event_created_in_page',
                'info' => 'Display events which created in Page to the All Events page at the Advanced Events app',
                'description' => 'Enable to display all public events to the both Advanced Events page in page detail and All Events page in Advanced Events app. Disable to display events created by an users to the both Advanced Events page in page detail and My Events page of this user in Advanced Events app and nobody can see these events in Advanced Events app but owner.',
                'type' => 'boolean',
                'value' => '0',
            ],
            'fevent_max_instance_repeat_event' => [
                'var_name' => 'fevent_max_instance_repeat_event',
                'info' => 'Maximum instances of each repeat events',
                'description' => 'Maximum instances of each repeat events',
                'type' => 'integer',
                'value' => '50',
            ],
            'subscribe_within_day' => [
                'var_name' => 'subscribe_within_day',
                'info' => 'Send email to subscribers information of new events which happen within days',
                'description' => 'Send email to subscribers information of new events which happen within days',
                'type' => 'integer',
                'value' => '10',
            ],
            'allow_change_date_recurrent_event' => [
                'var_name' => 'allow_change_date_recurrent_event',
                'info' => '* Allow to change date on recurring events',
                'description' => '* Allow to change date on recurring events',
                'type' => 'boolean',
                'value' => '1',
            ],
            'allow_change_time_recurrent_event' => [
                'var_name' => 'allow_change_time_recurrent_event',
                'info' => '* Allow to change time on recurring events',
                'description' => '* Allow to change time on recurring events',
                'type' => 'boolean',
                'value' => '1',
            ],
            'fevent_custom_url' => [
                'var_name' => 'fevent_custom_url',
                'info' => 'Update URL name for the app',
                'description' => '',
                'type' => 'string',
                'value' => 'fevent',
            ],
            'fevent_time_to_show_countdown' => [
                'var_name' => 'fevent_time_to_show_countdown',
                'info' => 'Time to show count down section in event details (days)',
                'description' => 'This setting defines the number of days before the start time of an event to show count down section.',
                'type' => 'integer',
                'value' => '7',
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'can_add_custom_fields' => [
                'var_name' => 'can_add_custom_fields',
                'info' => 'Can add event custom fields? Notice: this setting only apply for members who have permission to go to AdminCP',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 1,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                ],
            ],
            'can_manage_custom_fields' => [
                'var_name' => 'can_manage_custom_fields',
                'info' => 'Can manage event custom fields? Notice: this setting only apply for members who have permission to go to AdminCP',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 1,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                ],
            ],
            'can_edit_own_event' => [
                'var_name' => 'can_edit_own_event',
                'info' => 'Can edit own event?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 1,
                    2 => 1,
                    3 => 1,
                    4 => 1,
                    5 => 1,
                ],
            ],
            'points_fevent' => [
                'var_name' => 'points_fevent',
                'info' => 'How many points does the user get when they add new event?',
                'description' => '',
                'type' => 'integer',
                'value' => [
                    1 => '1',
                    2 => '1',
                    3 => '0',
                    4 => '1',
                    5 => '0',
                ],
            ],
            'can_post_comment_on_event' => [
                'var_name' => 'can_post_comment_on_event',
                'info' => 'Can post comments on events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 1,
                    2 => 1,
                    3 => 1,
                    4 => 1,
                    5 => 1,
                ],
            ],
            'can_edit_other_event' => [
                'var_name' => 'can_edit_other_event',
                'info' => 'Can edit all events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 1,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                ],
            ],
            'can_delete_other_event' => [
                'var_name' => 'can_delete_other_event',
                'info' => 'Can delete all events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                        1 => 1,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    ],
            ],
            'can_delete_own_event' => [
                'var_name' => 'can_delete_own_event',
                'info' => 'Can delete own event?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                        1 => 1,
                        2 => 1,
                        3 => 1,
                        4 => 1,
                        5 => 1,
                    ],
            ],
            'max_upload_size_event' => [
                'var_name' => 'max_upload_size_event',
                'info' => 'Max file size for event photos in kilobits (kb). (1000 kb = 1 mb) For unlimited add "0" without quotes.',
                'description' => '',
                'type' => 'integer',
                'value' => [
                        1 => '500',
                        2 => '500',
                        3 => '500',
                        4 => '500',
                        5 => '500',
                    ],
            ],
            'can_feature_events' => [
                'var_name' => 'can_feature_events',
                'info' => 'Can feature events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                        1 => 1,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    ],
            ],
            'can_approve_events' => [
                'var_name' => 'can_approve_events',
                'info' => 'Can approve events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                        1 => 1,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    ],
            ],
            'can_view_pirvate_events' => [
                'var_name' => 'can_view_pirvate_events',
                'info' => 'Can view private events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                        1 => 1,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    ],
            ],
            'event_must_be_approved' => [
                'var_name' => 'event_must_be_approved',
                'info' => 'Events must be approved first before they are displayed publicly?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                        1 => 0,
                        2 => 0,
                        3 => 0,
                        4 => 0,
                        5 => 0,
                    ],
            ],
            'total_mass_emails_per_hour' => [
                'var_name' => 'total_mass_emails_per_hour',
                'info' => 'Define how long this user group must wait until they are allowed to send out another mass email.',
                'description' => '',
                'type' => 'integer',
                'value' => [
                        1 => '0',
                        2 => '60',
                        3 => '60',
                        4 => '60',
                        5 => '60',
                    ],
            ],
            'can_access_event' => [
                'var_name' => 'can_access_event',
                'info' => 'Can browse and view the event module?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                        1 => 1,
                        2 => 1,
                        3 => 1,
                        4 => 1,
                        5 => 1,
                    ],
            ],
            'flood_control_events' => [
                'var_name' => 'flood_control_events',
                'info' => 'How many minutes should a user wait before they can create another event? Note: Setting it to "0" (without quotes) is default and users will not have to wait.',
                'description' => '',
                'type' => 'integer',
                'value' => [
                        1 => '0',
                        2 => '0',
                        3 => '0',
                        4 => '0',
                        5 => '0',
                    ],
            ],
            'can_mass_mail_own_members' => [
                'var_name' => 'can_mass_mail_own_members',
                'info' => 'Can mass email own event guests?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                        1 => 1,
                        2 => 1,
                        3 => 0,
                        4 => 1,
                        5 => 0,
                    ],
            ],
            'can_create_event' => [
                'var_name' => 'can_create_event',
                'info' => 'Can create an event?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 1,
                    2 => 1,
                    3 => 0,
                    4 => 1,
                    5 => 0,
                ],
            ],
            'can_purchase_sponsor' => [
                'var_name' => 'can_purchase_sponsor',
                'info' => 'Can members of this user group purchase a sponsored ad space?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                ],
            ],
            'can_attach_on_event' => [
                'var_name' => 'can_attach_on_event',
                'info' => 'Can the user attach file on their event?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 1,
                    2 => 1,
                    3 => 0,
                    4 => 1,
                    5 => 0,
                ],
            ],
            'can_sponsor_fevent' => [
                'var_name' => 'can_sponsor_fevent',
                'info' => 'Can members of this user group sponsor their events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                ],
            ],
            'auto_publish_sponsored_item' => [
                'var_name' => 'auto_publish_sponsored_item',
                'info' => 'After the user has purchased a sponsored space, should the event be published right away? If set to false, the admin will have to approve each new purchased sponsored event space before it is shown in the site.',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    1 => 1,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                ],
            ],
            'fevent_sponsor_price' => [
                'var_name' => 'fevent_sponsor_price',
                'info' => 'How much is the sponsor space worth for events? This works in a CPM basis.',
                'description' => '',
                'type' => 'string',
                'value' => [
                    1 => 'null',
                    2 => 'null',
                    3 => 'null',
                    4 => 'null',
                    5 => 'null',
                ],
            ],
            'max_upload_image_event' => [
                'var_name' => 'max_upload_image_event',
                'info' => 'Maximum number of images per upload',
                'description' => '',
                'type' => 'integer',
                'value' => [
                    1 => '6',
                    2 => '6',
                    3 => '6',
                    4 => '6',
                    5 => '6',
                ],
            ],
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'rsvp' => '',
                'category' => '',
                'sponsored' => '',
                'list' => '',
                'map' => '',
                'invite' => '',
                'calendar' => '',
                'birthday' => '',
                'subscribe-event' => '',
                'applyforrepeatevent' => '',
                'event-list' => '',
                'find-event' => ''
            ],
            'controller' => [
                'view' => 'fevent.view',
                'index' => 'fevent.index',
                'profile' => 'fevent.profile',
                'pagecalendar' => 'fevent.pagecalendar',
                'add' => 'fevent.add',
            ],
        ];
    }

    protected function setComponentBlock()
    {
        $index_blocks = $detail_blocks = $pagecalendar_block = [];

        $index_blocks = [
            'Map View' => [
                'type_id' => '0',
                'm_connection' => 'fevent.index',
                'component' => 'gmap-block',
                'module_id' => 'core',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ]
        ];

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "fevent.index" AND component <> "gmap-block"')
            ->executeField();

        if (!$iCnt) {
            $index_blocks = array_merge($index_blocks, [
                    'Featured Events' => [
                        'type_id' => '0',
                        'm_connection' => 'fevent.index',
                        'component' => 'event-list',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' => [
                                'data_source' => 'featured',
                                'limit' => '6',
                                'display_view_more' => '0',
                                'is_slider' => '1',
                                'view_modes' => [
                                        0 => 'grid',
                                    ],
                            ],
                    ],
                    'Find Events' => [
                            'type_id' => '0',
                            'm_connection' => 'fevent.index',
                            'component' => 'find-event',
                            'location' => '2',
                            'is_active' => '1',
                            'ordering' => '2',
                            'params' => '',
                    ],
                    'Birthdays' => [
                            'type_id' => '0',
                            'm_connection' => 'fevent.index',
                            'component' => 'birthday',
                            'location' => '2',
                            'is_active' => '1',
                            'ordering' => '3',
                            'params' =>
                                [],
                    ],
                    'Upcoming Events' => [
                        'type_id' => '0',
                        'm_connection' => 'fevent.index',
                        'component' => 'event-list',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '4',
                        'params' => [
                                'data_source' => 'upcoming',
                                'limit' => '3',
                                'display_view_more' => '1',
                                'is_slider' => '0',
                                'view_modes' => [
                                        0 => 'grid',
                                    ],
                            ],
                    ],
                    'Suggested Events' => [
                        'type_id' => '0',
                        'm_connection' => 'fevent.index',
                        'component' => 'event-list',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '5',
                        'params' => [
                                'data_source' => 'suggest',
                                'limit' => '6',
                                'display_view_more' => '0',
                                'is_slider' => '0',
                                'view_modes' => [
                                        0 => 'list',
                                        1 => 'grid',
                                    ],
                            ],
                    ],
                    'Subscribe Event' => [
                            'type_id' => '0',
                            'm_connection' => 'fevent.index',
                            'component' => 'subscribe-event',
                            'location' => '2',
                            'is_active' => '1',
                            'ordering' => '6',
                            'params' => '',
                    ],
                    'Categories' => [
                            'type_id' => '0',
                            'm_connection' => 'fevent.index',
                            'component' => 'category',
                            'location' => '3',
                            'is_active' => '1',
                            'ordering' => '7',
                            'params' => '',
                    ],
                    'Calendar' => [
                            'type_id' => '0',
                            'm_connection' => 'fevent.index',
                            'component' => 'calendar',
                            'location' => '3',
                            'is_active' => '1',
                            'ordering' => '8',
                            'params' => '',
                    ],
                    'Event Reminder' => [
                        'type_id' => '0',
                        'm_connection' => 'fevent.index',
                        'component' => 'event-list',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '9',
                        'params' => [
                                'data_source' => 'reminder',
                                'limit' => '3',
                                'display_view_more' => '0',
                                'is_slider' => '0',
                                'view_modes' => [
                                        0 => 'grid',
                                    ],
                            ],
                    ],
                    'Invitation' => [
                        'type_id' => '0',
                        'm_connection' => 'fevent.index',
                        'component' => 'event-list',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '10',
                        'params' => [
                                'data_source' => 'invited',
                                'limit' => '3',
                                'display_view_more' => '1',
                                'is_slider' => '0',
                                'view_modes' => [
                                        0 => 'grid',
                                    ],
                            ],
                    ],
                    'Ongoing Events' => [
                            'type_id' => '0',
                            'm_connection' => 'fevent.index',
                            'component' => 'event-list',
                            'location' => '3',
                            'is_active' => '1',
                            'ordering' => '11',
                            'params' => [
                                    'data_source' => 'ongoing',
                                    'limit' => '3',
                                    'display_view_more' => '1',
                                    'is_slider' => '0',
                                    'view_modes' => [
                                            0 => 'grid',
                                        ],
                                ],
                        ]
                    ]
            );
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "fevent.pagecalendar"')
            ->executeField();

        if (!$iCnt) {
            $pagecalendar_block = [
                'Categories1' => [
                        'type_id' => '0',
                        'title' => 'Categories',
                        'm_connection' => 'fevent.pagecalendar',
                        'component' => 'category',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' => '',
                ],
                'Popular Events' => [
                        'type_id' => '0',
                        'm_connection' => 'fevent.pagecalendar',
                        'component' => 'event-list',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' => [
                                'data_source' => 'popular',
                                'limit' => '3',
                                'display_view_more' => '0',
                                'is_slider' => '0',
                                'view_modes' => [
                                        0 => 'grid',
                                    ],
                            ],
                    ],
            ];
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "fevent.view"')
            ->executeField();

        if (!$iCnt) {
            $detail_blocks = [
                'Activity Feed' => [
                        'type_id' => '0',
                        'm_connection' => 'fevent.view',
                        'module_id' => 'feed',
                        'component' => 'display',
                        'location' => '4',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' => ''
                ],
                'Related Events' => [
                        'type_id' => '0',
                        'm_connection' => 'fevent.view',
                        'component' => 'event-list',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' => [
                                'data_source' => 'related',
                                'limit' => '3',
                                'display_view_more' => '0',
                                'is_slider' => '0',
                                'view_modes' => [
                                        0 => 'grid',
                                    ],
                            ],
                    ],
            ];
        }

        $this->component_block = array_merge($index_blocks, $pagecalendar_block, $detail_blocks);
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->_apps_dir = 'p-advevent';
        $this->_writable_dirs = [
            'PF.Base/file/pic/event/'
        ];

        $this->menu = [
            'phrase_var_name' => 'menu_fevent_events',
            'url' => 'fevent',
            'icon' => 'calendar',
        ];
        $this->admincp_route = "/fevent/admincp";
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->database = [
            'Fevent',
            'Fevent_Admin',
            'Fevent_Category',
            'Fevent_Category_Data',
            'Fevent_Cron',
            'Fevent_Cronlog',
            'Fevent_Custom_Field',
            'Fevent_Custom_Option',
            'Fevent_Custom_Value',
            'Fevent_Feed',
            'Fevent_Feed_Comment',
            'Fevent_Gapi',
            'Fevent_Image',
            'Fevent_Invite',
            'Fevent_Setting',
            'Fevent_Subscribe_Email',
            'Fevent_Text',
            'Fevent_Birthday_Wish'
        ];
        $this->admincp_menu = [
            _p('admin_menu_manage_categories') => '#',
            _p('admin_menu_add_category') => 'fevent.add',
            _p('admin_menu_manage_events') => 'fevent.manageevents',
            _p('admin_menu_add_custom_field') => 'fevent.custom.add',
            _p('admin_menu_manage_custom_fields') => 'fevent.custom',
            //_p('admin_menu_manage_location') => 'fevent.location', //FEVENT-1023
            _p('admin_menu_google_api_settings') => 'fevent.settinggapi',
            _p('admin_menu_birthday_block_photo') => 'fevent.birthdayphoto',
            _p('admin_menu_migration_events') => 'fevent.migrations',
        ];
    }
}