<?php
namespace Apps\P_AdvEvent\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class ManageEventsController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aVals = $this->request()->getArray('val');
        if (isset($aVals['delete']) && $aDeleteIds = $this->request()->getArray('id')) {
            if (Phpfox::getService('fevent.process')->actionMultiple($aDeleteIds, 'delete')) {
                $this->url()->send('admincp.fevent.manageevents',_p('event_s_successfully_deleted'));
            }
        }
        if (isset($aVals['feature']) && $aDeleteIds = $this->request()->getArray('id')) {
            if (Phpfox::getService('fevent.process')->actionMultiple($aDeleteIds, 'feature')) {
                $this->url()->send('admincp.fevent.manageevents', _p('event_s_successfully_featured'));
            }
        }
        if (isset($aVals['un_feature']) && $aDeleteIds = $this->request()->getArray('id')) {
            if (Phpfox::getService('fevent.process')->actionMultiple($aDeleteIds, 'un-feature')) {
                $this->url()->send('admincp.fevent.manageevents', _p('event_s_successfully_unfeatured'));
            }
        }
        if (isset($aVals['sponsor']) && $aDeleteIds = $this->request()->getArray('id')) {
            if (Phpfox::getService('fevent.process')->actionMultiple($aDeleteIds, 'sponsor')) {
                $this->url()->send('admincp.fevent.manageevents',_p('event_s_successfully_sponsored'));
            }
        }
        if (isset($aVals['un_sponsor']) && $aDeleteIds = $this->request()->getArray('id')) {
            if (Phpfox::getService('fevent.process')->actionMultiple($aDeleteIds, 'un-sponsor')) {
                $this->url()->send('admincp.fevent.manageevents',_p('event_s_successfully_unsponsored'));
            }
        }
        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');
        $iPageSize = 10;

        // Variables
        $aVals = array();
        $aConds = array();
        $aConds[] = "AND 1 =1 ";

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        $aVals['title'] = $oSearch->get('title');
        $aVals['owner'] = $oSearch->get('owner');
        $aVals['category_id'] = $oSearch->get('category_id');
        $aVals['status'] = $oSearch->get('status');
        $aVals['feature'] = $oSearch->get('feature');
        $aVals['sponsor'] = $oSearch->get('sponsor');
        $aVals['submit'] = $oSearch->get('submit');

        if ($aVals['title'] != '') {
            $aConds[] = "AND dbus.title like '%{$aVals['title']}%'";
        }
        if ($aVals['owner'] != '') {
            $aConds[] = "AND u.full_name like '%{$aVals['owner']}%'";
        }
        if ($aVals['category_id'] && $aVals['category_id'] != 0) {
            $aConds[] = "AND dc.category_id = {$aVals['category_id']}";
        }

        if ($aVals['status']) {
            switch ($aVals['status']) {
                case 'approved':
                    $aConds[] = "AND dbus.view_id = 0 ";
                    break;
                case 'pending':
                    $aConds[] = "AND dbus.view_id = 1 ";
                    break;
            }
        }

        if ($aVals['feature']) {
            switch ($aVals['feature']) {
                case 'featured':
                    $aConds[] = "AND dbus.is_featured = 1 ";
                    break;
                case 'not_featured':
                    $aConds[] = "AND dbus.is_featured = 0 ";
                    break;
            }
        }

        if ($aVals['sponsor']) {
            switch ($aVals['sponsor']) {
                case 'sponsor':
                    $aConds[] = "AND dbus.is_sponsor = 1 ";
                    break;
                case 'not_sponsor':
                    $aConds[] = "AND dbus.is_sponsor = 0 ";
                    break;
            }
        }

        list($iCount, $aList) = Phpfox::getService('fevent')->getManageEvent($aConds, $iPage, $iPageSize);

        // Set pager
        phpFox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCount
        ));

        $this->template()->setTitle(_p('fevent.manage_events'));

        $aCategories = Phpfox::getService('fevent.category')->getForBrowse(null);

        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_fevent"), $this->url()->makeUrl('admincp.app', ['id' => '__module_fevent']))
            ->setBreadcrumb(_p('fevent.manage_events'), $this->url()->makeUrl('admincp.fevent.manageevents'));

        $this->template()->assign(array(
            'aList' => $aList,
            'aCategories' => $aCategories,
            'aForms' => $aVals
        ));

        $this->template()->setHeader(array(
            'jscript/jquery.magnific-popup.js' => 'app_p-advevent',
            'jscript/manageevent.js' => 'app_p-advevent',
        ));

        $this->template()->setPhrase(array(
            'fevent.are_you_sure',
            'fevent.yes',
            'fevent.no',
        ));

    }
}