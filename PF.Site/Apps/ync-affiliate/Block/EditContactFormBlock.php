<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:32
 */

namespace Apps\YNC_Affiliate\Block;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
class EditContactFormBlock extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $iUserId = $this->getParam('user_id');
        $aDetail = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getDetail($iUserId);
        if(!count($aDetail))
        {
            return \Phpfox_Error::display(_p('can_not_find_contact_detail'));
        }
        $this->template()->assign([
            'aForms' => $aDetail,
        ]);
    }
}