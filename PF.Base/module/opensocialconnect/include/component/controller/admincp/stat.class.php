<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        YOUNETCO
 * @author        AnNT
 * @package        YouNet SocialConnect
 * @version        3.03
 */
class OpenSocialConnect_Component_Controller_Admincp_Stat extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aStats = Phpfox::getService('opensocialconnect.providers')->getStatistics();

        #Count total sync as total login
        foreach ($aStats as $k => $aStat) {
            $aStats[$k]['total_login'] = $aStat['total_sync'] + $aStat['total_login'];
        }

        $this->template()->setTitle(_p('opensocialconnect.statistics'))
            ->setBreadcrumb('apps', $this->url()->makeUrl('admincp.apps'))
            ->setBreadcrumb('module_opensocialconnect', $this->url()->makeUrl('admincp.app', ['id' => '__module_opensocialconnect']))
            ->setBreadCrumb(_p('opensocialconnect.statistics'),
                $this->url()->makeUrl('admincp.opensocialconnect.stat'))
            ->assign(array(
                'aStats' => $aStats
            ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('opensocialconnect.component_controller_admincp_stat_clean')) ? eval($sPlugin) : false);
    }
}

?>