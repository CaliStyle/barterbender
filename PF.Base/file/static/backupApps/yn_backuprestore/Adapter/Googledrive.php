<?php
/**
 * User: huydnt
 * Date: 06/01/2017
 * Time: 11:46
 */

namespace Apps\yn_backuprestore\Adapter;

require_once 'libs/vendor/autoload.php';
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

if(!function_exists('mime_content_type')) {
    function mime_content_type($filename) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = @strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

class Googledrive extends Abstracts
{
    private $_client;

    public function __construct($client_id, $client_secret, $redirect_uri, $access_token = null)
    {
        $this->_client = new Google_Client();
        $this->_client->setClientId($client_id);
        $this->_client->setClientSecret($client_secret);
        $this->_client->setScopes(array('https://www.googleapis.com/auth/drive.file'));
        $this->_client->setAccessType('offline');
        $this->_client->setRedirectUri($redirect_uri);
        if ($access_token) {
            $this->_client->setAccessToken($access_token);
        }
    }

    /**
     * Access token is expired
     * @return bool
     */
    public function isAccessTokenExpired()
    {
        return $this->_client->isAccessTokenExpired();
    }

    /**
     * Refresh token
     * @return array|bool
     */
    public function refreshToken()
    {
        $token = $this->_client->refreshToken($this->_client->getRefreshToken());
        if (isset($token['error']))
            return false;

        return $token;
    }

    /**
     * Redirect to authorize
     */
    public function authorize()
    {
        $authUrl = $this->_client->createAuthUrl();
        header('Location: ' . $authUrl);
    }

    /**
     * Get access token from code
     * @param $code
     * @return array|string
     */
    public function getAccessToken($code)
    {
        $this->_client->authenticate($code);
        return $this->_client->getAccessToken();
    }

    /**
     * Upload to google drive
     * @param $remote_path
     * @param $file_path
     * @internal param $access_token
     */
    public function upload($remote_path, $file_path)
    {
        $service = new Google_Service_Drive($this->_client);

        $parent = null;
        $remote_path = array_filter(explode(PHPFOX_DS, $remote_path));
        foreach ($remote_path as $folder) {
            // check if already had folder
            $searchInParent = '';
            if ($parent) {
                $searchInParent = " and '$parent->id' in parents";
            }
            $response = $service->files->listFiles(array(
                'q'      => "mimeType='application/vnd.google-apps.folder' and name='$folder' and trashed=false" . $searchInParent,
                'spaces' => 'drive',
                'fields' => 'nextPageToken, files(id, name)',
            ));
            // if had folder
            if (count($response->getFiles())) {
                $parent = $response->getFiles()[0];
            } else // if not, create new folder
            {
                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name'     => $folder,
                    'mimeType' => 'application/vnd.google-apps.folder',
                    'parents'  => array(($parent) ? $parent->id : null),
                ));
                $parent = $service->files->create($fileMetadata, array('fields' => 'id'));
            }
        }

        //Insert a file
        if ($parent) {
            $file = new Google_Service_Drive_DriveFile(array(
                'parents' => array($parent->id)
            ));
        } else {
            $file = new Google_Service_Drive_DriveFile();
        }

        $file->setName(basename($file_path));
        $file->setDescription('File Backup');
        $file->setMimeType(mime_content_type($file_path));

        $data = file_get_contents($file_path);

        $service->files->create($file, array(
            'data'       => $data,
            'mimeType'   => mime_content_type($file_path),
            'uploadType' => 'multipart'
        ));
    }

    /**
     * Set access token
     * @param $accessToken
     */
    public function setAccessToken($accessToken) {
        $this->_client->setAccessToken($accessToken);
    }
}