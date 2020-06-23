<?php

namespace Apps\PHPfox_Videos\Controller;

use Phpfox;
use Phpfox_Component;
use Aws\S3\S3Client;

defined('PHPFOX') or exit('NO DICE!');

class CallbackController extends Phpfox_Component
{
    public function process()
    {
        $notification = json_decode(trim(file_get_contents('php://input')), true);

        if(defined('PHPFOX_DEBUG') && PHPFOX_DEBUG) {
            Phpfox::getLog('v_zencoder_callback.log')->info($notification);
        }

        if (isset($notification['job']) && isset($notification['job']['state'])) {
            if ($notification['job']['state'] == 'finished') {
                $encoding = storage()->get('pf_video_' . $notification['job']['id']);
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
                        $aVals = array(
                            'privacy' => $encodingValue->privacy,
                            'privacy_list' => json_decode($encodingValue->privacy_list),
                            'callback_module' => $encodingValue->callback_module,
                            'callback_item_id' => $encodingValue->callback_item_id,
                            'parent_user_id' => $encodingValue->parent_user_id,
                            'title' => $encodingValue->title,
                            'category' => json_decode($encodingValue->category),
                            'text' => $encodingValue->text,
                            'status_info' => $encodingValue->status_info,
                            'is_stream' => 0,
                            'view_id' => $encodingValue->view_id,
                            'user_id' => $userId,
                            'server_id' => -1,
                            'path' => $encodingValue->video_path,
                            'ext' => $encodingValue->ext,
                            'default_image' => $encodingValue->default_image,
                            'image_server_id' => -1,
                            'duration' => $iDuration,
                            'video_size' => $iVideoSize,
                            'photo_size' => $iPhotoSize,
                            'feed_values' => isset($encodingValue->feed_values) ? json_decode($encodingValue->feed_values) : [],
                            'location_name' => $encodingValue->location_name,
                            'location_latlng' => $encodingValue->location_latlng,
                            'tagged_friends' => $encodingValue->tagged_friends
                        );
                        if (!defined('PHPFOX_FEED_NO_CHECK')) {
                            define('PHPFOX_FEED_NO_CHECK', true);
                        }
                        $iId = Phpfox::getService('v.process')->addVideo($aVals);

                        if (Phpfox::isModule('notification')) {
                            Phpfox::getService('notification.process')->add('v_ready', $iId, $userId,
                                $userId, true);
                        }

                        Phpfox::getLib('mail')->to($userId)
                            ->subject('video_is_ready')
                            ->message(['your_video_is_ready_link', ['link' => url('/video/play/' . $iId)]])
                            ->send();
                        storage()->del('pf_video_' . $notification['job']['id']);
                    } else {
                        storage()->update('pf_video_' . $notification['job']['id'], [
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
                        'region' => setting('pf_video_s3_region', 'us-east-2'),
                        'version' => 'latest',
                        'credentials' => [
                            'key' => setting('pf_video_s3_key'),
                            'secret' => setting('pf_video_s3_secret'),
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
                            'Bucket' => setting('pf_video_s3_bucket'),
                            'Key' => $sPath . $ext
                        ]);
                    }
                    storage()->del('pf_video_' . $notification['job']['id']);
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
