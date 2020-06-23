<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * [PHPFOX_HEADER]
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Socialad
 * @version        3.01
 */
class Socialad_Component_Controller_Admincp_FAQ_Add extends Phpfox_Component
{

        /**
         * Class process method wnich is used to execute this component.
         */
        public function process()
        {
            $aValidation = array(
                'question' => array(
                    'def' => 'string:required',
                    'title' => _p('Provide a question'),
                ),
                'answer' => array(
                    'def' => 'string:required',
                    'title' => _p('Provide a answer'),
                ),
            );

            $oValidator = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

            $bIsEdit = false;
                if ($iEditId = $this->request()->getInt('id'))
                {
                        if ($aFaq = Phpfox::getService('socialad.faq')->getForEdit($iEditId))
                        {
                                $bIsEdit = true;

                                $this->template()->assign('aForms', $aFaq);
                        }
                }

                if ($aVals = $this->request()->getArray('val'))
                {
                    if ($oValidator->isValid($aVals)) {
                        if ($bIsEdit) {
                            if (Phpfox::getService('socialad.faq.process')->update($aFaq['faq_id'], $aVals)) {
                                $this->url()->send('admincp.socialad.faq.add', array('id' => $aFaq['faq_id']),
                                    _p('faq_success_updated'));
                            }
                        } else {
                            if (Phpfox::getService('socialad.faq.process')->add($aVals)) {
                                $this->url()->send('admincp.socialad.faq.add', null, _p('faq_success_added'));
                            }
                        }
                    }
                }


                $this->template()->setTitle(($bIsEdit ? _p('faq_edit_title') : _p('add_new_faq')))
                        ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                        ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
                        ->setBreadcrumb(($bIsEdit ? _p('faq_edit_title') : _p('add_new_faq')), $this->url()->makeUrl('admincp.socialad.faq.add'))
                        ->assign(array(
                            'sOptions' => Phpfox::getService('socialad.faq')->display('option')->get(),
                            'sCreateJs' => $oValidator->createJS(),
                            'sGetJsForm' => $oValidator->getJsForm(),
                            'bIsEdit' => $bIsEdit
                        ))
                        ->setEditor();
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('socialad.component_controller_admincp_faq_add_clean')) ? eval($sPlugin) : false);
        }

}

?>