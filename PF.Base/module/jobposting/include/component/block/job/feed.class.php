<?php
/**
 * Created by IntelliJ IDEA.
 * User: thanhnc
 * Date: 24/01/2018
 * Time: 14:07
 */

class Jobposting_Component_Block_Job_Feed extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($iFeedId = $this->getParam('this_feed_id')) {
            $sParamKey = 'custom_param_jobposting_job_' . $iFeedId;
            $aAssign = $this->getParam($sParamKey);

            if (!empty($aAssign)) {
                $this->template()->assign($this->getParam($sParamKey));
            }

            $this->clearParam($sParamKey);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

        (($sPlugin = Phpfox_Plugin::get('jobposting.component_block_job_feed_clean')) ? eval($sPlugin) : false);
    }
}