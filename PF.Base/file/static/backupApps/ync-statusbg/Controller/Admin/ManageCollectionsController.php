<?php
namespace Apps\YNC_StatusBg\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

/**
 * Class ManageCollectionsController
 * @package Apps\YNC_StatusBg\Controller\Admin
 */
class ManageCollectionsController extends Phpfox_Component
{

    public function process()
    {
        if ($iId = $this->request()->getInt('delete')) {
            if (Phpfox::getService('yncstatusbg.process')->deleteCollection($iId)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_StatusBg'], _p('collection_deleted_successfully'));
            }
        }
        if ($iId = $this->request()->getInt('default')) {
            if (Phpfox::getService('yncstatusbg.process')->setDefault($iId)) {
                $this->url()->send('admincp.app', ['id' => 'YNC_StatusBg'],
                    _p('collection_set_as_default_successfully'));
            }
        }
        if ($aIds = $this->request()->getArray('ids')) {
            foreach ($aIds as $iId) {
                Phpfox::getService('yncstatusbg.process')->deleteCollection($iId);
            }
            $this->url()->send('admincp.app', ['id' => 'YNC_StatusBg'], _p('collection_s_deleted_successfully'));
        }
        $iLimit = 10;
        $iPage = $this->request()->getInt('page', 1);
        $this->template()
            ->setTitle(_p('manage_collections'), true)
            ->setBreadCrumb(_p('manage_collections'))
            ->assign([
                'aCollections' => Phpfox::getService('yncstatusbg')->getForManage($iLimit, $iPage, $iCount),
            ]);
        Phpfox::getLib('pager')->set([
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $iCount,
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncstatusbg.component_controller_admincp_manage_collections_clean')) ? eval($sPlugin) : false);
    }
}

