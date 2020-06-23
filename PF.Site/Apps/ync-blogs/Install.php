<?php

namespace Apps\YNC_Blogs;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\YNC_Blogs
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_Blogs';
    }

    protected function setAlias()
    {
        $this->alias = 'ynblog';
    }

    protected function setName()
    {
        $this->name = _p('Advanced Blogs');
    }

    protected function setVersion()
    {
        $this->version = '4.02p3';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            'yn_advblog_paging_mode' => [
                'var_name' => 'yn_advblog_paging_mode',
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
            'yn_advblog_max_file_size' => [
                'var_name' => 'yn_advblog_max_file_size',
                'info' => 'Maximum file size for thumbnail photos in kilobyte (KB, 1024 KB = 1 MB). For unlimited add "0" without quotes',
                'type' => Setting\Site::TYPE_TEXT,
                'value' => '500',
                'js_variable' => true
            ],
            'yn_advblog_size_import_blog' => [
                'var_name' => 'yn_advblog_size_import_blog',
                'info' => 'Number of item per page Import Blog in AdminCP',
                'type' => Setting\Site::TYPE_TEXT,
                'value' => '10',
                'js_variable' => true
            ],
            'yn_advblog_on_off_rss' => [
                'var_name' => 'yn_advblog_on_off_rss',
                'info' => 'Display RSS',
                'description' => 'Enable to display all RSS. Disable to hide them.',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 1,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'display_ynblog_created_in_group' => [
                'var_name' => 'display_ynblog_created_in_group',
                'info' => 'Display advanced blogs which created in Group to Advanced Blogs app',
                'description' => 'Enable to display all public advanced blogs created in Group to Advanced Blogs app. Disable to hide them.',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 0,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'display_ynblog_created_in_page' => [
                'var_name' => 'display_ynblog_created_in_page',
                'info' => 'Display advanced blogs which created in Page to Advanced Blogs app',
                'description' => 'Enable to display all public advanced blogs created in Page to Advanced Blogs app. Disable to hide them.',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 0,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'allow_create_feed_adv_when_add_new_item' => [
                'var_name' => 'allow_create_feed_adv_when_add_new_item',
                'info' => 'Allow to post on Main feed when add new item',
                'description' => '',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 1,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'custom_url' => [
                'var_name' => 'custom_url',
                'info' => 'Update URL name for the app',
                'description' => '',
                'type' => Setting\Site::TYPE_TEXT,
                'value' => 'advanced-blog',
            ],
        ];
        $this->settings['ynblog_meta_description'] = [
            'var_name' => 'ynblog_meta_description',
            'info' => 'Advanced Blog Meta Description',
            'description' => 'Meta description added to pages related to the Advanced Blog app. <a role="button" onclick="$Core.editMeta(\'seo_ynblog_meta_description\', true)">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_ynblog_meta_description"></span>',
            'type' => '',
            'value' => "{_p var='seo_ynblog_meta_description'}",
            'group_id' => 'seo',
        ];

        $this->settings['ynblog_meta_keywords'] = [
            'var_name' => 'ynblog_meta_keywords',
            'info' => 'Advanced Blog Meta Keywords',
            'description' => 'Meta keywords that will be displayed on sections related to the Advanced Blog app. <a role="button" onclick="$Core.editMeta(\'seo_ynblog_meta_keywords\', true)">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_ynblog_meta_keywords"></span>',
            'type' => '',
            'value' => "{_p var='seo_ynblog_meta_keywords'}",
            'group_id' => 'seo',
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'ynblog.points_ynblog' => [
                'var_name' => 'ynblog.points_ynblog',
                'info' => 'Points received when adding a blog',
                'type' => Setting\Groups::TYPE_TEXT,
                'value' => 0,
            ],
            'yn_advblog_view' => [
                'var_name' => 'yn_advblog_view',
                'info' => 'Can view blog',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 1,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_approve' => [
                'var_name' => 'yn_advblog_approve',
                'info' => 'Can approve blogs',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_automatically_approve' => [
                'var_name' => 'yn_advblog_automatically_approve',
                'info' => 'Should blog added by this user group be approved before they are displayed publicly',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 0,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_feature' => [
                'var_name' => 'yn_advblog_feature',
                'info' => 'Can feature blogs',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_follow' => [
                'var_name' => 'yn_advblog_follow',
                'info' => 'Can follow a blogger',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 1,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_embed_to_blog' => [
                'var_name' => 'yn_advblog_embed_to_blog',
                'info' => 'Can embed media into their blogs',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 1,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_add_blog' => [
                'var_name' => 'yn_advblog_add_blog',
                'info' => 'Can add a new blog?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "1"
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_delete_other' => [
                'var_name' => 'yn_advblog_delete_other',
                'info' => 'Can delete blogs added by other users',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_edit_other' => [
                'var_name' => 'yn_advblog_edit_other',
                'info' => 'Can edit blogs added by other users',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_delete' => [
                'var_name' => 'yn_advblog_delete',
                'info' => 'Can delete their own blogs',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 1,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_edit' => [
                'var_name' => 'yn_advblog_edit',
                'info' => 'Can edit their own blogs',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 1,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_comment' => [
                'var_name' => 'yn_advblog_comment',
                'info' => 'Can add comments on blogs',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => 1,
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_import' => [
                'var_name' => 'yn_advblog_import',
                'info' => 'Can import blogs from Wordpress/Blogger/Tumblr',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_max_blogs' => [
                'var_name' => 'yn_advblog_max_blogs',
                'info' => 'Maximum number of blog can post? For unlimited add "0" without quotes',
                'type' => Setting\Groups::TYPE_TEXT,
                'value' => [
                    "1" => "0",
                    "2" => "10",
                    "3" => "0",
                    "4" => "10",
                    "5" => "0"
                ],
            ],
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'recent_comment' => '',
                'hot_tags' => '',
                'category' => '',
                'top_blogger' => '',
                'author' => '',
                'other_authors' => '',
                'tag_author' => '',
                'top_categories' => '',
                'blog_list' => '',
                'rss' => '',
            ],
            'controller' => [
                'index' => 'ynblog.index',
                'view' => 'ynblog.view',
                'import' => 'ynblog.import',
                'following' => 'ynblog.following'
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $ynblog_index_blocks = $ynblog_view_blocks = $ynblog_following_blocks = array();

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "ynblog.index"')
            ->executeField();

        if (!$iCnt) {
            $ordering = 1;
            $ynblog_index_blocks = array(
                'Featured Blogs' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ynblog.index',
                        'component' => 'blog_list',
                        'module_id' => 'ynblog',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                        'params' =>
                            array(
                                'data_source' => 'featured',
                                'display_ranking' => '0',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '0',
                                'is_slider' => '1',
                                'view_modes' => '',
                            ),
                    ),
                'From your following bloggers' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ynblog.index',
                        'component' => 'blog_list',
                        'module_id' => 'ynblog',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                        'params' =>
                            array(
                                'data_source' => 'recommended',
                                'display_ranking' => '0',
                                'display_view_more' => '0',
                                'limit' => '6',
                                'cache_time' => '0',
                                'is_slider' => '0',
                                'view_modes' =>
                                    array(
                                        0 => 'grid',
                                    ),
                            ),
                    ),
                'Newest Blogs' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ynblog.index',
                        'component' => 'blog_list',
                        'module_id' => 'ynblog',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                        'params' =>
                            array(
                                'data_source' => 'latest',
                                'display_ranking' => '0',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '0',
                                'is_slider' => '0',
                                'view_modes' =>
                                    array(
                                        0 => 'grid',
                                    ),
                            ),
                    ),
                'Categories' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ynblog.index',
                        'component' => 'category',
                        'module_id' => 'ynblog',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                    ),
                'Most Popular' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ynblog.index',
                        'component' => 'blog_list',
                        'module_id' => 'ynblog',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                        'params' =>
                            array(
                                'data_source' => 'most_popular',
                                'defined_time' => 'all_time',
                                'display_ranking' => '1',
                                'display_view_more' => '1',
                                'limit' => '3',
                                'cache_time' => '0',
                                'is_slider' => '0',
                                'view_modes' => ''
                            ),
                    ),
                'Top Bloggers' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ynblog.index',
                        'component' => 'top_blogger',
                        'module_id' => 'ynblog',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                        'params' =>
                            array(
                                'limit' => '3',
                                'cache_time' => '0',
                            ),
                    ),
                'Continue Reading' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ynblog.index',
                        'component' => 'blog_list',
                        'module_id' => 'ynblog',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                        'params' =>
                            array(
                                'data_source' => 'continue_reading',
                                'display_ranking' => '0',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '0',
                                'is_slider' => '0',
                                'view_modes' => ''
                            ),
                    ),
                'Rss' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ynblog.index',
                        'component' => 'rss',
                        'module_id' => 'ynblog',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                    ),
            );
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "ynblog.view"')
            ->executeField();

        if (!$iCnt) {
            $ordering = 1;
            $ynblog_view_blocks = array(
                'Author Info' => array(
                    'type_id'       => '0',
                    'm_connection'  => 'ynblog.view',
                    'component'     => 'author',
                    'location'      => '3',
                    'is_active'     => '1',
                    'ordering' => $ordering++,
                ),
                'Related Articles' => array(
                    'type_id' => '0',
                    'm_connection' => 'ynblog.view',
                    'component' => 'blog_list',
                    'module_id' => 'ynblog',
                    'location' => '3',
                    'is_active' => '1',
                    'ordering' => $ordering++,
                    'params' =>
                        array(
                            'data_source' => 'related',
                            'display_ranking' => '0',
                            'display_view_more' => '0',
                            'limit' => '3',
                            'cache_time' => '0',
                            'is_slider' => '0',
                            'view_modes' => ''
                        ),
                ),
                'Also from this author' => array(
                    'type_id' => '0',
                    'm_connection' => 'ynblog.view',
                    'component' => 'blog_list',
                    'module_id' => 'ynblog',
                    'location' => '4',
                    'is_active' => '1',
                    'ordering' => $ordering++,
                    'params' =>
                        array(
                            'data_source' => 'more_from_user',
                            'display_ranking' => '0',
                            'display_view_more' => '0',
                            'limit' => '3',
                            'cache_time' => '0',
                            'is_slider' => '0',
                            'view_modes' => array(
                                0 => 'list',
                            ),
                        ),
                ),
            );
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "ynblog.following"')
            ->executeField();

        if (!$iCnt) {
            $ordering = 1;
            $ynblog_following_blocks = array(
                'Following:Top Bloggers' =>
                    array(
                        'title' => 'Top Bloggers',
                        'type_id' => '0',
                        'm_connection' => 'ynblog.following',
                        'component' => 'top_blogger',
                        'module_id' => 'ynblog',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => $ordering++,
                        'params' =>
                            array(
                                'limit' => '6',
                                'cache_time' => '0',
                            ),
                    ),
            );
        }

        $this->component_block = array_merge(
            $ynblog_index_blocks,
            $ynblog_view_blocks,
            $ynblog_following_blocks
        );
    }

    protected function setPhrase()
    {
        $this->addPhrases($this->_app_phrases);
    }

    protected function setOthers()
    {
        $this->notifications = [
            "__like" => [
                "message" => "{{ user_full_name }} liked your blog",
                "url" => "/ynblog/view/:id",
                "icon" => "file-text-o"
            ],
            "__comment" => [
                "message" => "{{ user_full_name }} commented on your blog",
                "url" => "/ynblog/view/:id",
                "icon" => "file-text-o"
            ],
            "__favoriteblog" => [
                "message" => "{{ user_full_name }} favorited on your blog",
                "url" => "/ynblog/view/:id",
                "icon" => "file-text-o"
            ]
        ];

        $this->_writable_dirs = [
            'PF.Base/file/pic/ynadvancedblog/'
        ];

        $this->admincp_action_menu = [
            "/ynblog/admincp/add-category" => "New Category"
        ];
        $this->admincp_route = "/ynblog/admincp";
        $this->admincp_menu = [
            "Categories" => "#",
            "Manage Blogs" => "ynblog.manageblogs",
            "Import Core Blog" => "ynblog.importcoreblogs",
        ];

        $this->menu = [
            "phrase_var_name" => "menu_advanced_blogs",
            "url" => 'ynblog',
            "icon" => "file-text-o"
        ];

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->_admin_cp_menu_ajax = false;
        $this->_apps_dir = "ync-blogs";

        $this->database = [
            'YnBlog_Blogs',
            'YnBlog_Category',
            'YnBlog_Category_Data',
            'YnBlog_Favorite',
            'YnBlog_Following',
            'YnBlog_ImportedBlog',
            'YnBlog_Saved',
        ];
    }
}