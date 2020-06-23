<?php

namespace Apps\YNC_WebPush\Block\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class ComposeNotificationBlock extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);
        $aEditItem = $this->getParam('aEditItem', []);
        if (!$aEditItem) {
            $sType = $this->getParam('sType');
            $sTypeId = $this->getParam('sTypeId');
            $iTemplateId = $this->getParam('iTemplateId', 0);
            if (empty($sType)) {
                return false;
            }

            if ($iTemplateId) {
                $aTemplate = Phpfox::getService('yncwebpush.template')->getForEdit($iTemplateId);
                $aTemplate['schedule_month'] = date('m');
                $aTemplate['schedule_day'] = date('d');
                $aTemplate['schedule_year'] = date('Y');
                $this->template()->assign([
                    'aForms' => $aTemplate,
                ]);
            }
            $this->template()->assign([
                'sType' => $sType,
                'sTypeId' => $sTypeId,
                'aTemplates' => Phpfox::getService('yncwebpush.template')->getAllTemplates('t.template_id, t.template_name'),
                'iTemplateId' => $iTemplateId,
                'bIsEdit' => false
            ]);
        } else {
            $this->template()->assign([
                'aForms' => $aEditItem,
                'bIsEdit' => true
            ]);
        }
        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('yncwebpush.component_block_admincp_compose_notification_clean')) ? eval($sPlugin) : false);
    }
}