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

class AdminManageScheduleController extends Admincp_Component_Controller_App_Index
{
    const DEFAULT_ITEMS_PER_PAGE = 8;

    public function process()
    {
        parent::process();
        $oScheduleService = Phpfox::getService('ynbackuprestore.schedule');
        $oDestinationService = Phpfox::getService('ynbackuprestore.destination');
        $iPage = $this->request()->getInt('page', 1);
        $aVal = $this->request()->getArray('val', array());
        $iItemPerPage = setting('ynbackuprestore_item_per_page', self::DEFAULT_ITEMS_PER_PAGE);
        if (!$iItemPerPage) {
            $iItemPerPage = self::DEFAULT_ITEMS_PER_PAGE;
        }
        Phpfox_Pager::instance()->set([
            'page'  => $iPage,
            'size'  => $iItemPerPage,
            'count' => $oScheduleService->getQuantity($aVal)
        ]);
        $aSchedules = $oScheduleService->getSchedules($aVal, $iPage,
            setting('ynbackuprestore_item_per_page', self::DEFAULT_ITEMS_PER_PAGE));
        // Get included string
        foreach ($aSchedules as &$aSchedule) {
            $aIncluded = array();
            $startDate = new \DateTime($aSchedule['start_date']);
            $aSchedule['start_date'] = date('d-m-Y H:i:s', $startDate->getTimestamp());
            if (count(json_decode($aSchedule['plugins_included'], true))) {
                $aIncluded[] = 'Plugins';
            }
            if (count(json_decode($aSchedule['themes_included'], true))) {
                $aIncluded[] = 'Themes';
            }
            if (count(json_decode($aSchedule['uploads_included'], true))) {
                $aIncluded[] = 'Upload Folders';
            }
            if (count(json_decode($aSchedule['database_included'], true))) {
                $aIncluded[] = 'Database Tables';
            }
            $aSchedule['sIncluded'] = implode(' | ', $aIncluded);
        }
        foreach ($aSchedules as &$aSchedule) {
            $destinations = $oDestinationService->getDestinationsByScheduleId($aSchedule['schedule_id']);
            $tmp = array();
            foreach ($destinations as $destination) {
                if (isset($destination['title']) && $destination['title']) {
                    $tmp[] = $destination['title'];
                }
            }
            if (count($tmp)) {
                $aSchedule['destinations'] = implode(', ', $tmp);
            } else {
                $aSchedule['destinations'] = _p('ynbackuprestore_no_destination_found');
            }
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
            ->setBreadCrumb(_p('Manage Schedules'), $this->url()->makeUrl('admincp.ynbackuprestore.manage-schedule'))
            ->assign([
                'aSchedules' => $aSchedules,
                'aForms'     => $aVal,
                'sAssetsDir' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/yn_backuprestore/assets/',
            ]);
    }
}