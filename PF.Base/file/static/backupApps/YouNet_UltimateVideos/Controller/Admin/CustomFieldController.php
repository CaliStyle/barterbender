<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YouNet_UltimateVideos\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class CustomFieldController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bOrderUpdated = false;

        if (($this->request()->get('req5') && $this->request()->get('req4') == 'delete') && Phpfox::getService('ultimatevideo.custom.group')->deleteGroup($this->request()->get('req5'))) {
            $this->url()->send('admincp.ultimatevideo.customfield', [], _p('custom_fields_successfully_deleted'));
        }

        if (($aFieldOrders = $this->request()->getArray('field')) && Phpfox::getService('ultimatevideo.custom.process')->updateOrder($aFieldOrders)) {
            $bOrderUpdated = true;
        }

        if (($aGroupOrders = $this->request()->getArray('group')) && Phpfox::getService('ultimatevideo.custom.group')->updateOrder($aGroupOrders)) {
            $bOrderUpdated = true;
        }

        if ($bOrderUpdated === true) {
            $this->url()->send('admincp.ultimatevideo.customfield', [], _p('custom_fields_successfully_updated'));
        }

        $aGroups = Phpfox::getService('ultimatevideo.custom.group')->getForListing();
        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
        $this->template()->setTitle(_p('manage_custom_field_groups'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("ultimate_videos"), Phpfox::getLib('url')->makeUrl('admincp.app', ['id' => 'YouNet_UltimateVideos']))
            ->setBreadcrumb(_p('manage_custom_field_groups'))
            ->setHeader(array(
                    '<script type="text/javascript">$Behavior.custom_set_url = function() { $Core.customgroup.url(\'' . $this->url()->makeUrl('admincp.ultimatevideo.customfield') . '\'); };</script>',
                    'jquery/ui.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.custom_admin_addSort = function(){$Core.customgroup.addSort();};</script>'
                )
            )
            ->assign(array(
                    'aGroups' => $aGroups,
                    'corePath' => $corePath,
                    'sUrl' => $this->url()->makeUrl('admincp.ultimatevideo.customfield'),
                    'urlModule' => Phpfox::getParam('core.url_module'),
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('custom.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}
