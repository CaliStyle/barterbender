<?php

/**
 * @param bool $flag
 * @return string
 */
function ultimatevideo_video_view_mode($flag = false)
{
    if (!$flag && false == \Phpfox_Template::instance()->getVar('bMultiViewMode')) {
        return '';
    }
    return '<div class="pull-right ultimatevideo-modeviews show_grid_view">
            <span title="' . _p('Grid View') . '" data-toggle="ultimatevideo" data-cmd="show_grid_view"><i class="ynicon yn-grid-view"></i></span><span title="' . _p('Casual View') . '" data-toggle="ultimatevideo" data-cmd="show_casual_view"><i class="ynicon yn-casual-view"></i></span>
            </div>';
}

/**
 * @param $offsetCount
 * @return string
 */
function ultimatevideo_mode_view_video_format($offsetCount)
{
    $totalCount = count((array)Phpfox_Template::instance()->getVar('aItems'));
    $MAX = 3;
    $total_row = ceil($totalCount / $MAX);
    $current_row = ceil($offsetCount / $MAX);
    $current_row_total = $current_row < $total_row ? $MAX : ($totalCount - ($total_row - 1) * $MAX);
    $current_offset = ($offsetCount - 1) % $MAX;
    return 'row-number-' . ($current_row % 2) . ' row-total-' . $current_row_total . ' row-offset-' . $current_offset;
}

/**
 * @param $value
 * @return string
 */
function ultimatevideo_duration($value)
{
    $value = intval($value);

    if ($value <= 0) {
        return '';
    }

    $hour = floor($value / 3600);
    $min = floor(($value - $hour * 3600) / 60);
    $second = $value - $hour * 3600 - $min * 60;
    $result = [];

    if ($hour) {
        $result[] = str_pad($hour, 2, '0', STR_PAD_LEFT);
    }
    $result[] = str_pad($min, 2, '0', STR_PAD_LEFT);
    $result[] = str_pad($second, 2, '0', STR_PAD_LEFT);

    return implode(':', $result);
}

/**
 * @param $value
 * @param int $id
 * @return string
 */
function ultimatevideo_rating($value, $id = 0)
{
    $value = floor($value + 0.4999);

    $result = [];
    $bCanEdit = $id > 0 && Phpfox::getUserId() > 0;

    for ($i = 1; $i <= 5; ++$i) {
        $edit = $bCanEdit ? 'data-toggle="ultimatevideo" data-cmd="rate_video" data-id="' . $id . '" data-value="' . $i . '"' : '';
        $result[] = $i <= $value ? '<i class="ico ico-star" ' . $edit . '></i>' : '<i class="ico ico-star disable" ' . $edit . '></i>';
    }

    return implode('', $result);
}

/**
 * @param $id
 * @return mixed
 */
function ultimatevideo_favourite($id)
{
    return Phpfox::getService('ultimatevideo.favorite')->isFavorite(Phpfox::getUserId(), $id);
}

/**
 * @param $id
 * @return mixed
 */
function ultimatevideo_watchlater($id)
{
    return Phpfox::getService('ultimatevideo.watchlater')->isWatchLater(Phpfox::getUserId(), $id);
}

/**
 *
 */
event('app_settings', function ($settings) {
    if (isset($settings['ynuv_app_enabled'])) {
        Phpfox::getService('admincp.module.process')->updateActivity('ultimatevideo', $settings['ynuv_app_enabled']);
    }
});

