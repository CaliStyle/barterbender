<?php
/**
 * User: huydnt
 * Date: 05/01/2017
 * Time: 08:50
 */

namespace Apps\yn_backuprestore\Service;


use Apps\yn_backuprestore\Adapter;
use Apps\yn_backuprestore\Adapter\Googledrive;
use Apps\yn_backuprestore\Adapter\Onedrive;
use DateTime;
use Google_Service_Exception;
use mysqli;
use Phpfox;
use Phpfox_Service;

class Backup extends Phpfox_Service
{
    protected $_ynbackuprestoreDir;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynbackuprestore_backups');
        $this->_ynbackuprestoreDir = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS;
    }

    /**
     * Get all Module except admincp
     * @return array
     */
    public function getModules()
    {
        $modules = $this->database()->select('`module_id` as id')->from(":module")
            ->where("`module_id` != 'admincp' AND phrase_var_name!='module_apps'")
            ->order("`module_id` ASC")
            ->executeRows();
        foreach ($modules as &$module) {
            $module['name'] = ucfirst($module['id']);
        }

        return $modules;
    }

    /**
     * Get all Apps
     * @return array
     */
    public function getApps()
    {
        return $this->database()->select('apps_id as id, apps_name as name')->from(":apps")
            ->where("apps_id != 'yn_backuprestore'")->order("`apps_name` ASC")->executeRows();
    }

    /**
     * Get all theme, exclude default
     * @return array
     */
    public function getThemes()
    {
        $themes = [];
        foreach (flavor()->all() as $flavor) {
            $themes[] = [
                'folder' => $flavor->id,
                'name' => $flavor->name
            ];
        }
        return $themes;
    }

    /**
     * Get upload folders, exclude some folders
     * @return array
     */
    public function getUploadFolders()
    {
        return array_diff(scandir(PHPFOX_DIR_FILE), [
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
        ]);
    }

    /**
     * Get database tables
     * @return array
     */
    public function getDatabaseTables()
    {
        return $this->database()->getTableStatus();
    }

    /**
     * Add new backup
     * @param $aVal
     * @param $aPlugins
     * @param $aThemes
     * @param $aUploads
     * @param $aDatabase
     * @param $sType
     * @return int
     */
    public function addBackup($aVal, $aPlugins, $aThemes, $aUploads, $aDatabase, $sType)
    {
        if (isset($aPlugins[0]) && $aPlugins[0] == 'all') {
            unset($aPlugins[0]);
        }
        if (isset($aThemes[0]) && $aThemes[0] == 'all') {
            unset($aThemes[0]);
        }
        if (isset($aUploads[0]) && $aUploads[0] == 'all') {
            unset($aUploads[0]);
        }
        if (isset($aDatabase[0]) && $aDatabase[0] == 'all') {
            unset($aDatabase[0]);
        }
        // add to backup table
        $backup_id = $this->database()->insert($this->_sTable, [
            'title'              => $aVal['prefix'] . "_" . date("Y-m-d_H-i-s"),
            'type'               => $sType,
            'plugins_included'   => json_encode($aPlugins),
            'themes_included'    => json_encode($aThemes),
            'uploads_included'   => json_encode($aUploads),
            'database_included'  => json_encode($aDatabase),
            'maintenance_mode'   => $aVal['maintenance_mode'],
            'archive_format'     => $aVal['archive_format'],
            'prefix'             => $aVal['prefix'],
            'creation_timestamp' => time()
        ]);

        // add to map table
        if ($backup_id) {
            foreach ($aVal['destination_ids'] as $destination_id) {
                $result = $this->database()->insert(":ynbackuprestore_destination_maps", [
                    'parent_id'      => $backup_id,
                    'parent_type'    => 'backup',
                    'destination_id' => ($destination_id == 'download') ? 0 : $destination_id
                ]);
                if (!$result) {
                    break;
                }
            }
        }

        return $backup_id;
    }

    /**
     * Process backup folder
     * @param $iId
     * @return bool
     */
    public function processBackup($iId)
    {
        $aBackup = $this->getBackup($iId);
        if (!$aBackup) {
            return false;
        }

        $sBackuprestorePath = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS;
        $this->mkdir($sBackuprestorePath);

        // create folder structure
        $sContainerPath = $sBackuprestorePath . $aBackup['title'] . PHPFOX_DS;
        $sPluginsContainerPath = $sContainerPath . 'plugins' . PHPFOX_DS;
        $sAppsContainerPath = $sPluginsContainerPath . 'Apps' . PHPFOX_DS;
        $sModuleContainerPath = $sPluginsContainerPath . 'module' . PHPFOX_DS;
        $sThemesContainerPath = $sContainerPath . 'themes' . PHPFOX_DS;
        $sUploadsContainerPath = $sContainerPath . 'uploads' . PHPFOX_DS;
        $this->mkdir($sContainerPath);
        $this->mkdir($sPluginsContainerPath);
        $this->mkdir($sAppsContainerPath);
        $this->mkdir($sModuleContainerPath);
        $this->mkdir($sThemesContainerPath);
        $this->mkdir($sUploadsContainerPath);

        // backup plugins
        if (isset($aBackup['plugins_included']) && $aBackup['plugins_included']) {
            $aPlugins = json_decode($aBackup['plugins_included'], true);
            foreach ($aPlugins as $sPlugin) {
                // copy plugin to container path
                if (strpos($sPlugin, 'app') !== false) {
                    $sPlugin = substr($sPlugin, 4);
                    if (version_compare(Phpfox::getVersion(), '4.5.0')) {
                        $app = $this->database()->select('*')->from(Phpfox::getT('apps'))->where(['apps_id' => $sPlugin])->executeRow();
                        if (!empty($app) && isset($app['apps_dir'])) {
                            $sPlugin = $app['apps_dir'];
                        }
                    }
                    $sCommand = "cp -a " . PHPFOX_DIR_SITE_APPS . $sPlugin . " $sAppsContainerPath";
                    exec($sCommand);
                } else {
                    $sPlugin = substr($sPlugin, 7);
                    $sCommand = "cp -a " . PHPFOX_DIR_MODULE . $sPlugin . " $sModuleContainerPath";
                    exec($sCommand);
                }
            }
        }

        // backup themes
        if (isset($aBackup['themes_included']) && $aBackup['themes_included']) {
            $aThemes = json_decode($aBackup['themes_included'], true);
            foreach ($aThemes as $sTheme) {
                // copy theme to container path
                $sCommand = "cp -a " . ( $sTheme == 'bootstrap' ? PHPFOX_DIR_THEME . $sTheme : PHPFOX_DIR_SITE . 'flavors' . PHPFOX_DS . $sTheme) . " $sThemesContainerPath";
                exec($sCommand);
            }
        }

        // backup uploads
        if (isset($aBackup['uploads_included']) && $aBackup['uploads_included']) {
            $aUploads = json_decode($aBackup['uploads_included'], true);
            foreach ($aUploads as $sFolder) {
                // copy upload folder to container path
                $sCommand = "cp -a " . PHPFOX_DIR_FILE . $sFolder . " $sUploadsContainerPath";
                exec($sCommand);
            }
        }

        // backup database
        if (isset($aBackup['database_included']) && $aBackup['database_included']) {
            $aTables = json_decode($aBackup['database_included'], true);
            include PHPFOX_DIR_SETTINGS . 'server.sett.php';
            $this->exportDatabase($_CONF['db']['host'], $_CONF['db']['user'], $_CONF['db']['pass'],
                $_CONF['db']['port'], $_CONF['db']['name'], $aTables, $_CONF['db']['prefix'], $sContainerPath . 'database.sql');
        }

        return true;
    }

    /**
     * Get backup by Id
     * @param $iId
     * @return array
     */
    public function getBackup($iId)
    {
        $aBackup = $this->database()->select('*')->from($this->_sTable)->where("backup_id = $iId")->executeRow();

        return $aBackup;
    }

    /**
     * Create dir if not exist
     * @param $sPath
     */
    protected function mkdir($sPath)
    {
        if (!is_dir($sPath)) {
            mkdir($sPath);
        }
    }

    /**
     * Export Database
     * @param $host
     * @param $user
     * @param $pass
     * @param $name
     * @param bool|array $tables
     * @return string
     */
    protected function exportDatabase($host, $user, $pass, $port, $name, $tables = false, $tbPrefix, $file_name)
    {
        $mysqli = new mysqli($host, $user, $pass, $name, Phpfox::getParam(array('db', 'port')));
        $mysqli->select_db($name);
        $mysqli->query("SET NAMES 'utf8'");

        // Table will not be imported
        $not_imported = [
            $tbPrefix. 'log_session',
            $tbPrefix. 'ynbackuprestore_backups',
            $tbPrefix. 'ynbackuprestore_destinations',
            $tbPrefix. 'ynbackuprestore_destination_maps',
            $tbPrefix. 'ynbackuprestore_destination_types',
            $tbPrefix. 'ynbackuprestore_schedules',
        ];

        $queryTables = $mysqli->query('SHOW TABLES');

        // Prevent when lock database
        if (empty($queryTables)) {
            return false;
        }
        while ($row = $queryTables->fetch_row()) {
            if (!in_array($row[0], $not_imported)) {
                $target_tables[] = $row[0];
            }
        }
        if (!isset($target_tables)) {
            return false;
        }
        if ($tables !== false && is_array($tables)) {
            $target_tables = array_intersect($target_tables, $tables);
        }
        // Port
        $mysql_port = !empty($port) ? ' -P'.$port : '';
        $list_tables = !empty($target_tables) ? implode(' ', $target_tables) : '';

        return exec("mysqldump -h$host -u$user -p$pass" . $mysql_port . " $name $list_tables > $file_name");
    }

    /**
     * Get last backup
     * @return array
     */
    public function getLastBackup()
    {
        $aBackup = $this->database()->select('*')->from($this->_sTable)->order('backup_id DESC')->executeRow();

        return $aBackup;
    }

    /**
     * Get all backups
     * @return array
     */
    public function getAllBackups()
    {
        $aBackups = $this->database()->select('*')->from($this->_sTable)->order('backup_id DESC')->executeRows();

        return $aBackups;
    }

    /**
     * Get quantity of backups
     * @param array $aVal
     * @return array
     */
    public function getQuantity($aVal = array())
    {
        // delete unsuccessful backups
        $this->database()->delete($this->_sTable, "size IS NULL");
        $select = $this->database()->select('count(*)')->from($this->_sTable);
        $where = array();
        if (isset($aVal['from_date']) && $aVal['from_date']) {
            list($day, $month, $year) = explode('-', $aVal['from_date']);
            $iFromTimestamp = mktime(0, 0, 0, $day, $month, $year);
            $where[] = "creation_timestamp >= $iFromTimestamp";
        }
        if (isset($aVal['to_date']) && $aVal['to_date']) {
            list($day, $month, $year) = explode('-', $aVal['to_date']);
            $iToTimestamp = mktime(23, 59, 59, $day, $month, $year);
            $where[] = "creation_timestamp <= $iToTimestamp";
        }
        if (isset($aVal['backup_type']) && $aVal['backup_type'] && $aVal['backup_type'] != 'all') {
            $where[] = "type = '$aVal[backup_type]'";
        }
        if (isset($aVal['plugins']) && $aVal['plugins']) {
            $where[] = "plugins_included != '[]'";
        }
        if (isset($aVal['themes']) && $aVal['themes']) {
            $where[] = "themes_included != '[]'";
        }
        if (isset($aVal['uploads']) && $aVal['uploads']) {
            $where[] = "uploads_included != '[]'";
        }
        if (isset($aVal['database']) && $aVal['database']) {
            $where[] = "database_included != '[]'";
        }
        if (count($where)) {
            $select->where(implode(' AND ', $where));
        }
        return $select->execute('getfield');
    }

    /**
     * Get backups
     * @param array $aVal
     * @param int $iPage
     * @param null $iLimit
     * @return array
     */
    public function getBackups($aVal = array(), $iPage = 1, $iLimit = null)
    {
        // delete unsuccessful backups
        $this->database()->delete($this->_sTable, "size IS NULL");
        $select = $this->database()->select('*')->from($this->_sTable)->order('backup_id DESC');
        $where = array();
        if (isset($aVal['from_date']) && $aVal['from_date']) {
            $iFromTimestamp = (new DateTime($aVal['from_date']))->getTimestamp();
            $where[] = "creation_timestamp >= $iFromTimestamp";
        }
        if (isset($aVal['to_date']) && $aVal['to_date']) {
            $iToTimestamp = (new DateTime($aVal['to_date'] . ' 23:59:59'))->getTimestamp();
            $where[] = "creation_timestamp <= $iToTimestamp";
        }
        if (isset($aVal['backup_type']) && $aVal['backup_type'] && $aVal['backup_type'] != 'all') {
            $where[] = "type = '$aVal[backup_type]'";
        }
        if (isset($aVal['plugins']) && $aVal['plugins']) {
            $where[] = "plugins_included != '[]'";
        }
        if (isset($aVal['themes']) && $aVal['themes']) {
            $where[] = "themes_included != '[]'";
        }
        if (isset($aVal['uploads']) && $aVal['uploads']) {
            $where[] = "uploads_included != '[]'";
        }
        if (isset($aVal['database']) && $aVal['database']) {
            $where[] = "database_included != '[]'";
        }
        if (count($where)) {
            $select->where(implode(' AND ', $where));
        }
        if ($iLimit) {
            $select->limit($iPage, $iLimit);
        }
        $backups = $select->executeRows();
        foreach ($backups as &$backup) {
            $backup['destinations'] = implode(", ", $this->getDestinationByBackupId($backup['backup_id']));
        }

        return $backups;
    }

    /**
     * Get destination by backup Id
     * @param $iId
     * @return array
     */
    private function getDestinationByBackupId($iId)
    {
        $destinations = $this->database()->select('dest.title, dest.destination_id')->from(':ynbackuprestore_destination_maps',
            'map')
            ->leftJoin(':ynbackuprestore_destinations', 'dest', 'dest.destination_id = map.destination_id')
            ->where("map.parent_id = $iId AND map.parent_type = 'backup'")->executeRows();
        $return_destinations = array();
        foreach ($destinations as &$destination) {
            if ($destination['title']) {
                $return_destinations[] = $destination['title'];
            }
        }
        if (!count($return_destinations)) {
            $downloadDest = $this->database()
                ->select('COUNT(*)')
                ->from(':ynbackuprestore_destination_maps', 'map')
                ->where("map.parent_id = $iId AND map.parent_type = 'backup' AND map.destination_id = 0")
                ->executeField();
            if ($downloadDest) {
                $return_destinations[] = _p('Download to computer');
            }
        }
        if (!count($return_destinations)) {
            $return_destinations[] = _p('ynbackuprestore_no_destination_found');
        }

        return $return_destinations;
    }

    /**
     * Get all available compress methods support
     * @return array
     */
    protected function getAvailableCompressMethods()
    {
        $aCompressMethods = [
            'zip',
            'tar',
            'tar.gz',
            'tar.bz2'
        ];

        $methods = array();

        foreach (array_keys($aCompressMethods) as $method) {
            if ($method == 'tar' || $method == 'tar.gz' || $method == 'tar.bz2') {
                exec("tar -version", $output, $result);
                if (!$result) {
                    $methods[] = $method;
                } else {
                    exec("tar --version", $output, $result);
                    if (!$result) {
                        $methods[] = $method;
                    }
                }
            } else {
                exec("$method -version", $output, $result);
                if (!$result) {
                    $methods[] = $method;
                } else {
                    exec("$method --version", $output, $result);
                    if (!$result) {
                        $methods[] = $method;
                    }
                }
            }
        }

        return $methods;
    }

    /**
     * Tranfer backup to destinations
     * @param $iId
     * @return bool|string
     */
    public function transferProcess($iId)
    {
        $aBackup = $this->getBackup($iId);
        if (!$aBackup) {
            return false;
        }
        $result = true;
        $aDestinations = $this->getDestinations($iId);

        $sFilePath = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['backup_id'] . PHPFOX_DS . $aBackup['title'] . "." . $aBackup['archive_format'];
        foreach ($aDestinations as $aDestination) {
            $aParams = json_decode($aDestination['params'], true);
            switch ($aDestination['type_id']) {
                case 2:
                    // Email type
                    $oEmail = new Adapter\Email();
                    $oEmail->upload(explode(',', $aParams['email_address']), $sFilePath, $iId);
                    break;
                case 3:
                    // FTP type
                    $oFtp = new Adapter\FtpServer();
                    if ($oFtp->connect($aParams['ftp_server'], $aParams['ftp_login'],
                        $aParams['ftp_password'], $aParams['ftp_mode'])
                    ) {
                        $oFtp->upload($sFilePath, $aParams['ftp_remote']);
                        $oFtp->close();
                    }
                    break;
                case 4:
                    // SFTP type
                    $oSftp = new Adapter\SftpServer();
                    if ($oSftp->connect($aParams['sftp_host'], $aParams['sftp_port'], $aParams['sftp_username'],
                        $aParams['sftp_password'], isset($aParams['sftp_scp']) ? true : false)
                    ) {
                        $oSftp->upload($aParams['sftp_directory'], $sFilePath);
                    }
                    break;
                case 5:
                    // MySQL
                    $oMysql = new Adapter\Mysql();
                    $sPort = isset($aParams['mysql_port']) ? $aParams['mysql_port'] : '';
                    if ($oMysql->connect($aParams['mysql_host'], $aParams['mysql_username'], $aParams['mysql_password'],
                        $aParams['mysql_dbname'], $sPort)
                    ) {
                        $oMysql->upload(PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['backup_id'] . PHPFOX_DS . 'database.sql');
                    }
                    break;
                case 6:
                    // Amazon S3
                    $oAmazon = new Adapter\Amazon($aParams['s3_access'], $aParams['s3_secret']);
                    $oAmazon->upload($aParams['s3_bucket'], $sFilePath);
                    break;
                case 7:
                    // Dropbox
                    $oDropb = new Adapter\Dropb($aParams['dropbox_key'], $aParams['dropbox_secret'],
                        $aParams['dropbox_token']);
                    $oDropb->upload($aParams['dropbox_store'], $sFilePath);
                    break;
                case 8:
                    // One Drive
                    if (!isset($_SESSION)) {
                        session_start();
                    }
                    $oState = json_decode($aDestination['params'])->access_token;
                    $oOneDrive = new Adapter\Onedrive($aParams['onedrive_id'], $oState);
                    try {
                        $oOneDrive->upload($aParams['onedrive_secret'], $aParams['onedrive_directory'], $sFilePath);
                    } catch (\Exception $e) {
                        \Phpfox::getLog('main.log')->log('backup_restore_onedrive_uploadfile', $e->getMessage());
                    }
                    break;
                case 9:
                    // Google Drive
                    $oGoogle = new Adapter\Googledrive($aParams['google_id'], $aParams['google_secret'], null,
                        $aParams['access_token']);
                    /**
                     * Check access token:
                     *      Case Token expired: If have refresh token -> refresh token -> update to database -> upload
                     */
                    if ($oGoogle->isAccessTokenExpired()) {
                        if (isset($aParams['access_token']['refresh_token'])) {
                            $aAccessToken = $oGoogle->refreshToken();
                            if ($aAccessToken === false) {
                                break;
                            }
                            // update access token
                            $aParams['access_token'] = $aAccessToken;
                            $oDestinationService = Phpfox::getService('ynbackuprestore.destination');
                            $oDestinationService->updateParams($iId, $aParams);
                            $oGoogle->setAccessToken($aAccessToken);
                        }
                    }
                    try {
                        $oGoogle->upload($aParams['google_folder'], $sFilePath);
                    } catch (Google_Service_Exception $e) {

                    }
                    break;
                default:
                    // download to computer
                    $result = 'download';
                    break;
            }
        }

        if ($aBackup['maintenance_mode']) {
            // site go online
            Phpfox::getService('admincp.setting.process')->update([
                'value' => [
                    'site_is_offline' => 0
                ]
            ]);
            \Phpfox_Cache::instance()->remove();
        }

        return $result;
    }

    /**
     * Finish backup
     * @param $iId
     * @return bool
     */
    public function finishBackup($iId)
    {
        $aBackup = $this->getBackup($iId);
        if (!$aBackup) {
            return false;
        }
        $sFolderToCompress = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['title'] . PHPFOX_DS;
        $this->mkdir(PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['backup_id'] . PHPFOX_DS);
        $sFilePath = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['backup_id'] . PHPFOX_DS . $aBackup['title'] . "." . $aBackup['archive_format'];

        // Coppy SQL file
        $sql_file = $sFolderToCompress . 'database.sql';
        if (file_exists($sql_file)) {
            @copy($sql_file, PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['backup_id'] . PHPFOX_DS . 'database.sql');
        }

        // compress backup file
        $aVailableMethods = $this->getAvailableCompressMethods();
        if (in_array($aBackup['archive_format'], $aVailableMethods)) {
            switch ($aBackup['archive_format']) {
                case 'zip':
                    $compress_command = "zip -r $sFilePath *";
                    break;
                case 'tar':
                    $compress_command = "tar -cvf $sFilePath *";
                    break;
                case 'tar.gz':
                    $compress_command = "tar -zcvf $sFilePath *";
                    break;
                case 'tar.bz2':
                    $compress_command = "tar -jcvf $sFilePath *";
                    break;
                default:
                    $compress_command = "zip -r $sFilePath *";
                    break;
            }
            chdir($sFolderToCompress);
            exec($compress_command);
        }

        // save log
        $this->saveLog($iId);

        // clean temporary files
        exec("chmod -R 777 $sFolderToCompress");
        exec("rm -rf $sFolderToCompress");

        // update file size
        $fSize = round(filesize($sFilePath) / 1000 / 1000, 2);
        $this->database()->update($this->_sTable, [
            'size' => ($fSize) ? $fSize : 0.01
        ], [
            'backup_id' => $iId
        ]);

        return true;
    }

    /**
     * Get destinations of an backup
     * @param $iId
     * @param array $aCond
     * @return array
     */
    public function getDestinations($iId, $aCond = array())
    {
        $where = [
            'maps.parent_id'   => $iId,
            'maps.parent_type' => 'backup'
        ];
        if (isset($aCond['type_id']) && $aCond['type_id']) {
            $where['dests.type_id'] = $aCond['type_id'];
        }
        return $this->database()->select('*')
            ->from(":ynbackuprestore_destination_maps", "maps")
            ->leftJoin(":ynbackuprestore_destinations", "dests", "dests.destination_id = maps.destination_id")
            ->where($where)->executeRows();
    }

    /**
     * Save log of backup
     * @param $iId
     * @return bool
     */
    protected function saveLog($iId)
    {
        $aBackup = $this->getBackup($iId);
        if (!$aBackup) {
            return false;
        }
        $aPlugins = json_decode($aBackup['plugins_included'], true);
        $aThemes = json_decode($aBackup['themes_included'], true);
        $aUploads = json_decode($aBackup['uploads_included'], true);
        $aDatabase = json_decode($aBackup['database_included'], true);
        $sLogPath = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS . $aBackup['backup_id'] . PHPFOX_DS . 'logfile.txt';
        $logFile = fopen($sLogPath, 'w');
        $sLog = _p('Backup in ') . date('Y-m-d H:i:s', $aBackup['creation_timestamp']) . PHP_EOL;
        $sLog .= _p("Included:") . PHP_EOL;
        if ($iNumPlugins = count($aPlugins)) {
            $sLog .= _p("- Plugins ({number})", ['number' => $iNumPlugins]) . PHP_EOL;
            foreach ($aPlugins as $aPlugin) {
                $sLog .= _p("\t+ $aPlugin") . PHP_EOL;
            }
        }
        if ($iNumThemes = count($aThemes)) {
            $sLog .= _p("- Themes ({number})", ['number' => $iNumThemes]) . PHP_EOL;
            foreach ($aThemes as $aTheme) {
                $sLog .= _p("\t+ $aTheme") . PHP_EOL;
            }
        }
        if ($iNumUpload = count($aUploads)) {
            $sLog .= _p("- Upload folders ({number})", ['number' => $iNumUpload]) . PHP_EOL;
            foreach ($aUploads as $aUpload) {
                $sLog .= _p("\t+ $aUpload") . PHP_EOL;
            }
        }
        if ($iNumDatabase = count($aDatabase)) {
            $sLog .= _p("- Database tables ({number})", ['number' => $iNumDatabase]) . PHP_EOL;
            foreach ($aDatabase as $sTable) {
                $sLog .= _p("\t+ $sTable") . PHP_EOL;
            }
        }
        fwrite($logFile, $sLog);
        return true;
    }

    /**
     * Cron job: delete backup older than days in setting
     */
    public function cronDeleteBackups()
    {
        if ($autoRemoveDays = setting('ynbackuprestore_auto_remove', 0)) {
            $aBackups = $this->getAllBackups();
            foreach ($aBackups as $aBackup) {
                if (($aBackup['creation_timestamp'] + $autoRemoveDays * 24 * 60 * 60) < time()) {
                    @$this->deleteBackup($aBackup['backup_id']);
                }
            }
        }
    }

    /**
     * Delete backup by id
     * @param $iId
     * @return bool
     */
    public function deleteBackup($iId)
    {
        /**
         * Delete files on server
         */
        $backupDir = $this->_ynbackuprestoreDir . $iId . PHPFOX_DS;
        exec("rm -rf $backupDir");
        /**
         * Delete row in database
         */
        return $this->database()->delete($this->_sTable, "backup_id = $iId");
    }

    /**
     * Renew token
     * @param $iId
     */
    public function renewToken($iId)
    {
        $oBackupService = Phpfox::getService('ynbackuprestore.backup');
        $oDestinationService = Phpfox::getService('ynbackuprestore.destination');
        $aOneDriveDest = $oBackupService->getDestinations($iId, [
            'type_id' => 8
        ]);
        $aGoogleDest = $oBackupService->getDestinations($iId, [
            'type_id' => 9
        ]);

        foreach ($aGoogleDest as $aItem) {
            $aParams = json_decode($aItem['params'], true);
            if (isset($aParams['access_token'])) {
                $oGoogle = new Googledrive($aParams['google_id'], $aParams['google_secret'],
                    \Phpfox_Url::instance()->makeUrl('admincp.ynbackuprestore.get-google-access-token'),
                    $aParams['access_token']);
                if ($oGoogle->isAccessTokenExpired()) {
                    if (isset($aParams['access_token']['refresh_token'])) {
                        $aAccessToken = $oGoogle->refreshToken();
                        if ($aAccessToken !== false) {
                            // update access token
                            $aParams['access_token'] = $aAccessToken;
                            $oDestinationService->updateParams($aItem['destination_id'], $aParams);
                        }
                    }
                }
            }
        }

        foreach ($aOneDriveDest as $aItem) {
            $aParams = json_decode($aItem['params'], true);
            if (isset($aParams['access_token'])) {
                $oState = json_decode($aItem['params'])->access_token;
                $oOneDrive = new Onedrive($aParams['onedrive_id'], $oState);
                if (isset($oState->token->data->refresh_token)) {
                    $oOneDrive->renewAccessToken($aParams['onedrive_secret']);
                    $access_token = $oOneDrive->getState();
                    if (!isset($access_token->token->data->error)) {
                        $aParams['access_token'] = $access_token;
                        $oDestinationService->updateParams($aItem['destination_id'], $aParams);
                    }
                }
            }
        }
    }

    /**
     * Add download to computer destination
     * @param $iBackupId
     * @return int
     */
    public function addDownloadDestination($iBackupId) {
        return $this->database()->insert(":ynbackuprestore_destination_maps", array(
            'parent_id'      => $iBackupId,
            'parent_type'    => 'backup',
            'destination_id' => 0
        ));
    }
}