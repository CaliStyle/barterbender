<?php
/**
 * User: huydnt
 * Date: 06/01/2017
 * Time: 09:48
 */

namespace Apps\yn_backuprestore\Adapter;

require_once 'libs/vendor/autoload.php';
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;

class Dropb extends Abstracts
{
    private $dropbox;

    public function __construct($app_key, $app_secret, $access_token)
    {
        //Configure Dropbox Application
        $app = new DropboxApp($app_key, $app_secret, $access_token);
        //Configure Dropbox service
        $this->dropbox = new Dropbox($app);
    }

    /**
     * Upload file to dropbox
     * @param $location
     * @param $file_path
     */
    public function upload($location, $file_path)
    {
        $dropboxFile = new DropboxFile($file_path);
        if ($location) {
            $location = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR,
                    array_filter(explode('/', $location)));
        }
        $this->dropbox->upload($dropboxFile, $location . DIRECTORY_SEPARATOR . basename($file_path),
            ['autorename' => true]);
    }
}