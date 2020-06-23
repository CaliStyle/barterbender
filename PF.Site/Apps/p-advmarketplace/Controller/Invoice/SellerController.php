<?php
namespace Apps\P_AdvMarketplace\Controller\Invoice;

use Phpfox_Component;
use Phpfox;

class SellerController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);

        $page = $this->request()->get('page');
        $size = 6;
        $search = $this->request()->get('val');

        if(!empty($search['name'])) {
            $this->search()->setCondition('AND (t.title LIKE "%'. $search['name'] .'%")');
        }

        $fromDay = $search['from_day'] = !empty($search['from_day']) ? $search['from_day'] : Phpfox::getTime('d');
        $fromMonth = $search['from_month'] = !empty($search['from_month']) ? $search['from_month'] : Phpfox::getTime('m');
        $fromYear = $search['from_year'] = !empty($search['from_year']) ? $search['from_year'] : Phpfox::getTime('Y');
        $toDay = $search['to_day'] = !empty($search['to_day']) ? $search['to_day'] : Phpfox::getTime('d');
        $toMonth = $search['to_month'] = !empty($search['to_month']) ? $search['to_month'] : Phpfox::getTime('m');
        $toYear = $search['to_year'] = !empty($search['to_year']) ? $search['to_year'] : Phpfox::getTime('Y');

        $this->search()->setCondition('AND (mi.time_stamp BETWEEN '. Phpfox::getLib('date')->mktime(0, 0 , 0, $fromMonth, $fromDay, $fromYear) .' AND ' .Phpfox::getLib('date')->mktime(23, 59 , 59, $toMonth, $toDay, $toYear) .')');

        list($iCnt, $aInvoices) = Phpfox::getService('advancedmarketplace.invoice')->getInvoicesForSeller(Phpfox::getUserId(), $this->search()->getConditions(), $page, $size);
        $aSectionMenu = Phpfox::getService('advancedmarketplace')->getSectionMenu();

        \Phpfox_Pager::instance()->set([
            'page' => $page,
            'size' => $size,
            'count' => $iCnt,
            'paging_mode' => 'pagination',
            'params' => [
                'pagination_show_first_last' => true
            ]
        ]);

        $this->template()->setTitle(_p('advancedmarketplace_seller_management'))
            ->setBreadcrumb(_p('advancedmarketplace.advanced_advancedmarketplace'),
                $this->url()->makeUrl('advancedmarketplace'))
            ->setBreadcrumb(_p('advancedmarketplace_seller_management'), null, false)
            ->setHeader('cache', array(
                    'table.css' => 'style_css'
                )
            )
            ->assign(array(
                    'aInvoices' => $aInvoices,
                    'aForms' => $search
                )
            )
            ->buildSectionMenu('advancedmarketplace', $aSectionMenu);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('advancedmarketplace.component_controller_invoice_seller_clean')) ? eval($sPlugin) : false);
    }
}