<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Admincp_Email extends Phpfox_Component
{

        /**
         * Class process method which is used to execute this component.
         */
        public function process()
        {
                $iTypeEmailId = 1;
                if ($this->request()->getArray('val'))
                {
                        $aVals = $this->request()->getArray('val');

                        if ($aVals['email_template_id'] != 0)
                        {
                                $iTypeEmailId = $aVals['email_template_id'];
                                Phpfox::getService('ecommerce.mail.process')->addEmailTemplate($aVals);
                        }
                }

                $aValidation = array(

                );

                $oValidator = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));

                $aTypes = Phpfox::getService('ecommerce.mail')->getAllEmailTypes();
                $aLanguages = Phpfox::getService('language')->getAll();

                $this->template()->assign(array(
                                'aTypes' => $aTypes,
                                'aLanguages' => $aLanguages,
                                'iCurrentTypeId' => $iTypeEmailId,
                                'sCreateJs' => $oValidator->createJS(),
                                'sGetJsForm' => $oValidator->getJsForm(),
                        ))
                        ->setTitle(_p('email_templates'))
                        ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                        ->setBreadCrumb(_p('auction'), $this->url()->makeUrl('admincp.app',['id' => '__module_auction']))
                        ->setBreadCrumb(_p('email_templates'), null, true)
                        ->setEditor();
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {

        }

}

?>