\Phpfox_Module::instance()->addServiceNames([
    'ultimatevideo' => '\Apps\YouNet_UltimateVideos\Service\Ultimatevideo',
    'ultimatevideo.callback' => '\Apps\YouNet_UltimateVideos\Service\Callback',
    'ultimatevideo.process' => '\Apps\YouNet_UltimateVideos\Service\Process',
    'ultimatevideo.browse' => '\Apps\YouNet_UltimateVideos\Service\Browse',
    'ultimatevideo.history' => '\Apps\YouNet_UltimateVideos\Service\History',
    'ultimatevideo.favorite' => '\Apps\YouNet_UltimateVideos\Service\Favourite',
    'ultimatevideo.watchlater' => '\Apps\YouNet_UltimateVideos\Service\Watchlaters',
    'ultimatevideo.playlist' => '\Apps\YouNet_UltimateVideos\Service\Playlist\Playlist',
    'ultimatevideo.playlist.process' => '\Apps\YouNet_UltimateVideos\Service\Playlist\Process',
    'ultimatevideo.playlist.browse' => '\Apps\YouNet_UltimateVideos\Service\Playlist\Browse',
    'ultimatevideo.category' => '\Apps\YouNet_UltimateVideos\Service\Category\Category',
    'ultimatevideo.category.process' => '\Apps\YouNet_UltimateVideos\Service\Category\Process',
    'ultimatevideo.multicat' => '\Apps\YouNet_UltimateVideos\Service\Multicat',
    'ultimatevideo.custom' => '\Apps\YouNet_UltimateVideos\Service\Custom\Custom',
    'ultimatevideo.custom.group' => '\Apps\YouNet_UltimateVideos\Service\Custom\Group',
    'ultimatevideo.custom.process' => '\Apps\YouNet_UltimateVideos\Service\Custom\Process',
    'ultimatevideo.rating' => '\Apps\YouNet_UltimateVideos\Service\Rating',
])->addComponentNames('block', [
    'ultimatevideo.editcategory' => '\Apps\YouNet_UltimateVideos\Block\EditCategoryBlock',
    'ultimatevideo.editcustomfield' => '\Apps\YouNet_UltimateVideos\Block\EditCustomFieldBlock',
    'ultimatevideo.popup_customfield_category' => '\Apps\YouNet_UltimateVideos\Block\PopupCustomFieldCategoryBlock',
    'ultimatevideo.custom.form' => '\Apps\YouNet_UltimateVideos\Block\Custom\FormBlock',
    'ultimatevideo.custom.view' => '\Apps\YouNet_UltimateVideos\Block\Custom\ViewBlock',
    'ultimatevideo.tags' => '\Apps\YouNet_UltimateVideos\Block\TagsBlock',
    'ultimatevideo.tags_video' => '\Apps\YouNet_UltimateVideos\Block\TagsVideoBlock',
    'ultimatevideo.tags_playlist' => '\Apps\YouNet_UltimateVideos\Block\TagsPlaylistBlock',
    'ultimatevideo.category' => '\Apps\YouNet_UltimateVideos\Block\CategoryBlock',
    'ultimatevideo.hot_category' => '\Apps\YouNet_UltimateVideos\Block\HotCategoryBlock',
    'ultimatevideo.user_playlist_checklist' => '\Apps\YouNet_UltimateVideos\Block\UserPlaylistChecklistBlock',
    'ultimatevideo.feed_video' => '\Apps\YouNet_UltimateVideos\Block\FeedVideoBlock',
    'ultimatevideo.playlist_detail_mode_listing' => '\Apps\YouNet_UltimateVideos\Block\PlaylistDetailModeListingBlock',
    'ultimatevideo.playlist_detail_mode_slide' => '\Apps\YouNet_UltimateVideos\Block\PlaylistDetailModeSlideBlock',
    'ultimatevideo.feed_playlist' => '\Apps\YouNet_UltimateVideos\Block\FeedPlaylistBlock',
    'ultimatevideo.video_list' => '\Apps\YouNet_UltimateVideos\Block\VideoListBlock',
    'ultimatevideo.playlist_list' => '\Apps\YouNet_UltimateVideos\Block\PlaylistListBlock',
    'ultimatevideo.your_playlists' => '\Apps\YouNet_UltimateVideos\Block\YourPlaylistsBlock',
    'ultimatevideo.add_category_list' => '\Apps\YouNet_UltimateVideos\Block\AddCategoryList',
    'ultimatevideo.rate_list' => '\Apps\YouNet_UltimateVideos\Block\RateListBlock',
    'ultimatevideo.rating_item' => '\Apps\YouNet_UltimateVideos\Block\RatingItem',
])->addComponentNames('controller', [
    'ultimatevideo.admincp.category' => '\Apps\YouNet_UltimateVideos\Controller\Admin\CategoryController',
    'ultimatevideo.admincp.category-add' => '\Apps\YouNet_UltimateVideos\Controller\Admin\AddCategoryController',
    'ultimatevideo.admincp.customfield-add' => '\Apps\YouNet_UltimateVideos\Controller\Admin\AddCustomFieldController',
    'ultimatevideo.admincp.customfield' => '\Apps\YouNet_UltimateVideos\Controller\Admin\CustomFieldController',
    'ultimatevideo.admincp.customfield-addfield' => '\Apps\YouNet_UltimateVideos\Controller\Admin\AddFieldController',
    'ultimatevideo.admincp.ultilities' => '\Apps\YouNet_UltimateVideos\Controller\Admin\UltilitiesController',
    'ultimatevideo.admincp.managevideos' => '\Apps\YouNet_UltimateVideos\Controller\Admin\ManageVideosController',
    'ultimatevideo.admincp.manageplaylists' => '\Apps\YouNet_UltimateVideos\Controller\Admin\ManagePlaylistsController',
    'ultimatevideo.index' => '\Apps\YouNet_UltimateVideos\Controller\IndexController',
    'ultimatevideo.add' => '\Apps\YouNet_UltimateVideos\Controller\AddController',
    'ultimatevideo.playlist' => '\Apps\YouNet_UltimateVideos\Controller\PlaylistController',
    'ultimatevideo.profile' => '\Apps\YouNet_UltimateVideos\Controller\ProfileController',
    'ultimatevideo.view' => '\Apps\YouNet_UltimateVideos\Controller\ViewController',
    'ultimatevideo.view_playlist' => '\Apps\YouNet_UltimateVideos\Controller\ViewPlaylistController',
    'ultimatevideo.addplaylist' => '\Apps\YouNet_UltimateVideos\Controller\AddPlaylistController',
    'ultimatevideo.code' => '\Apps\YouNet_UltimateVideos\Controller\CodeController',
    'ultimatevideo.embed' => '\Apps\YouNet_UltimateVideos\Controller\EmbedController',
    'ultimatevideo.invite' => '\Apps\YouNet_UltimateVideos\Controller\InviteController',
    'ultimatevideo.oauth2' => '\Apps\YouNet_UltimateVideos\Controller\Oauth2Controller',
    'ultimatevideo.uploadchannel' => '\Apps\YouNet_UltimateVideos\Controller\UploadChannelController',
    'ultimatevideo.form-upload' => '\Apps\YouNet_UltimateVideos\Controller\FormUploadController',
    'ultimatevideo.callback' => Apps\YouNet_UltimateVideos\Controller\CallbackController::class
])->addComponentNames('ajax', [
    'YouNet_UltimateVideos.ajax' => '\Apps\YouNet_UltimateVideos\Ajax\Ajax',
    'ultimatevideo.ajax' => '\Apps\YouNet_UltimateVideos\Ajax\Ajax',
])->addTemplateDirs([
    'ultimatevideo' => PHPFOX_DIR_SITE_APPS . 'YouNet_UltimateVideos' . PHPFOX_DS . 'views',
])->addAliasNames('ultimatevideo', 'YouNet_UltimateVideos');

