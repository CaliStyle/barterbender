<?php
/**
 * User: huydnt
 * Date: 05/01/2017
 * Time: 08:50
 */

namespace Apps\yn_backuprestore\Service;


use DateTime;
use mysqli;
use Phpfox;
use Phpfox_Service;

class Schedule extends Phpfox_Service
{
    protected $sBackuprestorePath;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT("ynbackuprestore_schedules");
        $this->sBackuprestorePath = PHPFOX_DIR_FILE . 'yn_backuprestore' . PHPFOX_DS;
    }

    /**
     * Add new schedule
     * @param $aVal
     * @param $aPlugins
     * @param $aThemes
     * @param $aUploads
     * @param $aDatabase
     * @return int
     */
    public function addSchedule($aVal, $aPlugins, $aThemes, $aUploads, $aDatabase)
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
        // add to shedule table
        $schedule_id = $this->database()->insert($this->_sTable, [
            '`title`'              => $aVal['schedule_name'],
            '`interval`'           => $aVal['interval'],
            '`start_date`'         => $aVal['datetime'],
            '`plugins_included`'   => json_encode($aPlugins),
            '`themes_included`'    => json_encode($aThemes),
            '`uploads_included`'   => json_encode($aUploads),
            '`database_included`'  => json_encode($aDatabase),
            '`maintenance_mode`'   => $aVal['maintenance_mode'],
            '`archive_format`'     => $aVal['archive_format'],
            '`prefix`'             => $aVal['prefix'],
            '`creation_timestamp`' => time()
        ]);

        // add to map table
        if ($schedule_id) {
            foreach ($aVal['destination_ids'] as $destination_id) {
                $result = $this->database()->insert(":ynbackuprestore_destination_maps", [
                    'parent_id'      => $schedule_id,
                    'parent_type'    => 'schedule',
                    'destination_id' => ($destination_id == 'download') ? 0 : $destination_id
                ]);
                if (!$result) {
                    break;
                }
            }
        }

        return $schedule_id;
    }

    /**
     * Update schedule
     * @param $iId
     * @param $aVal
     * @param $aPlugins
     * @param $aThemes
     * @param $aUploads
     * @param $aDatabase
     * @return int
     */
    public function updateSchedule($iId, $aVal, $aPlugins, $aThemes, $aUploads, $aDatabase)
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

        // add to shedule table
        $this->database()->update($this->_sTable, [
            '`title`'              => $aVal['schedule_name'],
            '`interval`'           => $aVal['interval'],
            '`start_date`'         => $aVal['datetime'],
            '`plugins_included`'   => json_encode($aPlugins),
            '`themes_included`'    => json_encode($aThemes),
            '`uploads_included`'   => json_encode($aUploads),
            '`database_included`'  => json_encode($aDatabase),
            '`maintenance_mode`'   => $aVal['maintenance_mode'],
            '`archive_format`'     => $aVal['archive_format'],
            '`prefix`'             => $aVal['prefix'],
            '`creation_timestamp`' => time()
        ], "schedule_id = $iId");

        // delete all old destinaion map
        $this->database()->delete(":ynbackuprestore_destination_maps", "parent_id=$iId AND parent_type='schedule'");

        // add to map table
        foreach ($aVal['destination_ids'] as $destination_id) {
            $result = $this->database()->insert(":ynbackuprestore_destination_maps", [
                'parent_id'      => $iId,
                'parent_type'    => 'schedule',
                'destination_id' => ($destination_id == 'download') ? 0 : $destination_id
            ]);
            if (!$result) {
                break;
            }
        }

        return $iId;
    }

    /**
     * Get schedules
     * @param array $aVal
     * @param int $iPage
     * @param null $iLimit
     * @return array
     */
    public function getSchedules($aVal = array(), $iPage = 1, $iLimit = null)
    {
        $select = $this->database()->select('*')->from($this->_sTable);
        $where = array();
        if (isset($aVal['schedule_id']) && $aVal['schedule_id']) {
            $cond = "schedule_id=$aVal[schedule_id]";
            return $select->where($cond)->executeRow();
        }
        if (isset($aVal['title']) && $aVal['title']) {
            $where[] = "title LIKE '%$aVal[title]%'";
        }
        if (isset($aVal['from_date']) && $aVal['from_date']) {
            $where[] = "start_date >= '$aVal[from_date]'";
        }
        if (isset($aVal['to_date']) && $aVal['to_date']) {
            $where[] = "start_date <= '$aVal[to_date]'";
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
        $select->order("start_date DESC");

        return $select->executeRows();
    }

    /**
     * Get quantity of schedules
     * @param $aVal
     * @return int|string
     */
    public function getQuantity($aVal)
    {
        $select = $this->database()->select('count(*)')->from($this->_sTable);
        $where = array();
        if (isset($aVal['title']) && $aVal['title']) {
            $where[] = "title = '$aVal[title]'";
        }
        if (isset($aVal['from_date']) && $aVal['from_date']) {
            $iFromTimestamp = (new DateTime($aVal['from_date']))->getTimestamp();
            $where[] = "creation_timestamp >= $iFromTimestamp";
        }
        if (isset($aVal['to_date']) && $aVal['to_date']) {
            $iToTimestamp = (new DateTime($aVal['to_date']))->getTimestamp();
            $where[] = "creation_timestamp <= $iToTimestamp";
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

        return $select->executeField();
    }

    /**
     * Delete multiple schedules
     * @param $aIds
     * @return bool
     */
    public function deleteSchedules($aIds)
    {
        $result = true;
        foreach ($aIds as $id) {
            if (!$this->delete($id)) {
                $result = false;
                break;
            }
        }
        return $result;
    }

    /**
     * Delete schedule
     * @param $iId
     * @return bool
     */
    public function delete($iId)
    {
        // delete all map
        if ($this->database()->delete(":ynbackuprestore_destination_maps", "parent_id=$iId AND parent_type='schedule'")) {
            return $this->database()->delete($this->_sTable, "schedule_id = $iId");
        }
        return false;
    }

    /**
     * Turn site online/offline
     * @param bool $bOffline
     */
    public function turnSiteOffline($bOffline = true)
    {
        Phpfox::getService('admincp.setting.process')->update([
            'value' => [
                'site_is_offline' => ($bOffline) ? 1 : 0
            ]
        ]);
        \Phpfox_Cache::instance()->remove();
    }

    /**
     * Process schedule folder
     * @param $iId
     * @return bool
     */
    public function processBackup($iId)
    {
        $aSchedule = $this->getSchedules(['schedule_id' => $iId]);
        if (!$aSchedule) {
            return false;
        }

        $this->mkdir($this->sBackuprestorePath);

        // create folder structure
        $sContainerPath = $this->sBackuprestorePath . $aSchedule['title'] . PHPFOX_DS;
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
        if (isset($aSchedule['plugins_included']) && $aSchedule['plugins_included']) {
            $aPlugins = json_decode($aSchedule['plugins_included'], true);
            foreach ($aPlugins as $sPlugin) {
                // copy plugin to container path
                if (strpos($sPlugin, 'app') !== false) {
                    $sPlugin = substr($sPlugin, 4);
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
        if (isset($aSchedule['themes_included']) && $aSchedule['themes_included']) {
            $aThemes = json_decode($aSchedule['themes_included'], true);
            foreach ($aThemes as $sTheme) {
                // copy theme to container path
                $sCommand = "cp -a " . PHPFOX_DIR_THEME . $sTheme . " $sThemesContainerPath";
                exec($sCommand);
            }
        }

        // backup uploads
        if (isset($aSchedule['uploads_included']) && $aSchedule['uploads_included']) {
            $aUploads = json_decode($aSchedule['uploads_included'], true);
            foreach ($aUploads as $sFolder) {
                // copy upload folder to container path
                $sCommand = "cp -a " . PHPFOX_DIR_FILE . $sFolder . " $sUploadsContainerPath";
                exec($sCommand);
            }
        }

        // backup database
        if (isset($aSchedule['database_included']) && $aSchedule['database_included']) {
            $aTables = json_decode($aSchedule['database_included'], true);
            include PHPFOX_DIR_SETTINGS . 'server.sett.php';
            $this->exportDatabase($_CONF['db']['host'], $_CONF['db']['user'], $_CONF['db']['pass'],
                $_CONF['db']['port'], $_CONF['db']['name'], $aTables, $_CONF['db']['prefix'], $sContainerPath . 'database.sql');
        }

        return true;
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
     * Update start date after cron run backup
     * @param $iId
     * @return bool|resource
     */
    public function updateStartDate($iId)
    {
        $aSchedule = $this->getSchedules(['schedule_id' => $iId]);
        $sInterval = $aSchedule['interval'];
        $oldDate = new DateTime($aSchedule['start_date']);
        switch ($sInterval) {
            case '4hours':
                $addTimeStamp = 4 * 60 * 60;
                break;
            case '8hours':
                $addTimeStamp = 8 * 60 * 60;
                break;
            case '12hours':
                $addTimeStamp = 12 * 60 * 60;
                break;
            case 'daily':
                $addTimeStamp = 24 * 60 * 60;
                break;
            case 'weekly':
                $addTimeStamp = 7 * 24 * 60 * 60;
                break;
            case 'fortnightly':
                $addTimeStamp = 15 * 24 * 60 * 60;
                break;
            case 'monthly':
                $addTimeStamp = 30 * 24 * 60 * 60;
                break;
            default:
                $addTimeStamp = 24 * 60 * 60; // default daily
                break;
        }
        $newDate = $oldDate->getTimestamp() + $addTimeStamp;
        return $this->database()->update($this->_sTable, [
            'start_date' => date('Y-m-d H:i:s', $newDate)
        ], "schedule_id = $iId");
    }

    public function getNextSchedule()
    {
        return $this->database()->select('start_date')->from($this->_sTable)
            ->order('start_date ASC')->where("start_date > '" . date('Y-m-d H:i:s') ."'")
            ->executeField();
    }

    public function checkAvailableSchedule($schedule)
    {
        return PHPFOX_TIME >= strtotime($schedule['start_date']);
    }
}