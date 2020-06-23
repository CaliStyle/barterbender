<?php
/**
 * User: huydnt
 * Date: 17/01/2017
 * Time: 11:09
 */

namespace Apps\yn_backuprestore\Service;

use Apps\yn_backuprestore\Adapter\Mysql;
use Phpfox;

class Restore extends \Phpfox_Service
{
    protected $_sRestoreDir;
    protected $_excludedDirs;

    public function __construct()
    {
        $this->_sRestoreDir = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS;
        $this->_excludedDirs  = [
            '.',
            '..',
            '.htaccess',
            'index.html',
            'cache',
            'css',
            'gzip',
            'log',
            'session',
            'settings',
            'static',
            'ynbackuprestore',
            'yn_backuprestore',
            'backuprestore',
        ];
    }


    /**
     * Initialize restore: extract compress file
     * @param $fileName
     * @return bool|string
     */
    public function initializeProcess($fileName)
    {
        // extract backup file
        $arr = explode('.', $fileName);
        $fileExt = $arr[count($arr) - 1];
        $old = umask(0);
        switch ($fileExt) {
            case 'zip':
                $basename = substr($fileName, 0, strlen($fileName) - 4);
                $command = "unzip $this->_sRestoreDir$fileName -d $this->_sRestoreDir$basename/";
                exec($command);
                break;
            case 'tar':
                $basename = substr($fileName, 0, strlen($fileName) - 4);
                if (!is_dir($this->_sRestoreDir . $basename)) {
                    mkdir($this->_sRestoreDir . $basename, 0777);
                }
                $command = "tar -xvf $this->_sRestoreDir$fileName -C $this->_sRestoreDir$basename/";
                exec($command);
                break;
            case 'gz':
                $basename = substr($fileName, 0, strlen($fileName) - 7);
                if (!is_dir($this->_sRestoreDir . $basename)) {
                    mkdir($this->_sRestoreDir . $basename, 0777);
                }
                $command = "tar -zxvf $this->_sRestoreDir$fileName -C $this->_sRestoreDir$basename/";
                exec($command);
                break;
            case 'bz2':
                $basename = substr($fileName, 0, strlen($fileName) - 8);
                if (!is_dir($this->_sRestoreDir . $basename)) {
                    mkdir($this->_sRestoreDir . $basename, 0777);
                }
                $command = "tar -jxvf $this->_sRestoreDir$fileName -C $this->_sRestoreDir$basename/";
                exec($command);
                break;
            default:
                break;
        }
        umask($old);

        return true;
    }

    public function validateFolderPermission($fileName)
    {
        $noPermissionDirs = [];
        $folderName = $this->getBasename($fileName);

        // check module and app permission
        $root = '';
        $pluginsDir = $this->_sRestoreDir . $folderName . PHPFOX_DS . 'plugins' . PHPFOX_DS;
        $plugins = array_diff(scandir($pluginsDir), $this->_excludedDirs);
        if (is_array($plugins) && count($plugins)) {
            foreach ($plugins as $plugin) {
                if ($plugin == 'Apps') {
                    $root = PHPFOX_DIR_SITE_APPS;
                } elseif ($plugin == 'module') {
                    $root = PHPFOX_DIR_MODULE;
                }
                $folders = scandir($pluginsDir . $plugin);
                foreach ($folders as $folder) {
                    $checkedFolder = $root . $folder;
                    if (file_exists($checkedFolder) && !is_writable($checkedFolder)) {
                        $noPermissionDirs[] = $checkedFolder;
                    }
                }
            }
        }

        $themesDir = $this->_sRestoreDir . $folderName . PHPFOX_DS . 'themes' . PHPFOX_DS;
        $themes = array_diff(scandir($themesDir), $this->_excludedDirs);
        if (is_array($themes) && count($themes)) {
            foreach ($themes as $theme) {
                $folders = scandir($themesDir . $theme);
                foreach ($folders as $folder) {
                    $checkedFolder = PHPFOX_DIR_THEME . $folder;
                    if (file_exists($checkedFolder) && !is_writable($checkedFolder)) {
                        $noPermissionDirs[] = $checkedFolder;
                    }
                }
            }
        }

        $uploadsDir = $this->_sRestoreDir . $folderName . PHPFOX_DS . 'uploads' . PHPFOX_DS;
        $uploads = array_diff(scandir($uploadsDir), $this->_excludedDirs);
        if (is_array($uploads) && count($uploads)) {
            foreach ($uploads as $upload) {
                $folders = scandir($uploadsDir . $upload);
                foreach ($folders as $folder) {
                    $checkedFolder = PHPFOX_DIR_FILE . $folder;
                    if (file_exists($checkedFolder) && !is_writable($checkedFolder)) {
                        $noPermissionDirs[] = $checkedFolder;
                    }
                }
            }
        }

        return $noPermissionDirs;
    }

    /**
     * Restore to destination
     * @param $source
     * @param $destination
     */
    private function restore($source, $destination)
    {
        $command = "cp -a $source. $destination";
        shell_exec($command);
    }

