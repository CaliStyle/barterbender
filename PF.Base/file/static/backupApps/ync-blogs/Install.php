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
        $this->name = _p('Advanced Blog');
    }

    protected function setVersion()
    {
        $this->version = '4.01p2';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.5.0';
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
                'var_name'    => 'yn_advblog_max_file_size',
                'info'        => 'Maximum file size for thumbnail photos in kilobyte (KB, 1024 KB = 1 MB). For unlimited add "0" without quotes',
                'type'        => Setting\Site::TYPE_TEXT,
                'value'       => '500',
                'js_variable' => true
            ],
            'yn_advblog_size_import_blog' => [
                'var_name'    => 'yn_advblog_size_import_blog',
                'info'        => 'Number of item per page Import Blog in AdminCP',
                'type'        => Setting\Site::TYPE_TEXT,
                'value'       => '10',
                'js_variable' => true
            ],
            'yn_advblog_default_viewmode' => [
                'var_name'    => 'yn_advblog_default_viewmode',
                'info'        => 'Default viewmode for Recent Posts block',
                'type'        => Setting\Site::TYPE_SELECT,
                'value'       => 'grid',
                'options'       => [
                    'grid' => 'Grid View',
                    'list' => 'List View',
                    'big' => 'Casual View',
                ],
                'js_variable' => true
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'ynblog.points_ynblog' => [
                'var_name' => 'ynblog.points_ynblog',
                'info'     => 'Points received when adding a blog',
                'type'     => Setting\Groups::TYPE_TEXT,
                'value'    => 0,
            ],
            'yn_advblog_view' => [
                'var_name' => 'yn_advblog_view',
                'info'     => 'Can view blog',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => 1,
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_approve' => [
                'var_name' => 'yn_advblog_approve',
                'info'     => 'Can approve blogs',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_automatically_approve' => [
                'var_name' => 'yn_advblog_automatically_approve',
                'info'     => 'Should blog added by this user group be approved before they are displayed publicly',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => 0,
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_feature' => [
                'var_name' => 'yn_advblog_feature',
                'info'     => 'Can feature blogs',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_follow' => [
                'var_name' => 'yn_advblog_follow',
                'info'     => 'Can follow a blogger',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => 1,
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_embed_to_blog' => [
                'var_name' => 'yn_advblog_embed_to_blog',
                'info'     => 'Can embed media into their blogs',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => 1,
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_add_blog' => [
                'var_name' => 'yn_advblog_add_blog',
                'info'     => 'Can add a new blog?',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "1",
                    "3" => "0",
                    "4" => "1",
                    "5" => "1"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_delete_other' => [
                'var_name' => 'yn_advblog_delete_other',
                'info'     => 'Can delete blogs added by other users',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_edit_other' => [
                'var_name' => 'yn_advblog_edit_other',
                'info'     => 'Can edit blogs added by other users',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_delete' => [
                'var_name' => 'yn_advblog_delete',
                'info'     => 'Can delete their own blogs',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => 1,
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_edit' => [
                'var_name' => 'yn_advblog_edit',
                'info'     => 'Can edit their own blogs',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => 1,
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_comment' => [
                'var_name' => 'yn_advblog_comment',
                'info'     => 'Can add comments on blogs',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => 1,
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_import' => [
                'var_name' => 'yn_advblog_import',
                'info'     => 'Can import blogs from Wordpress/Blogger/Tumblr',
                'type'     => Setting\Groups::TYPE_RADIO,
                'value'    => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
                'options'  => Setting\Groups::$OPTION_YES_NO
            ],
            'yn_advblog_max_blogs' => [
                'var_name' => 'yn_advblog_max_blogs',
                'info'     => 'Maximum number of blog can post? For unlimited add "0" without quotes',
                'type'     => Setting\Groups::TYPE_TEXT,
                'value'    => [
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
                'related-blog'      =>  '',
                'recent_posts'      =>  '',
                'recent_comment'    =>  '',
                'most_favorite'     =>  '',
                'most_read'         =>  '',
                'most_discussed'    =>  '',
                'hot_tags'          =>  '',
                'category'          =>  '',
                'hot_blogger'       =>  '',
                'author'            =>  '',
                'other_authors'     =>  '',
                'tag_author'        =>  '',
                'most_favorite_left'  =>  '', // Most Favorite Block 1
                'featured_blog'     =>  '',
                'same_author'     =>  '',
            ],
            'controller' => [
                'view'  =>  'ynblog.view',
                'index' =>  'ynblog.index',
                'import' =>  'ynblog.import',
                'following' =>  'ynblog.following'
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Blog Detail Author' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.view',
                'component'     => 'author',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '1',
            ],

            'Same Blogger' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.view',
                'component'     => 'same_author',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '2',
            ],

            'Related Blogs' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.view',
                'component'     => 'related-blog',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '3',
            ],

            'Category' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'category',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '1',
            ],

            'Hot Bloggers' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'hot_blogger',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '2',
            ],

            'Recent Comments' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'recent_comment',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '3',
            ],

            'Most Read' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'most_read',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '4',
            ],

            'Most Discussed' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'most_discussed',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '5',
            ],

            'Author' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'author',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '6',
            ],

            'Other Authors' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'other_authors',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '7',
            ],

            'Tags' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'tag_author',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '8',
            ],

            'Most Favorited' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'most_favorite_left',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '9',
            ],

            'Featured Blogs' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'featured_blog',
                'location'      => '2',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'Recent Posts' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'recent_posts',
                'location'      => '2',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
            'Most Favoriteds' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'most_favorite',
                'location'      => '2',
                'is_active'     => '1',
                'ordering'      => '3',
            ],
            'Hot Tags' => [
                'type_id'       => '0',
                'm_connection'  => 'ynblog.index',
                'component'     => 'hot_tags',
                'location'      => '2',
                'is_active'     => '1',
                'ordering'      => '4',
            ]

        ];
    }

    protected function setPhrase()
    {
        $this->addPhrases($this->_app_phrases);
    }

    protected function setOthers()
    {
        $this->notifications = [
            "__like"        => [
                "message" => "{{ user_full_name }} liked your blog",
                "url"     => "/advanced-blog/view/:id",
                "icon"    => "file-text-o"
            ],
            "__comment"     => [
                "message" => "{{ user_full_name }} commented on your blog",
                "url"     => "/advanced-blog/view/:id",
                "icon"    => "file-text-o"
            ],
            "__favoriteblog"     => [
                "message" => "{{ user_full_name }} favorited on your blog",
                "url"     => "/advanced-blog/view/:id",
                "icon"    => "file-text-o"
            ]
        ];

        $this->_writable_dirs = [
            'PF.Base/file/pic/ynadvancedblog/'
        ];

        $this->admincp_action_menu = [
            "/advanced-blog/admincp/add-category" => "New Category"
        ];
        $this->admincp_route = "/advanced-blog/admincp";
        $this->admincp_menu = [
            "Categories"         => "ynblog.category",
            "Manage Blogs"       => "ynblog.manageblogs",
            "Import Core Blog"   => "ynblog.importcoreblogs",
        ];

        $this->menu = [
            "name" => "Advanced Blog",
            "phrase_var_name" => "Advanced Blog",
            "url"  => "/advanced-blog",
            "icon" => "file-text-o"
        ];

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
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