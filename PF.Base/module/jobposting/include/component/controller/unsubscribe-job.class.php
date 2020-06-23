<?php
defined('PHPFOX') or exit('NO DICE!');

class JobPosting_Component_Controller_Unsubscribe_Job extends Phpfox_Component
{
    public function process() {
        $subscribeCode = $this->request()->get('subscribe_code');
        $valid = false;
        if(!empty($subscribeCode)) {
            $valid = Phpfox::getService('jobposting.job.process')->unsubscribeJob($subscribeCode);
        }
        $message = $valid ? _p('unsubscribe_successfully') : _p('you_cannot_unsubscribe');
        $this->template()->assign([
           'message' => $message,
            'isValid' => $valid
        ]);
    }
}