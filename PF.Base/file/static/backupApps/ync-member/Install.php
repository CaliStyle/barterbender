<?php
namespace Apps\YNC_Member;

use Core\App;
use Core\App\Install\Setting;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_Member';
    }

    protected function setAlias()
    {
        $this->alias = 'ynmember';
    }

    protected function setName()
    {
        $this->name = 'Advanced Member';
    }

    protected function setVersion()
    {
        $this->version = '4.02p1';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.5.0';
        $this->end_support_version = '4.5.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            'ynmember_meta_description' => [
                'var_name' => 'ynmember_meta_description',
                'info' => 'Advanced Members Meta Description',
                'description' => 'Meta description added to pages related to the Advanced Members app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=ynmember_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="ynmember_meta_description"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_ynmember_meta_description\'}',
                'group_id' => 'seo',
                'ordering' => 1,
            ],
            'ynmember_meta_keywords' => [
                'var_name' => 'ynmember_meta_keywords',
                'info' => 'Advanced Members Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Advanced Members app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=ynmember_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="ynmember_meta_keywords"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_ynmember_meta_keywords\'}',
                'group_id' => 'seo',
                'ordering' => 2,
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            "ynmember_add_review_self"     => [
                "var_name" => "ynmember_add_review_self",
                "info"     => "Can write review for oneself?",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0",
                ],
            ],
            "ynmember_add_review_others"     => [
                "var_name" => "ynmember_add_review_others",
                "info"     => "Can write review for others?",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0",
                ],
            ],
            "ynmember_edit_review_self"     => [
                "var_name" => "ynmember_edit_review_self",
                "info"     => "Can edit own review?",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0",
                ],
            ],
            "ynmember_edit_review_others"     => [
                "var_name" => "ynmember_edit_review_others",
                "info"     => "Can edit other's review",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0",
                ],
            ],
            "ynmember_delete_review_self"     => [
                "var_name" => "ynmember_delete_review_self",
                "info"     => "Can delete own review?",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0",
                ],
            ],
            "ynmember_delete_review_others"     => [
                "var_name" => "ynmember_delete_review_others",
                "info"     => "Can delete other's review",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0",
                ],
            ],
            "ynmember_like_comment_review"     => [
                "var_name" => "ynmember_like_comment_review",
                "info"     => "Can like and comment a review?",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0",
                ],
            ],
//            "ynmember_share_review"     => [
//                "var_name" => "ynmember_share_review",
//                "info"     => "Can share a review?",
//                'type'     => Setting\Groups::TYPE_RADIO,
//                "options"  => Setting\Groups::$OPTION_YES_NO,
//                "value"    => [
//                    "1" => "1",
//                    "2" => "1",
//                    "3" => "0",
//                    "4" => "1",
//                    "5" => "0",
//                ],
//            ],
            "ynmember.points_ynmember_review"     => [
                "var_name" => "ynmember_points_review",
                "info"     => "How many points do the users get when they review other members?",
                'type'        => Setting\Site::TYPE_TEXT,
                'value'       => '1',
            ],
            "ynmember_share_member"     => [
                "var_name" => "ynmember_share_member",
                "info"     => "Can share other members?",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0",
                ],
            ],
            "ynmember_follow_member"     => [
                "var_name" => "ynmember_follow_member",
                "info"     => "Can follow other members?",
                'type'     => Setting\Groups::TYPE_RADIO,
                "options"  => Setting\Groups::$OPTION_YES_NO,
                "value"    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "0",
                ],
            ],
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'advanced_search' => '',
                'featured_members' => '',
                'most_reviewed' => '',
                'top_rated' => '',
                'people_you_may_know' => '',
                'recommended_friends' => '',
                'member_of_day' => '',
            ],
            'controller' => [
                'index' => 'ynmember.index'
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Advanced Search' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.index',
                'component' => 'advanced_search',
                'location' => '2',
                'is_active' => '1',
                'ordering' => '1'
            ],
            'Featured Members' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.index',
                'component' => 'featured_members',
                'location' => '2',
                'is_active' => '1',
                'ordering' => '2'
            ],
            'Most Reviewed' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.index',
                'component' => 'most_reviewed',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1'
            ],
            'Top Rated' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.index',
                'component' => 'top_rated',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2'
            ],
            'People You May Know' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.index',
                'component' => 'people_you_may_know',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '3'
            ],
            'Recommended Friends' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.index',
                'component' => 'recommended_friends',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '4'
            ],
            'Member Of Day' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.index',
                'component' => 'member_of_day',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '2'
            ],
            'Member Of Day On Review' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.review',
                'component' => 'member_of_day',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '2'
            ],
            'Member Of Day On Birthday' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.birthday',
                'component' => 'member_of_day',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '2'
            ],
            'Birthday Calendar' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.index',
                'component' => 'birthday_calendar',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '3'
            ],
            'Birthday Calendar On Review' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.review',
                'component' => 'birthday_calendar',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '3'
            ],
            'Birthday Calendar On Birthday' => [
                'type_id' => '0',
                'm_connection' => 'ynmember.birthday',
                'component' => 'birthday_calendar',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '3'
            ],
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->admincp_menu = [
            'Manage Members' => 'ynmember.managemembers',
            'Manage Reviews' => 'ynmember.managereviews',
            'Add New Review Custom Field Groups' => 'ynmember.customfield.add',
            'Manage Review Custom Field Groups' => 'ynmember.customfield.index',
        ];

        $this->database = [
            'YnMember_Mod',
            'YnMember_Follow',
            'YnMember_Follow_Notification',
            'YnMember_Review',
            'YnMember_Birthday_Wish',
            'YnMember_Place',
            'YnMember_Review_Useful',
            'YnMember_Custom_Field',
            'YnMember_Custom_Group',
            'YnMember_Custom_Option',
            'YnMember_Custom_Value',
        ];

        $this->_apps_dir = 'ync-member';
    }
}