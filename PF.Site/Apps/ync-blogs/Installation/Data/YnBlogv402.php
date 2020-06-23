<?php

namespace Apps\YNC_Blogs\Installation\Data;

use Phpfox;

class YnBlogv402
{
    public function process()
    {
        $aReplacedBlocks = array(
            'related-blog' => array(
                'new_component' => 'blog_list',
                'old_params' => array(
                    'title' => _p('related_blogs'),
                    'data_source' => 'related',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                )
            ),
            'recent_posts' => array(
                'new_component' => 'blog_list',
                'old_params' => array(
                    'title' => _p('recent_blogs'),
                    'data_source' => 'latest',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '6',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'most_read' => array(
                'new_component' => 'blog_list',
                'old_params' => array(
                    'title' => _p('most_read'),
                    'data_source' => 'most_viewed',
                    'display_ranking' => '0',
                    'display_view_more' => '1',
                    'limit' => '4',
                    'cache_time' => '5',
                    'is_slider' => '0',
                )
            ),
            'most_discussed' => array(
                'new_component' => 'blog_list',
                'old_params' => array(
                    'title' => _p('most_discussed'),
                    'data_source' => 'most_commented',
                    'display_ranking' => '0',
                    'display_view_more' => '1',
                    'limit' => '4',
                    'cache_time' => '5',
                    'is_slider' => '0',
                )
            ),
            'most_favorite' => array(
                'new_component' => 'blog_list',
                'old_params' => array(
                    'title' => _p('most_favorited'),
                    'data_source' => 'most_favorited',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '10',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'most_favorite_left' => array(
                'new_component' => 'blog_list',
                'old_params' => array(
                    'title' => _p('most_favorited'),
                    'data_source' => 'most_favorited',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '10',
                    'cache_time' => '5',
                    'is_slider' => '0',
                )
            ),
            'featured_blog' => array(
                'new_component' => 'blog_list',
                'old_params' => array(
                    'title' => _p(''),
                    'data_source' => 'featured',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '6',
                    'cache_time' => '5',
                    'is_slider' => '1',
                )
            ),
            'same_author' => array(
                'new_component' => 'blog_list',
                'old_params' => array(
                    'title' => _p('same_blogger'),
                    'data_source' => 'more_from_user',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                )
            ),
            'hot_blogger' => array(
                'new_component' => 'top_blogger',
                'old_params' => array(
                    'title' => _p('top_bloggers'),
                    'limit' => '3',
                    'cache_time' => '5',
                )
            ),
        );
        $aOldBlocks = db()->select('*')
            ->from(':block')
            ->where('module_id = "ynblog"')
            ->executeRows();
        foreach ($aOldBlocks as $aOldBlock) {
            $sComponent = $aOldBlock['component'];
            if (!empty($aReplacedBlocks[$sComponent])){
                if (!empty($aOldBlock['params'])){
                    $aOldParams = json_decode($aOldBlock['params'], true);
                    $aParams = array_merge($aReplacedBlocks[$sComponent]['old_params'], $aOldParams);
                } else {
                    $aParams = array_merge($aReplacedBlocks[$sComponent]['old_params']);
                }

                db()->update(':block',
                    array(
                        'component' => $aReplacedBlocks[$sComponent]['new_component'],
                        'params' => json_encode($aParams)
                    ),
                    array(
                        'block_id' => $aOldBlock['block_id']
                    )
                );
            }
        }

        foreach ($aReplacedBlocks as $key => $aReplacedBlock) {
            db()->delete(':component', '`module_id` = "ynblog" AND `component` = "' . $key . '"');
        }

        db()->delete(':menu', 'module_id = \'ynblog\' AND url_value <> \'ynblog\'');
    }

}