    private function getBasename($filePath)
    {
        $arr = explode('.', $filePath);
        $fileExt = $arr[count($arr) - 1];
        switch ($fileExt) {
            case 'zip':
                $basename = substr($filePath, 0, strlen($filePath) - 4);
                break;
            case 'tar':
                $basename = substr($filePath, 0, strlen($filePath) - 4);
                break;
            case 'gz':
                $basename = substr($filePath, 0, strlen($filePath) - 7);
                break;
            case 'bz2':
                $basename = substr($filePath, 0, strlen($filePath) - 8);
                break;
            default:
                $basename = false;
                break;
        }
        return $basename;
    }

    /**
     * Restore all plugins
     * @param $fileName
     * @return bool
     */
    public function restorePlugins($fileName)
    {
        $folderName = $this->getBasename($fileName);
        $pluginsDir = $this->_sRestoreDir . $folderName . PHPFOX_DS . 'plugins' . PHPFOX_DS;
        try {
            $plugins = array_diff(scandir($pluginsDir), $this->_excludedDirs);
        } catch (\Exception $e) {
            return false;
        }
        if (is_array($plugins) && count($plugins)) {
            foreach ($plugins as $plugin) {
                if ($plugin == 'Apps') {
                    $this->restore($pluginsDir . $plugin . PHPFOX_DS, PHPFOX_DIR_SITE_APPS);
                    /**
                     * Code below is for testing
                     */
//                    $this->restore($pluginsDir . $plugin . PHPFOX_DS, '/opt/lampp/htdocs/phpfox4/PF.Site/Apps/');
                } elseif ($plugin == 'module') {
                    $this->restore($pluginsDir . $plugin . PHPFOX_DS, PHPFOX_DIR_MODULE);
                    /**
                     * Code below is for testing
                     */
//                    $this->restore($pluginsDir . $plugin . PHPFOX_DS, '/opt/lampp/htdocs/phpfox4/PF.Base/module/');
                }
            }
        }
        return true;
    }

    /**
     * Restore themes
     * @param $fileName
     * @return bool
     * @internal param $fileName
     */
    public function restoreThemes($fileName)
    {
        $folderName = $this->getBasename($fileName);
        $themesDir = $this->_sRestoreDir . $folderName . PHPFOX_DS . 'themes' . PHPFOX_DS;
        try {
            $themes = array_diff(scandir($themesDir), $this->_excludedDirs);
        } catch (\Exception$e) {
            return false;
        }
        if (is_array($themes) && count($themes)) {
            $this->restore($themesDir, PHPFOX_DIR_THEME);
            /**
             * Code below is for testing
             */
//            $this->restore($themesDir . PHPFOX_DS, '/opt/lampp/htdocs/phpfox4/PF.Site/flavors/');
        }
        return true;
    }

    /**
     * Restore Uploads folders
     * @param $fileName
     * @return bool
     * @internal param $fileName
     */
    public function restoreUploads($fileName)
    {
        $folderName = $this->getBasename($fileName);
        $uploadsDir = $this->_sRestoreDir . $folderName . PHPFOX_DS . 'uploads' . PHPFOX_DS;
        try {
            $folders = array_diff(scandir($uploadsDir), $this->_excludedDirs);
        } catch (\Exception $e) {
            return false;
        }
        if (is_array($folders) && count($folders)) {
            $this->restore($uploadsDir, PHPFOX_DIR_FILE);
            /**
             * Code below is for testing
             */
//            $this->restore($uploadsDir . PHPFOX_DS, '/opt/lampp/htdocs/phpfox4/PF.Base/file/');
        }
        return true;
    }

    /**
     * Restore Database
     * @param $fileName
     * @return bool
     */
    public function restoreDatabase($fileName)
    {
        $folderName = $this->getBasename($fileName);

        $fileDir = $this->_sRestoreDir . $folderName . PHPFOX_DS . 'database.sql';
        if (file_exists($fileDir)) {
            $mysql = new Mysql();
            include PHPFOX_DIR_SETTINGS . 'server.sett.php';

            // Prepare params
            $host = $_CONF['db']['host'];
            $user = $_CONF['db']['user'];
            $pass = $_CONF['db']['pass'];
            $name = $_CONF['db']['name'];
            $port = $_CONF['db']['port'];

            if ($mysql->connect($host, $user, $pass, $name, $port)
            ) {
                // Run cmd
                $mysql_port = !empty($port) ? ' -P'.$port : '';
                exec("mysql -h$host -u$user -p$pass" . $mysql_port . " $name <$fileDir");
            }
            /**
             * Code below for testing
             */
//            if ($mysql->connect('localhost', 'root', '', 'xxxxx')) {
//                $mysql->upload($fileDir);
//            }
        }
        return true;
    }

    /**
     * Clean up backup file
     * @param $fileName
     * @return bool
     * @internal param $fileName
     */
    public function cleanup($fileName)
    {
        // clean up
        $folderName = $this->getBasename($fileName);
        $folderDir = $this->_sRestoreDir . $folderName . PHPFOX_DS;
        $fileDir = $this->_sRestoreDir . $fileName;
        exec("rm -rf $folderDir");
        exec("rm -rf $fileDir");

        // online site
        Phpfox::getService('admincp.setting.process')->update([
            'value' => [
                'site_is_offline' => 0
            ]
        ]);
        \Phpfox_Cache::instance()->remove();

        return true;
    }
}