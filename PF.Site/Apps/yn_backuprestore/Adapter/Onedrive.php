<?php
/**
 * User: huydnt
 * Date: 06/01/2017
 * Time: 10:09
 */

namespace Apps\yn_backuprestore\Adapter;

require_once "libs/onedrive/vendor/autoload.php";
use Krizalys\Onedrive\Client;

if (!isset($_SESSION)) {
    session_start();
}

class Onedrive extends Abstracts
{
    private $_client;

    public function __construct($client_id, $state = null)
    {
        $aClientConstruct = array(
            'client_id' => $client_id,
        );
        if ($state) {
            $aClientConstruct['state'] = $state;
        }
        $this->_client = new Client($aClientConstruct);
    }

    /**
     * Get access token
     * @param $client_secret
     * @param $code
     * @return object
     */
    public function getAccessToken($client_secret, $code)
    {
        $this->_client->obtainAccessToken($client_secret, $code);

        return $this->_client->getState();
    }

    /**
     * Upload file to One Drive
     * @param $client_secret
     * @param $remote_path
     * @param $file_path
     */
    public function upload($client_secret, $remote_path, $file_path)
    {
        // renew access token
        if (!$this->_client->getAccessTokenStatus()) {
            $this->_client->renewAccessToken($client_secret);
        }

        $remotePathParsed = array_filter(explode(PHPFOX_DS, $remote_path));

        $root = (array)$this->_client->fetchRoot();
        if(empty($root['value']) || count($root['value']) == 0) {
            return false;
        }

        $items = $root['value'];
        $isRoot = true;

        foreach ($remotePathParsed as $path) {
            $folder_created = false;
            $parent = null;
            if($isRoot) {
                foreach($items as $child) {
                    $child = (array)$child;
                    if(isset($child['folder']) && $child['name'] == $path) {
                        $folder_created = true;
                        $parent = $child;
                        break;
                    }
                }
                $isRoot = false;
            }
            else {
                if(isset($parent)) {
                    $items = (array)$this->_client->fetchObjects($parent['id']);
                    foreach ($items as $child) {
                        $child = (array)$child;
                        if (isset($child['folder']) && $child['name'] == $path) {
                            $folder_created = true;
                            $parent = $child;
                        }
                    }
                }
            }

            // create folder if not exist
            if (isset($parent) && !$folder_created) {
                $parent = (array)$this->_client->createFolder($path, $parent['id']);
            }
        }

        // upload file to folder
        $this->_client->createFile($file_path, $remote_path);
    }

    /**
     * Authorize, get code
     * @param $redirect_uri
     */
    public function authorize($redirect_uri)
    {
        // Gets a log in URL with sufficient privileges from the OneDrive API.
        $url = $this->_client->getLogInUrl(array(
            'files.readwrite',
            'offline_access'
        ), $redirect_uri);

        $_SESSION['onedrive_client_state'] = $this->_client->getState();
        header('Location: ' . $url);
    }

    /**
     * Get access token status
     * <br>0 No access token.
     * <br>-1 Access token will expire soon (1 minute or less).
     * <br> -2 Access token is expired.
     * <br> 1 Access token is valid.
     * @return int
     */
    public function getAccessTokenStatus()
    {
        return $this->_client->getAccessTokenStatus();
    }

    /**
     * Renew access token
     * @param $client_secret
     */
    public function renewAccessToken($client_secret)
    {
        $this->_client->renewAccessToken($client_secret);
    }

    /**
     * Get state containing access token
     */
    public function getState() {
        return $this->_client->getState();
    }
}