<?php
namespace Apps\P_AdvEvent\Controller;

use Phpfox;

class Unsubscribe extends \Phpfox_Component
{
    public function process()
    {
        $code = $this->request()->get('code');
        if(($subscribeId = Phpfox::getService('fevent')->getSubscriberByCode($code)) && Phpfox::getService('fevent.process')->unsubscribe($subscribeId)) {
            $this->template()->assign([
                'isSuccess' => true
            ]);
        }
        else {
            \Phpfox_Error::set(_p('you_can_unsubscribe_with_invalid_code'));
        }
    }
}