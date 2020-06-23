<?php
/**
 * Created by PhpStorm.
 * User: davidnguyen
 * Date: 7/27/16
 * Time: 9:27 AM
 */

require_once 'Google/autoload.php';
require_once 'Google/Client.php';
require_once 'Google/Service/YouTube.php';

namespace Apps\YouNet_UltimateVideos\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Database;

defined('PHPFOX') or exit('NO DICE!');

class Api extends Phpfox_Service
{
    public function __construct()
    {
        $this->_vTable = Phpfox::getT('ynultimatevideo_videos');
    }

    protected function database()
    {
        return Phpfox_Database::instance();
    }

    public function uploadVideoToChannel($uploadedVideo)
    {
        /*
         * You can acquire an OAuth 2.0 client ID and client secret from the
         * Google Developers Console <https://console.developers.google.com/>
         * For more information about using OAuth 2.0 to access Google APIs, please see:
         * <https://developers.google.com/youtube/v3/guides/authentication>
         * Please ensure that you have enabled the YouTube Data API for your project.
         */
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $owner = $uploadedVideo -> getOwner();

        // get allow to upload to youtube (user and admin)
        $youtube_allow = $settings->getSetting('ynultimatevideo_youtube_allow', 0);
        $user_allow = $uploadedVideo -> allow_upload_channel;
        if(!$youtube_allow || !$user_allow)
        {
            throw new Ynultimatevideo_Model_Exception('Not allow upload to YouTube');
        }

        // Get token from user (owner of video)
        $token = $uploadedVideo -> user_token;

        // Get Client ID and Client secret key from Youtube API
        $OAUTH2_CLIENT_ID = $settings->getSetting('ynultimatevideo_youtube_clientid', "");
        $OAUTH2_CLIENT_SECRET = $settings->getSetting('ynultimatevideo_youtube_secret', "");

        if(empty($OAUTH2_CLIENT_ID) || empty($OAUTH2_CLIENT_SECRET) || empty($token)) {
            throw new Ynultimatevideo_Model_Exception('YouTube settings were missing');
        }

        // get new google client
        $client = new Google_Client();
        $client->setClientId($OAUTH2_CLIENT_ID);
        $client->setClientSecret($OAUTH2_CLIENT_SECRET);
        $client->setAccessType('offline');
        $client->setAccessToken($token);

        /**
         * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
         */
        if($client->isAccessTokenExpired()) {
            $newToken = json_decode($client->getAccessToken());
            $client->refreshToken($newToken->refresh_token);
        }

        // Check to ensure that the access token was successfully acquired.
        if ($client->getAccessToken()) {
            try{

                // Define an object that will be used to make all API requests.
                $youtube = new Google_Service_YouTube($client);

                $storageObject = Engine_Api::_() -> getItem('storage_file', $uploadedVideo -> file_id);
                if (!$storageObject)
                {
                    throw new Ynultimatevideo_Model_Exception('Video storage file was missing');
                }

                $originalPath = $storageObject -> temporary();
                if (!file_exists($originalPath))
                {
                    throw new Ynultimatevideo_Model_Exception('Could not pull to temporary file');
                }

                // Create a snippet with title, description, tags and category ID
                // Create an asset resource and set its snippet metadata and type.
                // This example sets the video's title, description, keyword tags, and
                // video category.
                $snippet = new Google_Service_YouTube_VideoSnippet();
                $snippet->setTitle($uploadedVideo -> getTitle());
                $snippet->setDescription($uploadedVideo -> getDescription());

                // Numeric video category. See
                // https://developers.google.com/youtube/v3/docs/videoCategories/list
                $snippet->setCategoryId("22");

                // Set the video's status to "public". Valid statuses are "public",
                // "private" and "unlisted".
                $status = new Google_Service_YouTube_VideoStatus();
                $status->privacyStatus = "public";

                // Associate the snippet and status objects with a new video resource.
                $video = new Google_Service_YouTube_Video();
                $video->setSnippet($snippet);
                $video->setStatus($status);

                // Specify the size of each chunk of data, in bytes. Set a higher value for
                // reliable connection as fewer chunks lead to faster uploads. Set a lower
                // value for better recovery on less reliable connections.
                $chunkSizeBytes = 1 * 1024 * 1024;

                // Setting the defer flag to true tells the client to return a request which can be called
                // with ->execute(); instead of making the API call immediately.
                $client->setDefer(true);

                // Create a request for the API's videos.insert method to create and upload the video.
                $insertRequest = $youtube->videos->insert("status,snippet", $video);

                // Create a MediaFileUpload object for resumable uploads.
                $media = new Google_Http_MediaFileUpload(
                    $client,
                    $insertRequest,
                    'video/*',
                    null,
                    true,
                    $chunkSizeBytes
                );
                $media->setFileSize(filesize($originalPath));

                // Read the media file and upload it chunk by chunk.
                $status = false;
                $handle = fopen($originalPath, "rb");
                while (!$status && !feof($handle)) {
                    $chunk = fread($handle, $chunkSizeBytes);
                    $status = $media->nextChunk($chunk);
                }
                fclose($handle);

                // If you want to make other calls after the file upload, set setDefer back to false
                $client->setDefer(false);

                //update video data (replace uploaded video to youtube video)
                $uploadedVideo -> code = $status['id'];
                $uploadedVideo -> file_id = 0;
                $uploadedVideo -> type = 1;
                $uploadedVideo -> status = 1;
                $uploadedVideo -> allow_upload_channel = 0;
                $uploadedVideo -> user_token = "";
                $uploadedVideo -> save();

                // save thumbnail
                $adapter = Ynultimatevideo_Plugin_Factory::getPlugin($uploadedVideo -> type);
                $adapter -> setParams(array(
                    'code' => $uploadedVideo -> code,
                    'video_id' => $uploadedVideo -> getIdentity()
                ));

                if($adapter -> getVideoLargeImage())
                    $uploadedVideo -> setPhoto($adapter -> getVideoLargeImage());

                if($adapter -> getVideoDuration())
                    $uploadedVideo -> duration = $adapter -> getVideoDuration();
                $uploadedVideo -> save();

                // delete old video
                $storageObject -> delete();

                // delete temporary file
                unlink($originalPath);

                // insert action in a seperate transaction if video status is a success
                $actionsTable = Engine_Api::_() -> getDbtable('actions', 'activity');
                $db = $actionsTable -> getAdapter();
                $db -> beginTransaction();

                try
                {
                    // new action
                    $item = Engine_Api::_() -> getItem($uploadedVideo -> parent_type, $uploadedVideo -> parent_id);
                    if ($uploadedVideo -> parent_type == 'group')
                    {
                        $action = $actionsTable -> addActivity($owner, $item, 'advgroup_video_create');
                    }
                    elseif ($uploadedVideo -> parent_type == 'event')
                    {
                        $action = $actionsTable -> addActivity($owner, $item, 'ynevent_video_create');
                    }
                    else
                    {
                        $action = $actionsTable -> addActivity($owner, $uploadedVideo, 'ynultimatevideo_new');
                    }
                    if ($action)
                    {
                        $actionsTable -> attachActivity($action, $uploadedVideo);
                    }

                    // notify the owner
                    Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($owner, $owner, $uploadedVideo, 'ynultimatevideo_processed');

                    $db -> commit();
                }
                catch (Exception $e)
                {
                    $db -> rollBack();
                    throw $e;
                }

            }
            catch (Exception $e)
            {
                unlink($originalPath);
                $uploadedVideo -> status = 7;
                $uploadedVideo -> save();
                // notify the owner
                $translate = Zend_Registry::get('Zend_Translate');
                Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($owner, $owner, $uploadedVideo, 'ynultimatevideo_processed_failed', array(
                    'message' => $translate -> translate('Video conversion failed.'),
                    'message_link' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynultimatevideo_general', true),
                ));
                throw new Ynultimatevideo_Model_Exception($e -> getMessage());
            }
        }
    }
}