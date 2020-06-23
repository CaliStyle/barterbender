<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:32
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
class CodesController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $this->template()->setTitle(_p('codes'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('affiliate'))
            ->setBreadCrumb(_p('codes'),$this->url()->makeUrl('affiliate.codes'));
        $iPageSize = 5;
        $iPage = $this->request()->get('page',1);
        list ($iCount,$aMaterials) = Phpfox::getService('yncaffiliate.materials')->getMaterialsInFrontend($iPage,$iPageSize);
        $this->template()->assign([
            'aMaterials' => $aMaterials,
            'iCnt'  => $iCount,
            'corePath' => Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-affiliate',
            'iPage' => $iPage
        ]);
        Phpfox::getLib('pager')->set([
            'page'  => $iPage,
            'size'  => $iPageSize,
            'count' => $iCount,
        ]);
    }
}