<?php

namespace Apps\YNC_WebPush\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class AddTemplateController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);
        $bIsEdit = false;
        $aValidation = array(
            'template_name' => array(
                'def' => 'string:required',
                'title' => _p('provide_a_name_for_template')
            ),
            'title' => array(
                'def' => 'string:required',
                'title' => _p('provide_a_title_for_notification')
            ),
            'redirect_url' => array(
                'def' => 'string:required',
                'title' => _p('provide_a_redirect_url_for_notification')
            )
        );

        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ync_webpush_add_template',
                'aParams' => $aValidation
            )
        );
        if ($iId = $this->request()->getInt('id')) {
            $bIsEdit = true;
            $aTemplate = Phpfox::getService('yncwebpush.template')->getForEdit($iId);
            if (!$aTemplate) {
                Phpfox_Error::display(_p('error_template_you_are_looking_for_does_not_existed'));
            }
            $this->template()->assign([
                'aForms' => $aTemplate,
                'iEditId' => $iId
            ]);
        }
        if ($aVals = $this->request()->getArray('val')) {
            if ($oValid->isValid($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('yncwebpush.template.process')->add($aVals, true)) {
                        $this->url()->send('admincp.yncwebpush.manage-templates', _p('template_updated_successfully'));
                    }
                } elseif (Phpfox::getService('yncwebpush.template.process')->add($aVals)) {
                    $this->url()->send('admincp.yncwebpush.manage-templates', _p('template_added_successfully'));
                }
            }
        }
        $sTitle = $bIsEdit ? _p('edit_template') : _p('add_new_template');
        $this->template()->setTitle($sTitle)
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('Web Push Notification'), $this->url()->makeUrl('admincp.app', ['id' => 'YNC_WebPush']))
            ->setBreadCrumb($sTitle, $this->url()->makeUrl('admincp.yncwebpush.add-template'))
            ->setHeader([
                'jscript/admin.js' => 'app_ync-webpush'
            ]);

        $this->template()->assign([
            'sCreateJs' => $oValid->createJS(),
            'sGetJsForm' => $oValid->getJsForm(),
            'bIsEdit' => $bIsEdit,
            'sTitle' => $sTitle
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncwebpush.component_controller_add_template_clean')) ? eval($sPlugin) : false);
    }
}