group('/ultimatevideo', function () {
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('ultimatevideo.admincp.category');
        return 'controller';
    });

    route('/admincp/category/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'ynultimatevideo_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );

        \Phpfox::getLib('cache')->remove('ynultimatevideos');

        return true;
    });
    route('/form-upload/', 'ultimatevideo.form-upload');
    route('/upload', function () {
        Phpfox::isUser(true);
        $aVals = request()->get('val');
        $valid = true;
        $addVideo = true;
        // upload
        if (empty($aVals['video_type']) && $aVals['video_source'] == "Uploaded") {
            if (empty($aVals['video_path']) && empty($aVals['encoding_id'])) {
                $valid = false;
                \Phpfox_Error::set(_p('no_files_found_or_file_is_not_valid_please_try_again'));
            } else {
                $methodUpload = Phpfox::getParam('ultimatevideo.ynuv_video_method_upload');
                if($methodUpload == 0 && !empty($aVals['video_path'])) {
                    $aVals['video_code'] = substr($aVals['video_path'], strpos($_FILES['video_path'], '/') + 1);
                }
                elseif($methodUpload == 1 && !empty($aVals['encoding_id'])) {
                    $storageData = [
                        'privacy' => (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
                        'privacy_list' => json_encode(isset($aVals['privacy_list']) ? $aVals['privacy_list'] : []),
                        'callback_module' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : ''),
                        'callback_item_id' => (isset($aVals['callback_item_id']) ? (int)$aVals['callback_item_id'] : 0),
                        'parent_user_id' => isset($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0,
                        'title' => isset($aVals['title']) ? $aVals['title'] : '',
                        'text' => '',
                        'description' => isset($aVals['status_info']) ? $aVals['status_info'] : (isset($aVals['description']) ? $aVals['description'] : ''),
                        'updated_info' => 1,
                        'feed_values' => json_encode($aVals),
                        'tagged_friends' => isset($aVals['tagged_friends']) ? $aVals['tagged_friends'] : null,
                        'location_name' => (!empty($aVals['location']['name'])) ? Phpfox::getLib('parse.input')->clean($aVals['location']['name']) : '',
                        'video_embed' => $aVals['video_embed'],
                        'video_source' => 'Uploaded',
                        'is_approved' => !Phpfox::getUserParam('ultimatevideo.ynuv_should_be_approve_before_display_video'),
                        'allow_upload_channel' => isset($aVals['allow_upload_channel']) ? $aVals['allow_upload_channel'] : 0,
                        'category' => json_encode(isset($aVals['category']) ? $aVals['category'] : []),
                        'tag_list' => isset($aVals['tag_list']) ? $aVals['tag_list'] : ''
                    ];

                    $storageData['location_name'] = (!empty($aVals['location']['name'])) ? Phpfox::getLib('parse.input')->clean($aVals['location']['name']) : null;
                    if ((!empty($aVals['location']['latlng']))) {
                        $aMatch = explode(',', $aVals['location']['latlng']);
                        $aMatch['latitude'] = floatval($aMatch[0]);
                        $aMatch['longitude'] = floatval($aMatch[1]);
                        $storageData['location_latlng'] = [
                            'latitude' => $aMatch['latitude'],
                            'longitude' => $aMatch['longitude']
                        ];
                    } else {
                        $storageData['location_latlng'] = '';
                    }

                    storage()->update('ynuv_video_' . $aVals['encoding_id'], $storageData);
                    $addVideo = false;
                }
            }
        }

        if($valid && $addVideo) {
            $aVals['video_embed'] = $aVals['video_link'];
            $aVals['description'] = $aVals['status_info'];
            \Phpfox::getService('ultimatevideo.process')->add($aVals, null, true);
        }


        if (!empty($aVals['callback_module'])) {
            return url()->send($aVals['callback_module'] . '.' . $aVals['callback_item_id'] . '.ultimatevideo');
        } else {
            echo '<script>window.location.href = "'. (!empty($aVals['prev_url']) ? $aVals['prev_url'] : Phpfox_Url::instance()->makeUrl('')) . '";</script>';
        }
    });

    route('/', 'ultimatevideo.index');

    route('/code/:id', 'ultimatevideo.code')
        ->where([':id' => '([0-9]+)']);

    route('/invite/:id', 'ultimatevideo.invite')
        ->where([':id' => '([0-9]+)']);

    route('/embed/:id', 'ultimatevideo.embed')
        ->where([':id' => '([0-9]+)']);

    route('/:id/*', 'ultimatevideo.view')
        ->where([':id' => '([0-9]+)']);

    route('/category/:id/*', 'ultimatevideo.index')
        ->where([':id' => '([0-9]+)']);

    route('/addplaylist/*', 'ultimatevideo.addplaylist');

    route('/add/*', 'ultimatevideo.add');

    route('/playlist/:id/*', 'ultimatevideo.view_playlist')
        ->where([':id' => '([0-9]+)']);

    route('/profile/*', 'ultimatevideo.view');

    route('/playlist/*', 'ultimatevideo.playlist');

    route('/playlist/category/:id/*', 'ultimatevideo.playlist');

    route('/oauth2/*', 'ultimatevideo.oauth2');

    route('/callback/', 'ultimatevideo.callback');

    route('/uploadchannel/:id', 'ultimatevideo.uploadchannel')
        ->where([':id' => '([0-9]+)']);

});

function p_ultimatevideo_n($number, $single, $plural, $translate = 1)
{
    if ($number == 1) {
        return $translate ? _p($single) : $single;
    } else {
        return $translate ? _p($plural) : $plural;
    }
}
Phpfox::getLib('setting')->setParam('ultimatevideo.url_pic', Phpfox::getParam('core.url_pic'));
Phpfox::getLib('setting')->setParam('ultimatevideo.url_pic_default', Phpfox::getParam('core.path_actual'). 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'YouNet_UltimateVideos' . PHPFOX_DS .'assets' . PHPFOX_DS . 'image' . PHPFOX_DS);