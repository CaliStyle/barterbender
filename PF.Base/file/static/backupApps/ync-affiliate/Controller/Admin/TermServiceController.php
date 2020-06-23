<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 15:17
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class TermServiceController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $aForms['title']   = setting('yncaffiliate.ynaf_term_of_service_title','');
        $aForms['content'] = setting('yncaffiliate.ynaf_term_of_service_content','');

        if ($aVals = $this->request()->get('val'))
        {
            if ($this->_validate($aVals))
            {
                $aTerm['ynaf_term_of_service_title']   = $aVals['title'];
                $aTerm['ynaf_term_of_service_content'] = $aVals['content'];

                if (Phpfox::getService('yncaffiliate.setting.process')->updateSetting($aTerm)) {
                    $this->url()->send('admincp.yncaffiliate.term-service', _p('Successfully update term of service'));
                }
            }
        }
        $this->template()->setTitle(_p('Term of Service'))
            ->setBreadCrumb(_p('Term of Service'))
            ->assign([
                'aForms' => $aForms,
            ]);


    }

    public function _validate($aVals)
    {
        if (empty($aVals['title'])) {
            Phpfox_Error::set(_p('Title is required'));
        }

        if (empty($aVals['content'])) {
            Phpfox_Error::set(_p('Content is required'));
        }

        return Phpfox_Error::isPassed();
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncaffiliate.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}