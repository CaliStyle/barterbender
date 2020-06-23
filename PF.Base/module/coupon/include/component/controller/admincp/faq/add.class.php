<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 */
class Coupon_Component_Controller_Admincp_FAQ_Add extends Phpfox_Component
{

        /**
         * Class process method wnich is used to execute this component.
         */
        public function process()
        {
                $bIsEdit = false;
                if ($iEditId = $this->request()->getInt('id'))
                {
                        if ($aFaq = Phpfox::getService('coupon.faq')->getForEdit($iEditId))
                        {
                                $bIsEdit = true;

                                $this->template()->assign('aForms', $aFaq);
                        }
                }

                if ($aVals = $this->request()->getArray('val'))
                {
                        if ($bIsEdit)
                        {
                                if (Phpfox::getService('coupon.faq.process')->update($aFaq['faq_id'], $aVals))
                                {
                                        $this->url()->send('admincp.coupon.faq.add', array('id' => $aFaq['faq_id']), _p('faq_success_updated'));
                                }
                        } else
                        {
                                if (Phpfox::getService('coupon.faq.process')->add($aVals))
                                {
                                        $this->url()->send('admincp.coupon.faq.add', null, _p('faq_success_added'));
                                }
                        }
                }

                $aValidation = array(
                    'question' => array(
                        'title' => "Provide a question",
                        'def' => 'required'
                    ),
                );

                $oValidator = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

                $this->template()->setTitle(($bIsEdit ? _p('faq_edit_title') : _p('add_new_faq')))
                        ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                        ->setBreadCrumb(_p('module_coupon'), $this->url()->makeUrl('admincp.app').'?id=__module_coupon')
                        ->setBreadcrumb(($bIsEdit ? _p('faq_edit_title') : _p('add_new_faq')), $this->url()->makeUrl('admincp.coupon.faq.add'))
                        ->assign(array(
                            'sOptions' => Phpfox::getService('coupon.faq')->display('option')->get(),
                            'sCreateJs' => $oValidator->createJS(),
                            'sGetJsForm' => $oValidator->getJsForm(),
                            'bIsEdit' => $bIsEdit
                        ))
                        ->setEditor(array('wysiwyg' => true));
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_admincp_faq_add_clean')) ? eval($sPlugin) : false);
        }

}

?>