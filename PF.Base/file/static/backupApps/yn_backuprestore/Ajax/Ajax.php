<?php
/**
 * User: huydnt
 * Date: 05/01/2017
 * Time: 10:50
 */

namespace Apps\yn_backuprestore\Ajax;


use Apps\yn_backuprestore\Adapter\Amazon;
use Phpfox;

class Ajax extends \Phpfox_Ajax
{
    /**
     * Ajax call delete destination, reload on success
     */
    public function deleteDestination()
    {
        $iId = $this->get('id');
        if (Phpfox::getService('ynbackuprestore.destination')->deleteDestination($iId)) {
            $this->call('location.reload()');
        }
    }

    /**
     * Delete multiple Destinations
     */
    public function deleteSelected()
    {
        $aIds = $this->get('destination_row');
        if (Phpfox::getService('ynbackuprestore.destination')->deleteDestinations($aIds)) {
            $this->call('location.reload()');
        }
    }

    /**
     * Delete multiple schedules
     */
    public function deleteSelectedSchedules()
    {
        $aIds = $this->get('schedule_row');
        if (Phpfox::getService('ynbackuprestore.schedule')->deleteSchedules($aIds)) {
            $this->call('location.reload()');
        }
    }

    /**
     * Backup: initialize process
     */
    public function initializeProcess()
    {
        $iId = $this->get('id');
        $aBackup = Phpfox::getService('ynbackuprestore.backup')->getBackup($iId);
        if ($aBackup['maintenance_mode']) {
            // switch to maintenance mode
            Phpfox::getService('admincp.setting.process')->update([
                'value' => [
                    'site_is_offline' => 1
                ]
            ]);
            \Phpfox_Cache::instance()->remove();
        }
        $this->call('$Core.BackupRestore.backupProcess(' . $iId . ', "' . _p('Creating backup files...') . '");');
    }

    /**
     * Backup: process backup
     */
    public function backupProcess()
    {
        $iId = $this->get('id');
        $result = Phpfox::getService('ynbackuprestore.backup')->processBackup($iId);
        if ($result) {
            $this->call('$Core.BackupRestore.finishBackup(' . $iId . ', "' . _p('Creating backup files...') . '");');
        }
    }

    /**
     * Backup: finish backup
     */
    public function finishBackup()
    {
        $iId = $this->get('id');
        $result = Phpfox::getService('ynbackuprestore.backup')->finishBackup($iId);
        if ($result) {
            $this->call('$Core.BackupRestore.transferProcess(' . $iId . ', "' . _p('Backing up database and files...') . '");');
        }
    }

    /**
     * Backup: transfer backup file
     */
    public function transferProcess()
    {
        $iId = $this->get('id');
        $result = Phpfox::getService('ynbackuprestore.backup')->transferProcess($iId);
        if ($result) {
            $aBackup = Phpfox::getService('ynbackuprestore.backup')->getBackup($iId);
            $this->call('$Core.BackupRestore.doneBackup(' . $iId . ', "' . _p('Finished!') . '", ' . $aBackup['size'] . ', "' . $aBackup['title'] . '.' . $aBackup['archive_format'] . '");');
        }
        if ($result === 'download') {
            $this->call('location.href="' . \Phpfox_Url::instance()->makeUrl('admincp.ynbackuprestore.download') . $iId . '"');
        }
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule()
    {
        $iId = $this->get('id');
        if (Phpfox::getService('ynbackuprestore.schedule')->delete($iId)) {
            $this->call('location.reload()');
        }
    }

    /**
     * Initialize restore process
     */
    public function restoreInitializeProcess()
    {
        $fileName = $this->get('file');
        if (Phpfox::getService('ynbackuprestore.restore')->initializeProcess($fileName)) {
            $this->call('$Core.BackupRestore.validateFolderPermission("' . $fileName . '");');
        } else {
            $this->call('$Core.BackupRestore.wrongRestoreFile();');
        }
    }

    /**
     * Initialize restore process
     */
    public function validateFolderPermission()
    {
        $fileName = $this->get('file');
        $errorFolders = Phpfox::getService('ynbackuprestore.restore')->validateFolderPermission($fileName);
        if (empty($errorFolders)) {
            $this->call('$Core.BackupRestore.restorePlugins("' . $fileName . '", "' . _p('Restoring plugins...') . '");');
        } else {
            $this->template()->assign([
                'errorFolders' => $errorFolders
            ])->getTemplate('ynbackuprestore.block.folder-error');
            $this->replaceWith('#backup_message', $this->getContent(false));
            $this->call('$Core.BackupRestore.wrongRestoreFile()');
        }
    }

    /**
     * Restore Plugins
     */
    public function restorePlugins()
    {
        $fileName = $this->get('file');
        if (Phpfox::getService('ynbackuprestore.restore')->restorePlugins($fileName)) {
            $this->call('$Core.BackupRestore.restoreThemes("' . $fileName . '", "' . _p('Restoring themes...') . '");');
        } else {
            $this->call('$Core.BackupRestore.wrongRestoreFile()');
        }
    }

    /**
     * Restore Themes
     */
    public function restoreThemes()
    {
        $fileName = $this->get('file');
        if (Phpfox::getService('ynbackuprestore.restore')->restoreThemes($fileName)) {
            $this->call('$Core.BackupRestore.restoreUploads("' . $fileName . '", "' . _p('Restoring upload folders...') . '");');
        } else {
            $this->call('$Core.BackupRestore.wrongRestoreFile()');
        }
    }

    /**
     * Restore Upload folders
     */
    public function restoreUploads()
    {
        $fileName = $this->get('file');
        if (Phpfox::getService('ynbackuprestore.restore')->restoreUploads($fileName)) {
            $this->call('$Core.BackupRestore.restoreDatabase("' . $fileName . '", "' . _p('Restoring database...') . '");');
        } else {
            $this->call('$Core.BackupRestore.wrongRestoreFile()');
        }
    }

    /**
     * Restore Upload folders
     */
    public function restoreDatabase()
    {
        $fileName = $this->get('file');
        if (Phpfox::getService('ynbackuprestore.restore')->restoreDatabase($fileName)) {
            $this->call('$Core.BackupRestore.cleanup("' . $fileName . '", "' . _p('Clean up...') . '");');
        }
    }

    public function cleanup()
    {
        $fileName = $this->get('file');
        if (Phpfox::getService('ynbackuprestore.restore')->cleanup($fileName)) {
            $this->call('$Core.BackupRestore.finishRestore("' . $fileName . '", "' . _p('Finished!') . '");');
        }
    }

    public function getListBuckets()
    {
        $access = $this->get('access');
        $secret = $this->get('secret');
        $secret = str_replace(' ', '+', $secret);
        $amazon = new Amazon($access, $secret);
        $buckets = $amazon->listBuckets($access, $secret);
        if (is_array($buckets)) {
            // list buckets successfully
            if (!count($buckets)) {
                $this->call('$Core.BackupRestore.addAmazonError("' . _p('No buckets found. Please go to your Amazon S3 Console to create a bucket.') . '")');
            } else {
                $jsBucketsArray = "['" . implode("','", $buckets) . "']";
                $this->call('$Core.BackupRestore.addListBuckets(' . $jsBucketsArray . ')');
            }
        } elseif (is_string($buckets)) {
            // authorize false
            $buckets = explode(':', $buckets);
            $buckets = $buckets[count($buckets) - 1];
            $this->call('$Core.BackupRestore.addAmazonError("' . $buckets . '")');
        }
    }
}