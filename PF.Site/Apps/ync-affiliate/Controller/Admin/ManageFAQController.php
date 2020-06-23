<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 14:41
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ManageFAQController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();

        $bIsSearch = false;
        $aConds = array();
        $aSearch = $this->request()->get('search');

        if ($aSearch) {
            $aVals['question'] = $aSearch['faq_question'];
            $bIsSearch = true;
            $this->template()->assign([
                    'sSearch' => $aSearch['faq_question']
                ]);
        } else {
            $aVals['question'] = '';
        }

        if ($aVals['question']) {
            $aConds[] = "faq.question like '%{$aVals['question']}%'";
        }

        $aItems = Phpfox::getService('yncaffiliate.faq.faq')->get($aConds);

        if (($iDelete = $this->request()->getInt('deleteFaq'))) {
            if (Phpfox::getService('yncaffiliate.faq.process')->deleteFaq($iDelete)) {
                $this->url()->send('admincp.yncaffiliate.manage-faq', _p('Successfully deleted the Faq.'));
            }
        }

        // assign variables to template
        $this->template()->assign([
                'aItems'       => $aItems,
                'bIsSearch'    => $bIsSearch,
            ]
        );
        $this->template()
            ->setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('admincp.app',['id' => 'YNC_Affiliate']))
            ->setBreadCrumb(_p('Manage FAQs'));
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