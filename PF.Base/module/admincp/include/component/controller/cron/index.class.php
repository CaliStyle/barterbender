<?php
defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Cron_Index extends Phpfox_Component {
    public function process(){
        $cronUrl = str_replace("index.php/", "", Phpfox::getParam('core.path')) . 'cron.php?token=' . setting('pf_cron_task_token');
        $this->template()->setTitle(_p("Cron Job URL"))
            ->setActiveMenu('admincp.settings.cron')
            ->setBreadCrumb(_p("Cron Job URL"), $this->url()->makeUrl('admincp.cron'))
            ->assign([
               'cron_url' => $cronUrl
            ]);
    }
}