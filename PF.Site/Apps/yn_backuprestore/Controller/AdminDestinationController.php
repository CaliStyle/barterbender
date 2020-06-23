<?php
/**
 * User: huydnt
 * Date: 04/01/2017
 * Time: 16:43
 */

namespace Apps\yn_backuprestore\Controller;


use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Pager;

class AdminDestinationController extends Admincp_Component_Controller_App_Index
{
    const DEFAULT_ITEMS_PER_PAGE = 8;

    public function process()
    {
        parent::process();
        if (isset($_SESSION)) {
            session_unset();
            session_destroy();
        }
        $iPage = $this->request()->getInt('page', 1);
        $oDestinationService = Phpfox::getService('ynbackuprestore.destination');
        $oTypeService = Phpfox::getService('ynbackuprestore.type');
        $aVal = $this->request()->getArray('val', array());
        $iItemPerPage = setting('ynbackuprestore_item_per_page', self::DEFAULT_ITEMS_PER_PAGE);
        if (!$iItemPerPage) {
            $iItemPerPage = self::DEFAULT_ITEMS_PER_PAGE;
        }
        Phpfox_Pager::instance()->set([
            'page'  => $iPage,
            'size'  => $iItemPerPage,
            'count' => $oDestinationService->getQuantity($aVal)
        ]);
        $aDestinations = $oDestinationService->getDestinations($aVal, $iPage,
            setting('ynbackuprestore_item_per_page', self::DEFAULT_ITEMS_PER_PAGE));
        $aTypes = $oTypeService->getAllTypes();
        if (isset($aVal['type_id']) && $aVal['type_id']) {
            $this->template()->assign('iTypeId', $aVal['type_id']);
        }

        $this->template()
            ->setBreadCrumb(_p('Manage Destinations'), $this->url()->makeUrl('admincp.ynbackuprestore.destination'))
            ->assign([
            'aForms'        => $aVal,
            'aDestinations' => $aDestinations,
            'aTypes'        => $aTypes,
            'sAssetsDir'    => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/yn_backuprestore/assets/',
        ]);
    }
}