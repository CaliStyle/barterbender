<?php
/**
 * User: huydnt
 * Date: 16/01/2017
 * Time: 16:06
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Phpfox;

class AdminAddScheduleController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        // services
        $oDestinationService = Phpfox::getService('ynbackuprestore.destination');
        $oScheduleService = Phpfox::getService('ynbackuprestore.schedule');
        $oBackupService = Phpfox::getService('ynbackuprestore.backup');
        // destinations
        $aDestinations = $oDestinationService->getDestinations();
        if (!count($aDestinations)) {
            \Phpfox_Error::display(_p('You don\'t have any destination to do this action. Please go to <a href=\'{url}\'><b>Add New Destinations</b></a> to add some.',
                ['url' => \Phpfox_Url::instance()->makeUrl('admincp.ynbackuprestore.add-destination')]));
        }
        // things to backup
        $aModules = $oBackupService->getModules();
        $aApps = $oBackupService->getApps();
        $aThemes = $oBackupService->getThemes();
        $aUploads = $oBackupService->getUploadFolders();
        $aDatabase = $oBackupService->getDatabaseTables();
        // special destination type
        $aEmailsDest = array();
        $aDatabaseDest = array();
        foreach ($aDestinations as $aDestination) {
            if ($aDestination['type_id'] == 2) {
                $aEmailsDest[] = $aDestination['destination_id'];
            }
            if ($aDestination['type_id'] == 5) {
                $aDatabaseDest[] = $aDestination['destination_id'];
            }
        }
        $sEmailsDest = null;
        if (count($aEmailsDest)) {
            $sEmailsDest = '"' . implode('","', $aEmailsDest) . '"';
        }
        $sDatabaseDest = null;
        if (count($aDatabaseDest)) {
            $sDatabaseDest = '"' . implode('","', $aDatabaseDest) . '"';
        }
        if ($iId = $this->request()->get('schedule_id')) {
            $aSchedule = $oScheduleService->getSchedules(['schedule_id' => $iId]);
            $aScheduleApps = json_decode($aSchedule['plugins_included'], true);
            $aScheduleThemes = json_decode($aSchedule['themes_included'], true);
            $aScheduleUploads = json_decode($aSchedule['uploads_included'], true);
            $aScheduleDatabase = json_decode($aSchedule['database_included'], true);
            foreach ($aScheduleApps as &$aScheduleApp) {
                if (strpos($aScheduleApp, 'app') !== false) {
                    $aScheduleApp = substr($aScheduleApp, 4);
                } else {
                    $aScheduleApp = substr($aScheduleApp, 7);
                }
            }
            $aDestinationIds = $oDestinationService->getDestinationIdsByScheduleId($iId);
            $this->template()->assign([
                'aDestinationIds'   => $aDestinationIds,
                'aScheduleApps'     => $aScheduleApps,
                'aScheduleThemes'   => $aScheduleThemes,
                'aScheduleUploads'  => $aScheduleUploads,
                'aScheduleDatabase' => $aScheduleDatabase,
                'id'                => $iId,
                'aForms'            => $aSchedule
            ]);
        }
        $sOffline = _p('ynbackuprestore_change_to_offline',
            ['url' => $this->url()->makeUrl('admincp.setting.edit', ['group-id' => 'site_offline_online'])]);
        $this->template()
            ->setBreadCrumb(_p('Add new schedule'), $this->url()->makeUrl('admincp.ynbackuprestore.add-schedule'))
            ->assign([
                'sOffline'      => $sOffline,
                'aDestinations' => $aDestinations,
                'aModules'      => $aModules,
                'aApps'         => $aApps,
                'aThemes'       => $aThemes,
                'aUploads'      => $aUploads,
                'aDatabase'     => $aDatabase,
                'sEmailsDest'   => $sEmailsDest,
                'sDatabaseDest' => $sDatabaseDest,
                'sAssetsDir'    => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/yn_backuprestore/assets/',
            ]);

        // add schedule
        $oRequest = $this->request();
        if ($aVal = $oRequest->getArray('val')) {
            $bIsValid = true;
            if (!isset($aVal['destination_ids'])) {
                \Phpfox_Error::set(_p('* Backup Destination is required.'));
                $bIsValid = false;
            }
            $aPlugins = $oRequest->getArray('plugin');
            $aThemes = $oRequest->getArray('theme');
            $aUploads = $oRequest->getArray('upload');
            $aDatabase = $oRequest->getArray('database');

            // prefix is required
            if (!(isset($aVal['prefix']) && $aVal['prefix'])) {
                $bIsValid = false;
            }
            $aVal['prefix'] = preg_replace('/[^A-Za-z0-9_.-]/', '_', $aVal['prefix']);
            // check include
            if (!count($aPlugins) && !count($aThemes) && !count($aUploads) && !count($aDatabase)) {
                $bIsValid = false;
            }

            // if params is valid, save backup
            if ($bIsValid) {
                $aVal['maintenance_mode'] = ($aVal['maintenance_mode'] == 'yes') ? 1 : 0;
                if ($oRequest->get('schedule_id')) {
                    if ($oScheduleService->updateSchedule($iId, $aVal, array_keys($aPlugins), array_keys($aThemes),
                        array_keys($aUploads), array_keys($aDatabase))
                    ) {
                        $this->url()->send('admincp.ynbackuprestore.manage-schedule');
                    }
                } else {
                    if ($iId = $oScheduleService->addSchedule($aVal, array_keys($aPlugins), array_keys($aThemes),
                        array_keys($aUploads), array_keys($aDatabase))
                    ) {
                        $this->url()->send('admincp.ynbackuprestore.manage-schedule');
                    }
                }
            }
        }
    }
}