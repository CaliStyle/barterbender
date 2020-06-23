<?php
/**
 * User: huydnt
 * Date: 13/01/2017
 * Time: 16:59
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Pager;

class AdminManageBackupController extends Admincp_Component_Controller_App_Index
{
    const DEFAULT_ITEMS_PER_PAGE = 8;
    public function process()
    {
        parent::process();
        $oBackupService = Phpfox::getService('ynbackuprestore.backup');
        $oScheduleService = Phpfox::getService('ynbackuprestore.schedule');
        $last = $oBackupService->getLastBackup();
        if (isset($last) && $last) {
            $this->template()->assign('aLastBackup', $last);
        }
        $next = $oScheduleService->getNextSchedule();
        if (isset($next) && $next) {
            $this->template()->assign('aNextSchedule', $next);
        }
        $iItemPerPage = setting('ynbackuprestore_item_per_page', self::DEFAULT_ITEMS_PER_PAGE);
        if (!$iItemPerPage) {
            $iItemPerPage = self::DEFAULT_ITEMS_PER_PAGE;
        }
        $iPage = $this->request()->getInt('page', 1);
        $aVal = $this->request()->getArray('val', array());
        Phpfox_Pager::instance()->set([
            'page'  => $iPage,
            'size'  => $iItemPerPage,
            'count' => $oBackupService->getQuantity($aVal)
        ]);
        $aBackups = $oBackupService->getBackups($aVal, $iPage, setting('ynbackuprestore_item_per_page', self::DEFAULT_ITEMS_PER_PAGE));
        // Get included string
        foreach ($aBackups as &$aBackup) {
            $aIncluded = array();
            if (count(json_decode($aBackup['plugins_included'], true))) {
                $aIncluded[] = 'Plugins';
            }
            if (count(json_decode($aBackup['themes_included'], true))) {
                $aIncluded[] = 'Themes';
            }
            if (count(json_decode($aBackup['uploads_included'], true))) {
                $aIncluded[] = 'Upload Folders';
            }
            if (count(json_decode($aBackup['database_included'], true))) {
                $aIncluded[] = 'Database Tables';
            }
            $aBackup['sIncluded'] = implode(' | ', $aIncluded);
        }

        if (isset($aVal['backup_type']) && $aVal['backup_type']) {
            $this->template()->assign('backup_type', $aVal['backup_type']);
        }
        if (isset($aVal['plugins']) && $aVal['plugins']) {
            $this->template()->assign('plugins', 'on');
        }
        if (isset($aVal['themes']) && $aVal['themes']) {
            $this->template()->assign('themes', 'on');
        }
        if (isset($aVal['uploads']) && $aVal['uploads']) {
            $this->template()->assign('uploads', 'on');
        }
        if (isset($aVal['database']) && $aVal['database']) {
            $this->template()->assign('database', 'on');
        }
        $this->template()
            ->setBreadCrumb(_p('Manage Backups'), $this->url()->makeUrl('admincp.ynbackuprestore.manage-backup'))
            ->assign([
                'aBackups'   => $aBackups,
                'aForms'     => $aVal,
                'sAssetsDir' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/yn_backuprestore/assets/',
            ]);
    }
}