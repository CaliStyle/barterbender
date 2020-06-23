<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 18:43
 */

namespace Apps\YNC_Affiliate\Controller\Admin;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddFAQController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();

        $bIsEdit = false;

        if (($iEditId = $this->request()->getInt('idFaq'))) {
            $aRow = Phpfox::getService('yncaffiliate.faq.faq')->getFaqById($iEditId);
            $bIsEdit = true;
            $this->template()->assign([
                    'aForms'  => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($this->_validate($aVals))
            {
                if ($bIsEdit) {
                    if (Phpfox::getService('yncaffiliate.faq.process')->updateFaq($iEditId, $aVals)) {
                        $this->url()->send('admincp.yncaffiliate.manage-faq', _p('Successfully updated the FAQ'));
                    }
                } else {
                    if (Phpfox::getService('yncaffiliate.faq.process')->addFaq($aVals)) {
                        $this->url()->send('admincp.yncaffiliate.manage-faq', _p('Successfully created a new FAQ'));
                    }
                }
            }
        }

        if ($bIsEdit)
        {
            $this->template()->setTitle(_p('edit_faqs'))
                ->setBreadCrumb(_p('edit_faqs'));
        }
        else
        {
            $this->template()->setTitle(_p('add_faqs'))
                ->setBreadCrumb(_p('add_faqs'));
        }

        $this->template()->assign([
                'bIsEdit' => $bIsEdit,
            ]
        );
    }

    public function _validate($aVals)
    {
        $bIsFail = false;
        if (empty($aVals['question'])) {
            Phpfox_Error::set(_p('question_is_required'));
            $bIsFail = true;
        }

        if (empty($aVals['answer'])) {
            Phpfox_Error::set(_p('answer_is_required'));
            $bIsFail = true;
        }
        if($bIsFail)
        {
            $this->template()->assign([
                'aForms'  => $aVals,
                ]);
        }
        return Phpfox_Error::isPassed();
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynafiliate.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}