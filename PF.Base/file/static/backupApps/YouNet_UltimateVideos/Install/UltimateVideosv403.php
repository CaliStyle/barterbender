<?php

namespace Apps\YouNet_UltimateVideos\Install;

use Phpfox;

class UltimateVideosv403
{
    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }

    public function process()
    {
        if (!$this->database()->isField(Phpfox::getT('ynultimatevideo_category'), 'is_hot')) {
            $this->database()->query("ALTER TABLE  `" . Phpfox::getT('ynultimatevideo_category') . "` ADD  `is_hot` TINYINT(1) NOT NULL DEFAULT 0");
        }

        $aReplacedBlocks = array(
            'slideshow_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'featured',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '5',
                    'cache_time' => '5',
                    'is_slider' => '1',
                )
            ),
            'featured_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'featured',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'recent_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'latest',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'most_viewed_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'most_viewed',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '10',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'most_liked_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'most_liked',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'most_commented_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'most_commented',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'recommended_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'recommended',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'related_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'related',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'user_posted_video' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'more_from_user',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'watch_it_again' => array(
                'new_component' => 'video_list',
                'old_params' => array(
                    'data_source' => 'watch_it_again',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '6',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'slideshow_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'featured',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '5',
                    'cache_time' => '5',
                    'is_slider' => '0',
                )
            ),
            'featured_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'featured',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'recent_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'latest',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '10',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'most_viewed_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'most_viewed',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'most_liked_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'most_liked',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'most_commented_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'most_commented',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'recommended_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'recommended',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '10',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'related_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'related',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
            'user_posted_playlist' => array(
                'new_component' => 'playlist_list',
                'old_params' => array(
                    'data_source' => 'more_from_user',
                    'display_ranking' => '0',
                    'display_view_more' => '0',
                    'limit' => '3',
                    'cache_time' => '5',
                    'is_slider' => '0',
                    'view_modes' => array('grid', 'list', 'customcasual')
                )
            ),
        );

        $aOldBlocks = db()->select('*')
            ->from(':block')
            ->where('module_id = "ultimatevideo"')
            ->executeRows();

        foreach ($aOldBlocks as $aOldBlock) {
            $sComponent = $aOldBlock['component'];
            if (!empty($aReplacedBlocks[$sComponent])) {
                if (!empty($aOldBlock['params'])) {
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
            db()->delete(':component', '`module_id` = "ultimatevideo" AND `component` = "' . $key . '"');
        }
    }
}