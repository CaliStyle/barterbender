<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:26
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
class LinksController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $iIsAffiliate =  Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate(Phpfox::getUserId());
        if($iIsAffiliate != 'approved' && $iIsAffiliate != 'inactive')
        {
            $this->url()->send('affiliate',_p('you_do_not_have_permission_to_view_this_page'));
        }
        $this->template()->setTitle(_p('links'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('affiliate'))
            ->setBreadCrumb(_p('links'),$this->url()->makeUrl('affiliate.links'));
        $aSuggestLinks = Phpfox::getService('yncaffiliate.link')->getSuggestLinks();
        $this->template()->setHeader([
            'jscript/clipboard.min.js' => 'app_ync-affiliate'
        ]);
        $this->template()->assign([
            'aSuggestLinks' => $aSuggestLinks,
            'corePath' => Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-affiliate'
        ]);
    }
}