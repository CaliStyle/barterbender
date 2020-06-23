<?php

namespace Apps\YouNet_UltimateVideos;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\YouNet_UltimateVideos
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YouNet_UltimateVideos';
    }

    protected function setAlias()
    {

        $this->alias = 'ultimatevideo';
    }

    protected function setName()
    {
        $this->name = 'Ultimate Videos';
    }

    protected function setVersion()
    {
        $this->version = '4.03p4';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setSettings()
    {
        $ordering = 1;
        $this->settings = [
            'ynuv_app_enabled' => [
                'info' => 'Ultimate Videos App Enabled',
                'type' => 'input:radio',
                'value' => '1',
                'js_variable' => '1',
                'ordering' => $ordering++
            ],
            'ynuv_paging_mode' => [
                'var_name' => 'ynuv_paging_mode',
                'info' => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page.',
                'type' => 'select',
                'value' => 'loadmore',
                'options' => [
                    'loadmore' => 'Scrolling down to Load More items',
                    'next_prev' => 'Use Next and Prev buttons',
                    'pagination' => 'Use Pagination with page number'
                ],
                'ordering' => $ordering++
            ],
            'ynuv_enable_uploading_of_videos' => [
                'var_name' => 'ynuv_enable_uploading_of_videos',
                'info' => 'Enable Uploading of Videos',
                'description' => 'Enable this option if you would like to give users the ability to upload videos from their computer. <br/><b>Notice:</b> This feature requires that FFMPEG be installed',
                'type' => 'boolean',
                'value' => '1',
                'ordering' => $ordering++
            ],
            'ynuv_video_method_upload' => [
                'var_name' => 'ynuv_video_method_upload',
                'info' => 'Uploading Method',
                'description' => 'Select which method to encode your videos.',
                'type' => Setting\Site::TYPE_SELECT,
                'value' => '0',
                'options' => ['0' => 'FFMPEG', '1' => 'Zencoder + S3'],
                'ordering' => $ordering++
            ],
            'ynuv_ffmpeg_path' => [
                'info' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
                'value' => '',
                'ordering' => $ordering++
            ],
            'ynuv_video_key' => [
                'var_name' => 'ynuv_video_key',
                'info' => 'Zencoder API Key',
                'ordering' => $ordering++
            ],
            'ynuv_video_s3_key' => [
                'var_name' => 'ynuv_video_s3_key',
                'info' => 'Amazon S3 Access Key',
                'ordering' => $ordering++
            ],
            'ynuv_video_s3_secret' => [
                'var_name' => 'ynuv_video_s3_secret',
                'info' => 'Amazon S3 Secret',
                'ordering' => $ordering++
            ],
            'ynuv_video_s3_bucket' => [
                'var_name' => 'ynuv_video_s3_bucket',
                'info' => 'Amazon S3 Bucket',
                'ordering' => $ordering++
            ],
            'ynuv_video_s3_region' => [
                'var_name' => 'ynuv_video_s3_region',
                'info' => 'Amazon S3 Region',
                'description' => 'This setting is updated from Bucket info. Do not change this value.',
                'ordering' => $ordering++
            ],
            'ynuv_video_s3_url' => [
                'var_name' => 'ynuv_video_s3_url',
                'info' => 'Provide the S3, CloudFront or Custom URL',
                'ordering' => $ordering++
            ],
            'ynuv_cron_limit_per_time' => [
                'info' => 'How many videos you want to allow to run cronjob at the same time?',
                'value' => '5',
                'ordering' => $ordering++
            ],
            'ynuv_youtube_api_key' => [
                'info' => 'YouTube Data API v3 key. Please fill in the api key for parsing youtube video (YouTube Data API v3)',
                'value' => 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M',
                'ordering' => $ordering++
            ],
            'ynuv_allow_user_upload_video_to_yt' => [
                'info' => 'Allow to upload videos to YouTube? <br/> Settings up OAuth 2.0: <a target="_blank" href= "https://developers.google.com/identity/protocols/OpenIDConnect?hl=en#appsetup">https://developers.google.com/identity/protocols/OpenIDConnect?hl=en#appsetup</a> <br/> Authorized redirect URIs: [your_domain]<b>/ultimatevideo/oauth2/</b>',
                'type' => 'input:radio',
                'value' => '1',
                'ordering' => $ordering++
            ],
            'ynuv_youtube_client_id' => ['info' => 'YouTube Client ID', 'value' => '', 'ordering' => $ordering++],
            'ynuv_youtube_client_secret' => ['info' => 'YouTube Client Secret', 'value' => '', 'ordering' => $ordering++],
            'ynuv_meta_description' => [
                'var_name' => 'ynuv_meta_description',
                'info' => 'UltimateVideos Meta Description',
                'description' => 'Meta description added to pages related to the UltimateVideos app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_ynuv_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_ynuv_meta_description"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_ynuv_meta_description\'}',
                'group_id' => 'seo',
                'ordering' => $ordering++
            ],
            'ynuv_meta_keywords' => [
                'var_name' => 'ynuv_meta_keywords',
                'info' => 'UltimateVideos Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the UltimateVideos app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_ynuv_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_ynuv_meta_keywords"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_ynuv_meta_keywords\'}',
                'group_id' => 'seo',
                'ordering' => $ordering++
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'ynuv_time_before_share_other_video' => [
                'info' => 'How many minutes should a user wait before they can share/upload another video? Note: Setting it to "0" (without quotes) is default and users will not have to wait.',
                'type' => 'input:text',
                'value' => '0',
            ],
            'ultimatevideo.points_ultimatevideo_video' => [
                'info' => 'Points received when adding a video.',
                'type' => 'input:text',
                'value' => '1',
                'description' => 'Specify how many points the user will receive when adding a new video.',
            ],
            'ynuv_file_size_limit_in_megabytes' => [
                'info' => 'Maximum file size of video uploaded (MB)?',
                'type' => 'input:text',
                'value' => '100',
            ],
            'ynuv_max_file_size_photos_upload' => [
                'info' => 'Maximum file size of photo uploaded (KB)?',
                'type' => 'input:text',
                'value' => '500',
            ],
            'ynuv_can_approve_video' => [
                'info' => 'Can approve videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '0', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_should_be_approve_before_display_video' => [
                'info' => 'Should videos added by this user group be approved before they are displayed publicly?',
                'type' => 'input:radio',
                'value' => ['1' => '0', '2' => '0', '3' => '1', '4' => '0', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_feature_video' => [
                'info' => 'Can feature videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '0', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_view_video' => [
                'info' => 'Can view videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_upload_video' => [
                'info' => 'Can upload videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_delete_video_of_other_user' => [
                'info' => 'Can delete all videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '0', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_edit_video_of_other_user' => [
                'info' => 'Can edit all videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '0', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_delete_own_video' => [
                'info' => 'Can delete own videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_edit_own_video' => [
                'info' => 'Can edit own videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_add_comment_on_video' => [
                'info' => 'Can add comments on videos?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_how_many_video_user_can_add' => [
                'info' => 'How many videos can a user add?',
                'type' => 'input:text',
                'value' => '10',
            ],
            'ultimatevideo.points_ultimatevideo_playlist' => [
                'info' => 'Points received when adding a playlist.',
                'type' => 'input:text',
                'value' => '1',
                'description' => 'Specify how many points the user will receive when adding a new playlist.',
            ],
            'ynuv_can_approve_playlist' => [
                'info' => 'Can approve playlists?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '0', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_should_be_approve_before_display_playlist' => [
                'info' => 'Should playlist added by this user group be approved before they are displayed publicly?',
                'type' => 'input:radio',
                'value' => ['1' => '0', '2' => '0', '3' => '1', '4' => '0', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_feature_playlist' => [
                'info' => 'Can feature playlists?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '0', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_view_playlist' => [
                'info' => 'Can view playlist?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_add_playlist' => [
                'info' => 'Can add new playlist?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_delete_playlist_of_other_user' => [
                'info' => 'Can delete all playlists?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '0', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_edit_playlist_of_other_user' => [
                'info' => 'Can edit all playlists?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '0', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_delete_own_playlists' => [
                'info' => 'Can delete own playlists?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_edit_own_playlists' => [
                'info' => 'Can edit own playlists?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_can_add_comment_on_playlist' => [
                'info' => 'Can add comments on playlists?',
                'type' => 'input:radio',
                'value' => ['1' => '1', '2' => '1', '3' => '0', '4' => '1', '5' => '0',],
                'options' => ['yes' => 'Yes', 'no' => 'No',],
            ],
            'ynuv_how_many_video_user_can_add_to_playlist' => [
                'info' => 'How many videos can a user add to playlist?',
                'type' => 'input:text',
                'value' => '10',
            ],
            'can_sponsor_video' => [
                'var_name' => 'can_sponsor_video',
                'info' => 'Can members of this user group mark a video as Sponsor without paying fee?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
            ],
            'can_purchase_sponsor_video' => [
                'var_name' => 'can_purchase_sponsor_video',
                'info' => 'Can members of this user group purchase a sponsored ad space for their videos?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ],
            ],
            'ultimatevideo_video_sponsor_price' => [
                'var_name' => 'ultimatevideo_video_sponsor_price',
                'info' => 'How much is the sponsor space worth for videos? This works in a CPM basis.',
                'description' => '',
                'type' => 'currency',
                'value'=> 0,
            ],
            'auto_publish_sponsored_video' => [
                'var_name' => 'auto_publish_sponsored_video',
                'info' => 'Auto publish sponsored video?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    "1" => "1",
                    "2" => "0",
                    "3" => "0",
                    "4" => "0",
                    "5" => "0"
                ]
            ],
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'category' => '',
                'hot_category' => '',
                'tags_video' => '',
                'video_list' => '',
                'playlist_list' => '',
                'your_playlists' => '',
                'playlist_detail_mode_slide' => '',
            ],
            'controller' => [
                'index' => 'ultimatevideo.index',
                'profile' => 'ultimatevideo.profile',
                'view' => 'ultimatevideo.view',
                'playlist' => 'ultimatevideo.playlist',
                'view_playlist' => 'ultimatevideo.view_playlist',
            ],
        ];
    }

    protected function setComponentBlock()
    {
        $ultimatevideo_index_blocks = $ultimatevideo_playlist_blocks = $ultimatevideo_view_blocks = $ultimatevideo_view_playlist_blocks = array();

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "ultimatevideo.index"')
            ->executeField();

        if (!$iCnt) {
            $ultimatevideo_index_blocks = array(
                'Sponsored Videos' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'video_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' =>
                            array(
                                'data_source' => 'sponsor_video',
                                'display_ranking' => '0',
                                'display_view_more' => '0',
                                'limit' => '6',
                                'cache_time' => '5',
                                'is_slider' => '1',
                                'view_modes' => '',
                            ),
                    ),
                'Featured Videos' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'video_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' =>
                            array(
                                'data_source' => 'featured',
                                'display_ranking' => '0',
                                'display_view_more' => '0',
                                'limit' => '6',
                                'cache_time' => '5',
                                'is_slider' => '1',
                                'view_modes' => '',
                            ),
                    ),
                'Hot Categories' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'hot_category',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '3',
                    ),
                'Featured Playlists' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'playlist_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '4',
                        'params' =>
                            array(
                                'data_source' => 'featured',
                                'display_view_more' => '0',
                                'limit' => '6',
                                'cache_time' => '5',
                                'view_modes' => '',
                            ),
                    ),
                'Latest Videos' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'video_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '5',
                        'params' =>
                            array(
                                'data_source' => 'latest',
                                'display_ranking' => '0',
                                'display_view_more' => '0',
                                'limit' => '6',
                                'cache_time' => '5',
                                'is_slider' => '0',
                                'view_modes' =>
                                    array(
                                        0 => 'grid',
                                    ),
                            ),
                    ),
                'Recommended Videos' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'video_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '6',
                        'params' =>
                            array(
                                'data_source' => 'recommended',
                                'display_ranking' => '0',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '5',
                                'is_slider' => '0',
                                'view_modes' =>
                                    array(
                                        0 => 'grid',
                                    ),
                            ),
                    ),
                'Latest Playlists' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'playlist_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '7',
                        'params' =>
                            array(
                                'data_source' => 'latest',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '5',
                                'view_modes' =>
                                    array(
                                        0 => 'grid',
                                    ),
                            ),
                    ),
                'Categories' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'category',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '8',
                    ),
                'Your Playlists' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'your_playlists',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '9',
                        'params' =>
                            array(
                                'display_view_more' => '1',
                                'limit' => '6',
                            ),
                    ),
                'Tags' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'tags_video',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '10',
                    ),
                'Recommended Playlists' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'playlist_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '11',
                        'params' =>
                            array(
                                'data_source' => 'recommended',
                                'display_view_more' => '1',
                                'limit' => '3',
                                'cache_time' => '5',
                                'view_modes' => '',
                            ),
                    ),
                'Most Viewed Videos' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'video_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '12',
                        'params' =>
                            array(
                                'data_source' => 'most_viewed',
                                'display_ranking' => '1',
                                'display_view_more' => '1',
                                'limit' => '3',
                                'cache_time' => '5',
                                'is_slider' => '0',
                                'view_modes' => '',
                            ),
                    ),
                'Top Rated Videos' =>
                    array(
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.index',
                        'component' => 'video_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '13',
                        'params' =>
                            array(
                                'data_source' => 'top_rated',
                                'display_ranking' => '0',
                                'display_view_more' => '1',
                                'limit' => '3',
                                'cache_time' => '5',
                                'is_slider' => '0',
                                'view_modes' => '',
                            ),
                    ),);
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "ultimatevideo.playlist"')
            ->executeField();

        if (!$iCnt) {
            $ultimatevideo_playlist_blocks = array(
                'Playlists - Featured Playlists' =>
                    array(
                        'title' => 'Featured Playlists',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.playlist',
                        'component' => 'playlist_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' =>
                            array(
                                'data_source' => 'featured',
                                'display_view_more' => '0',
                                'limit' => '6',
                                'cache_time' => '5',
                                'view_modes' => '',
                            ),
                    ),
                'Playlists - Latest Playlists' =>
                    array(
                        'title' => 'Latest Playlists',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.playlist',
                        'component' => 'playlist_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '2',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' =>
                            array(
                                'data_source' => 'latest',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '5',
                                'view_modes' =>
                                    array(
                                        0 => 'grid',
                                        1 => 'list',
                                    ),
                            ),
                    ),
                'Category' =>
                    array(
                        'title' => 'Category',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.playlist',
                        'component' => 'category',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '1',
                    ),
                'Playlists - Recommended Playlists' =>
                    array(
                        'title' => 'Recommended Playlists',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.playlist',
                        'component' => 'playlist_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '3',
                        'params' =>
                            array(
                                'data_source' => 'recommended',
                                'display_view_more' => '1',
                                'limit' => '3',
                                'cache_time' => '5',
                                'view_modes' => '',
                            ),
                    ),);
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "ultimatevideo.view"')
            ->executeField();

        if (!$iCnt) {
            $ultimatevideo_view_blocks = array(
                'Related Videos' =>
                    array(
                        'title' => 'Related Videos',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.view',
                        'component' => 'video_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '1',
                        'params' =>
                            array(
                                'data_source' => 'related',
                                'display_ranking' => '0',
                                'display_view_more' => '0',
                                'limit' => '3',
                                'cache_time' => '5',
                                'is_slider' => '0',
                                'view_modes' => '',
                            ),
                    ),
                'More From User' =>
                    array(
                        'title' => 'More From User',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.view',
                        'component' => 'video_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' =>
                            array(
                                'data_source' => 'more_from_user',
                                'display_ranking' => '0',
                                'display_view_more' => '0',
                                'limit' => '3',
                                'cache_time' => '5',
                                'is_slider' => '0',
                                'view_modes' => '',
                            ),
                    )
            );
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "ultimatevideo.view_playlist" AND component = "playlist_detail_mode_slide"')
            ->executeField();

        if (!$iCnt) {
            $ultimatevideo_view_playlist_blocks = array(
                'Playlist Videos' =>
                    array(
                        'title' => 'Playlist Videos',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.view_playlist',
                        'component' => 'playlist_detail_mode_slide',
                        'module_id' => 'ultimatevideo',
                        'location' => '11',
                        'is_active' => '1',
                        'ordering' => '5',
                    )
            );
        } else {
            $ultimatevideo_view_playlist_blocks = array();
        }

        $iCnt = db()->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection = "ultimatevideo.view_playlist"')
            ->executeField();

        if (!$iCnt) {
            $ultimatevideo_view_playlist_blocks = array_merge($ultimatevideo_view_playlist_blocks, array(
                'Playlist: More From User' =>
                    array(
                        'title' => 'More From User',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.view_playlist',
                        'component' => 'playlist_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' =>
                            array(
                                'data_source' => 'more_from_user',
                                'display_view_more' => '1',
                                'limit' => '6',
                                'cache_time' => '5',
                                'view_modes' =>
                                    array(
                                        0 => 'grid',
                                        1 => 'list',
                                    ),
                            ),
                    ),
                'Related Playlists' =>
                    array(
                        'title' => 'Related Playlists',
                        'type_id' => '0',
                        'm_connection' => 'ultimatevideo.view_playlist',
                        'component' => 'playlist_list',
                        'module_id' => 'ultimatevideo',
                        'location' => '3',
                        'is_active' => '1',
                        'ordering' => '2',
                        'params' =>
                            array(
                                'data_source' => 'related',
                                'display_view_more' => '1',
                                'limit' => '3',
                                'cache_time' => '5',
                                'view_modes' =>
                                    array(
                                        0 => 'grid',
                                        1 => 'list',
                                    ),
                            ),
                    ),
            ));
        }

        $this->component_block = array_merge(
            $ultimatevideo_index_blocks,
            $ultimatevideo_playlist_blocks,
            $ultimatevideo_view_blocks,
            $ultimatevideo_view_playlist_blocks
        );
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->notifications = [];
        $this->admincp_route = '/ultimatevideo/admincp';
        $this->admincp_menu = [
            'Manage Categories' => '#',
            'Add New Category' => 'ultimatevideo.category-add',
            'Add New Custom Field Groups' => 'ultimatevideo.customfield-add',
            'Manage Custom Field Groups' => 'ultimatevideo.customfield',
            'Video Ultilities' => 'ultimatevideo.ultilities',
            'Manage Videos' => 'ultimatevideo.managevideos',
            'Manage Playlists' => 'ultimatevideo.manageplaylists',
        ];

        $this->menu = ['name' => 'Ultimate Videos', 'url' => '/ultimatevideo', 'icon' => 'video-camera',];

        $this->icon = 'http://static.demo.younetco.com/ynicons/fox4/ultimate_video.png';

        $this->_publisher = 'YouNetCo';
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher_url = 'https://phpfox.younetco.com/';
    }
}