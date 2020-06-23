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
class FAQController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $this->template()->setTitle(_p('faqs'))
            ->setBreadCrumb(_p('Affiliate'),$this->url()->makeUrl('affiliate'))
            ->setBreadCrumb(_p('faqs'),$this->url()->makeUrl('affiliate.faqs'));
        $iPage = $this->request()->getInt('page',1);
        $iLimit = 10;
        list($iCount,$aFaqs) = Phpfox::getService('yncaffiliate.faq.faq')->getByPage($iPage,$iLimit);
        $this->template()->assign([
            'aFaqs' => $aFaqs,
            'iPage' => $iPage
        ]);
        \Phpfox_Pager::instance()->set([
            'page'  => $iPage,
            'size'  => $iLimit,
            'count' => $iCount,
        ]);
    }
}