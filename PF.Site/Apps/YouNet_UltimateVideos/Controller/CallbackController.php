<?php
namespace Apps\YouNet_UltimateVideos\Controller;

use Phpfox_Component;
use Phpfox;
use Aws\S3\S3Client;

class CallbackController extends Phpfox_Component
{
    public function process()
    {
        $notification = json_decode(trim(file_get_contents('php://input')), true);

        if(defined('PHPFOX_DEBUG') && PHPFOX_DEBUG) {
            Phpfox::getLog('ultimatevideo_zencoder_callback.log')->info($notification);
        }


        if (isset($notification['job']) && isset($notification['job']['state'])) {
            if ($notification['job']['state'] == 'finished') {
                $encoding = storage()->get('ynuv_video_' . $notification['job']['id']);

                if (empty($encoding->value->cancel_upload)) {
                    $iDuration = 0;
                    $iVideoSize = 0;
                    $iPhotoSize = 0;
                    if (isset($notification['outputs'][0])) {
                        $iDuration = (int)($notification['outputs'][0]['duration_in_ms'] / 1000);
                        $iVideoSize = (int)($notification['outputs'][0]['file_size_in_bytes']);
                        if (isset($notification['outputs'][0]['thumbnails'][0]['images'][1])) {
                            $iPhotoSize = (int)($notification['outputs'][0]['thumbnails'][0]['images'][1]['file_size_bytes']);
                        }
                    }
                    $encodingValue = $encoding->value;
                    if (!empty($encodingValue->updated_info)) {
                        $userId = $encodingValue->user_id;
                        $vals = array(
                            'privacy' => $encodingValue->privacy,
                            'privacy_list' => json_decode($encodingValue->privacy_list, true),
                            'callback_module' => $encodingValue->callback_module,
                            'callback_item_id' => $encodingValue->callback_item_id,
                            'parent_user_id' => $encodingValue->parent_user_id,
                            'title' => $encodingValue->title,
                            'description' => $encodingValue->description,
                            'is_stream' => 0,
                            'is_approved' => $encodingValue->is_approved,
                            'user_id' => $userId,
                            'video_server_id' => -1,
                            'video_path' => $encodingValue->video_path,
                            'ext' => $encodingValue->ext,
                            'image_path' => $encodingValue->default_image,
                            'image_server_id' => -1,
                            'duration' => $iDuration,
                            'video_size' => $iVideoSize,
                            'photo_size' => $iPhotoSize,
                            'feed_values' => isset($encodingValue->feed_values) ? json_decode($encodingValue->feed_values, true) : [],
                            'location' => [
                                'location_name' => $encodingValue->location_name,
                                'location_latlng' => $encodingValue->location_latlng,
                            ],
                            'tagged_friends' => $encodingValue->tagged_friends,
                            'video_source' => $encodingValue->video_source,
                            'video_code' => 1,
                            'force_status' => 1,
                            'allow_upload_channel' => $encodingValue->allow_upload_channel,
                            'category' => json_decode($encodingValue->category),
                            'tag_list' => $encodingValue->tag_list
                        );
                        if (!defined('PHPFOX_FEED_NO_CHECK')) {
                            define('PHPFOX_FEED_NO_CHECK', true);
                        }

                        $id = \Phpfox::getService('ultimatevideo.process')->add($vals, null, true);

                        if (Phpfox::isModule('notification')) {
                            Phpfox::getService('notification.process')->add('ultimatevideo_videoconvert', $id, $userId, $userId, true);
                        }

                        if($vals['is_approved']) {
                            if (isset($vals['callback_module']) && Phpfox::isModule($vals['callback_module']) && Phpfox::hasCallback($vals['callback_module'], 'getFeedDetails')) {
                                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($vals['callback_module'] . '.getFeedDetails', $vals['callback_item_id']))->add('ultimatevideo_video', $id, $vals['privacy'], (isset($vals['privacy_comment']) ? (int)$vals['privacy_comment'] : 0), $vals['callback_item_id'], $userId) : null);
                            } else {
                                ((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? Phpfox::getService('feed.process')->add('ultimatevideo_video', $id, $vals['privacy'], 0, 0, $userId) : null);
                            }
                        }

                        storage()->del('ynuv_video_' . $notification['job']['id']);
                    } else {
                        storage()->update('ynuv_video_' . $notification['job']['id'], [
                            'encoded' => 1,
                            'server_id' => -1,
                            'image_server_id' => -1,
                            'duration' => $iDuration,
                            'video_size' => $iVideoSize,
                            'photo_size' => $iPhotoSize
                        ]);
                    }
                } else {
                    $sPath = str_replace('.mp4', '', $encoding->value->video_path);
                    $oClient = new S3Client([
                        'region' => Phpfox::getParam('ultimatevideo.ynuv_video_s3_region', 'us-east-2'),
                        'version' => 'latest',
                        'credentials' => [
                            'key' => Phpfox::getParam('ultimatevideo.ynuv_video_s3_key'),
                            'secret' => Phpfox::getParam('ultimatevideo.ynuv_video_s3_secret'),
                        ],
                    ]);
                    foreach ([
                                 '.webm',
                                 '-low.mp4',
                                 '.ogg',
                                 '.mp4',
                                 '.png/frame_0000.png',
                                 '.png/frame_0001.png',
                                 '.png/frame_0002.png'
                             ] as $ext) {
                        $oClient->deleteObject([
                            'Bucket' => Phpfox::getParam('ultimatevideo.ynuv_video_s3_bucket'),
                            'Key' => $sPath . $ext
                        ]);
                    }
                    storage()->del('ynuv_video_' . $notification['job']['id']);
                }
                if($encoding) {
                    $file = PHPFOX_DIR_FILE . 'static/' . $encoding->value->id . '.' . $encoding->value->ext;
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                }
            }
        }
        exit;
    }
}