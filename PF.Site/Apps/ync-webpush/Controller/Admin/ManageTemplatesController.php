<?php

namespace Apps\YNC_WebPush\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class ManageTemplatesController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);

        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('yncwebpush.template.process')->deleteTemplate($iDelete)) {
                $this->url()->send('admincp.yncwebpush.manage-templates',
                    _p('notification_template_deleted_successfully'));
            }
        }
        $sCond = '1 = 1';
        $iLimit = 10;
        $aSearch = $this->request()->getArray('search');
        $bIsSearch = false;
        if (!empty($aSearch['title'])) {
            $bIsSearch = true;
            $sCond .= ' AND t.template_name LIKE \'%' . $aSearch['title'] . '%\'';
        }
        $iPage = $this->request()->getInt('page');
        $aTemplates = Phpfox::getService('yncwebpush.template')->getForManage($sCond, $iLimit, $iPage, $iCount);
        $this->template()->setTitle(_p('manage_templates'))
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Web Push Notification'), $this->url()->makeUrl('admincp.app', ['id' => 'YNC_WebPush']))
            ->setBreadCrumb(_p('manage_templates'), $this->url()->makeUrl('admincp.yncwebpush.manage-templates'))
            ->setHeader([
                'jscript/admin.js' => 'app_ync-webpush',
                'css/admin.css' => 'app_ync-webpush',
            ]);
        Phpfox::getLib('pager')->set([
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $iCount,
        ]);
        $this->template()->assign([
            'aTemplates' => $aTemplates,
            'sCreateLink' => $this->url()->makeUrl('admincp.yncwebpush.add-template'),
            'aForms' => $aSearch,
            'bIsSearch' => $bIsSearch
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncwebpush.component_controller_admincp_manage_templates_clean')) ? eval($sPlugin) : false);
    }
}