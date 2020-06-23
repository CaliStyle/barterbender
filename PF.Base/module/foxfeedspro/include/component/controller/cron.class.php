<?php

/*
 * @developer       [NTMD]
 * @copyright       [YouNet Copyright]
 * @author          [YouNet Company]
 * @package         [Module Name]
 * @version         [1.0]
 */

class FoxFeedsPro_Component_Controller_Cron extends Phpfox_Component
{
    public function process()
    {
        $this->template()->setBreadcrumb('Cron')->setTitle('Cron');
        phpfox::getService('foxfeedspro.cron')->approved();
    }
}
?>
